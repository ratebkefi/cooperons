<?php

namespace Apr\MandataireBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ContractBundle\Entity\LegalDocument;
use Apr\MandataireBundle\Entity\Mandataire;
use Apr\MandataireBundle\Entity\Transfer;

class MandataireManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprMandataireBundle:Mandataire');
    }

    public function loadMandataireById($id)
    {
        return $this->getRepository()->find($id);
    }

    public function getInfosMandataireAllCollaborators($member)
    {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $params = array(
            'corporate' => null,
            'contract' => null,
            'settlements' => null,
        );

        $collaborators = $member->getAllCollaborators();

        foreach ($collaborators as $collaborator) {

            // Contrat Cadre ...

            $corporate = $collaborator->getCorporate();
            if (!$collaborator->getParty()->isCooperons() && $collaborator->getParty()->getAdministrator() == $collaborator && !$corporate->isAccordSigned()) {
                $params['corporate'] = $corporate;
            }

            // Contracts

            $my_allContracts = $collaborator->getAllContractsAsClient();
            if (!is_null($my_allContracts)) {
                foreach ($my_allContracts as $contract) {
                    if ($contract->isActive()) {
                        $mandataire = $contract->getMandataire();
                        // Rappel: mandataire = null pour contracts Coopérons <-> Coopérons
                        if (!is_null($mandataire)) {
                            $waitingSettlements = $settlementsManager->loadWaitingSettlements($mandataire);
                            if (($mandataire->getDepot() < $mandataire->getMinDepot()) or $waitingSettlements) {
                                $params['contract'] = $contract;
                                $params['settlements'] = $waitingSettlements;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $params;

    }

    public function createMandataire(LegalDocument $legalDocument, $ownerLabel, $clientLabel,
                                     $ownerAccountRef, $clientAccountRef, $ownerIncomeRef, $clientIncomeRef) {
        $contract = $legalDocument->getContract();
        $mandataire = new Mandataire($legalDocument, $contract->getOwner(), $contract->getClient(), $ownerLabel, $clientLabel,
            $ownerAccountRef, $clientAccountRef, $ownerIncomeRef, $clientIncomeRef);
        return $mandataire;
    }

    public function createTransfer(Mandataire $debited, Mandataire $credited, $amount = 0, &$listeEmails)
    {
        $recordManager = $this->container->get('apr_mandataire.record_manager');
        $transferRecord = $recordManager->recordTransfer($debited, $credited, $amount);

        if ($transferRecord) {
            $transfer = new Transfer($debited, $credited, $amount);
            $transfer->setRecord($transferRecord);

            $this->persist($transfer);
            // flush géré plus tard

            array_push($listeEmails, $this->getEmailConfirmationTransfer($debited, $credited, $amount));
            return $transfer;
        }
    }

    public function updateMinDepot(Mandataire $mandataire, $minDepot, &$listeEmails)
    {

        $mandataire->setMinDepot($minDepot);
        $this->persistAndFlush($mandataire);

        if ($mandataire->getContract() && $mandataire->getContract()->getStatus() == 'active') {
            array_push($listeEmails, $this->getEmailModificationDepot($mandataire));
        }
    }

    public function liquidation(Mandataire $mandataire, &$listeEmails)
    {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $paymentsManager = $this->container->get('apr_mandataire.payments_manager');

        foreach ($paymentsManager->loadPaymentsByStatus($mandataire) as $payment) {
            $paymentsManager->cancelPayment($payment);
        }
        foreach ($settlementsManager->loadWaitingSettlements($mandataire) as $settlement) {
            $settlementsManager->cancelSettlement($mandataire, $settlement, $listeEmails);
        }

        $mandataire->liquidate();
        $this->persistAndFlush($mandataire);

        $paymentsManager->createPayment($mandataire);
    }

    public function prepareEmailMandataire(Mandataire $mandataire, $extraMsg = '', $ccOwner = true)
    {
        $member = $mandataire->getClientMember();
        $mailParam = array();
        $mailParam['to'] = $member->getEmail();
        if ($ccOwner) $mailParam['cc'] = array($mandataire->getOwnerMember()->getEmail());
        $mailParam['body']['parameter'] = array(
            'member' => $member,
            'mandataire' => $mandataire,
            'minDepot' => $mandataire->getMinDepot(),
            'depot' => $mandataire->getDepot(),
            'extraMsg' => $extraMsg
        );
        return $mailParam;
    }

    public function getEmailConfirmationTransfer(Mandataire $debited, Mandataire $credited, $amount)
    {
        $mailParam = $this->prepareEmailMandataire($credited, null, false);
        $mailParam['body']['parameter']['debitedMandataire'] = $debited;
        $mailParam['body']['parameter']['debitedDepot'] = $debited->getDepot();
        $mailParam['body']['parameter']['amount'] = $amount;
        $mailParam['subject'] = "Le transfert de " . number_format($amount, 2, '.', ' ') . " € sur votre " . $credited->getShortLabel() . " a été effectué";
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationTransfer.html.twig';
        return $mailParam;
    }

    public function getEmailModificationDepot(Mandataire $mandataire, $extraMsg = '')
    {
        $mailParam = $this->prepareEmailMandataire($mandataire, $extraMsg, false);
        $mailParam['subject'] = "Modification de votre Dépôt Minimum: " . number_format($mandataire->getMinDepot(), 2, '.', ' ') . ' €';
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:modificationMinDepot.html.twig';
        return $mailParam;
    }

    /**
     * Check if user can access to mandataire
     *
     * @author Fondative <devteam@fondative.com>
     * @param $user
     * @param $mandataire
     * @param $isOwner
     * @param $isClient
     * @throws ApiException
     * @return boolean
     */
    public function securityCheck($user, $mandataire, $isOwner = false, $isClient = false)
    {
        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        $member = $user->getMember();
        if ($isOwner) {
            $notAuthorized = !$mandataire->getOwner()->isAuthorized($member);
        } else if ($isClient) {
            $notAuthorized = !$mandataire->getClient()->isAuthorized($member);
        } else {
            $contract = $mandataire->getContract();
            if ($contract) {
                $party = $contract->getAuthorizedParty($member);
                $notAuthorized = is_null($party);
            } else {
                $party = $mandataire->getClient();
                $notAuthorized = (is_null($party) or !$party->isAuthorized($member));
            }
        }
        if ($notAuthorized) {
            throw new ApiException(40310);
        } else {
            return true;
        }
    }
}
