<?php

namespace Apr\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use \Apr\ContractBundle\Entity\Party;
use \Apr\ContractBundle\Entity\Contract;
use Apr\ContractBundle\Entity\Collaborator;
use \Apr\MandataireBundle\Entity\Mandataire;
use \Apr\ProgramBundle\Entity\Member;

class CoopAEManager extends BaseManager
{

    protected $em;
    protected $container;

    private $rangeSettledPoints = 30000;
    private $rateSettledPoints = 0.01;

    public $rateFeeCooperons = 0.0275;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        // Nécessaire pour implémenter BaseManager - non utilisé
    }

    public function loadProgramCoopAE() {
        $programManager = $this->container->get('apr_program.program_manager');
        $idProgramAE = $this->container->getParameter('idProgramAE');
        return $programManager->loadProgramById($idProgramAE);
    }

    public function createParticipatesToAE(Member $member) {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        $programAE = $this->loadProgramCoopAE();
        return $participatesToManager->createParticipatesTo($programAE, $member, $member->getUser()->getId());
    }

    public function loadParticipatesToCoopAE($member) {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        return $participatesToManager->loadParticipatesToByMember($this->loadProgramCoopAE(), $member);
    }

    public function agreeCGVCoopAE($collaborator, &$listeEmails){
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        // Vérification que le client Corporate a signé le contrat cadre ...
        $corporate = $collaborator->getCorporate();
        if($corporate && !$corporate->isAccordSigned()) return;

        // Mandataire
        $cooperonsManager->createMandataireCooperons($corporate->getParty());

        $corporate->agreeCGVCoopAE();

        $this->persistAndFlush($corporate);

        array_push($listeEmails,$this->getEmailCGVCoopAEAgreed($collaborator));
    }

    public function collaboratorWithParticipatesAECanSponsor(Collaborator $collaborator, Contract $contract) {
        $invitation = $contract->getInvitation();
        if($invitation) {
            $filterContract = $invitation->getInfos();
        } else {
            $filterContract = $contract->getFilter($collaborator->getParty());
        }

        if($collaborator->getParty()->getAutoEntrepreneur()) {
            // les AE peuvent parrainer leurs CLIENTS - et leurs fournisseurs, distributeurs ...
            return  $filterContract!= 'default:client';
        } else {
            // les Collaborators peuvent parrainer leurs PRESTATAIRES - et leurs fournisseurs, distributeurs ...
            return $filterContract!= 'default:owner';
        }
    }

    public function createSponsorshipCoopAEFromContract(Contract $contract, $userMember, &$listeEmails) {
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');

        $invitation = $contract->getInvitation();
        if($invitation) {
            $sponsor = $this->loadParticipatesToCoopAE($invitation->getSponsorMember());
            if(!$sponsor or !$this->collaboratorWithParticipatesAECanSponsor($invitation->getCollaborator(), $contract)) return null;
            $affiliateMember = $userMember;
        } else {
            if($contract->isCreatedByOwner()) {
                $sponsor = $this->loadParticipatesToCoopAE($contract->getOwnerMember());
                if(!$sponsor or !$this->collaboratorWithParticipatesAECanSponsor($contract->getOwnerCollaborator(), $contract)) return null;
                $affiliateMember = $contract->getClientMember();
            } else {
                $sponsor = $this->loadParticipatesToCoopAE($contract->getClientMember());
                if(!$sponsor or !$this->collaboratorWithParticipatesAECanSponsor($contract->getClientCollaborator(), $contract)) return null;
                $affiliateMember = $contract->getOwnerMember();
            }
        }

        $participatesTo = $this->loadParticipatesToCoopAE($affiliateMember);
        if(!$participatesTo) $participatesTo = $this->createParticipatesToAE($affiliateMember);
        return $sponsorShipManager->createSponsorship($sponsor->getProgram(), $sponsor, $participatesTo, $listeEmails);
    }

    public function givePointsToSponsorCoopAE(Mandataire $mandataire, $totalAmount, &$listeEmails) {
        $historyPointsManager = $this->container->get('apr_program.account_points_history_manager');

        $contract = $mandataire->getContract();

        $recruitment = $contract->getRecruitment();
        if($recruitment) {
            $recruitmentContract = $recruitment->getRecruitmentContract();
            $ownerMember = $recruitmentContract->getOwnerMember();
            $strInfo = 'recrutement '.$recruitmentContract->getOwner()->getLabel();
        } else {
            $ownerMember = $contract->getOwnerMember();
            $strInfo = 'prestation '.$contract->getOwner()->getLabel();
        }

        $ownerParticipatesTo = $this->loadParticipatesToCoopAE($ownerMember);
        $sponsor = $ownerParticipatesTo->getSponsor();

        if($sponsor) {
            // Total du CA facturé par l'AE (indépendamment du Contract en cours ...)
            $totalAmount = $totalAmount - max($contract->getOwner()->getTotalIncomeHt() - $this->rangeSettledPoints, 0);

            if($totalAmount > 0) {
                $datas = array(
                    array(
                        'labelOperation' => 'Commission 1%',
                        'amount' => $ownerMember->convertToPoints($totalAmount * $this->rateSettledPoints),
                        'info' => $strInfo.' '.number_format($totalAmount, 2, ',', ' ').' €'
                    )
                );
                $historyPointsManager->addPoints($sponsor, $datas, $listeEmails);
            }
        }
    }

    public function convertPoints(Party $party, $settlement, $description, $result, &$listeEmails) {
        $avantageManager = $this->container->get('apr_program.avantage_manager');

        $autoEntrepreneur = $party->getAutoEntrepreneur();
        if($autoEntrepreneur) {
            $member = $autoEntrepreneur->getMember();
            $valuePoints = $member->getValuePoints();
            if($valuePoints >= $result['amountTtc']) {
                $avantageManager->addAvantageAE($member, $settlement, $description, $result, $listeEmails);
                return true;
            } else {
                return false;
            }
        }
    }

    public function createSettlementFeeMandataire(&$listeEmails) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $allSettlements = $settlementsManager->loadSettlementsForMandataireFee();

        $allParties = array();
        $party = null;
        foreach($allSettlements as $settlement) {
            $mandataire = $settlement->getMandataire();
            // allSettlements triés par Owner ...
            if($mandataire->getOwner() != $party) {
                $party = $mandataire->getOwner();
                $allParties[$party->getId()] = array(
                    'party' => $party,
                    'convertible' => array('totalAmount' => 0, 'settlements' => array()),
                    'nonConvertible' => array('totalAmount' => 0, 'settlements' => array()),
                );
            }
            $sign = ($settlement->getAmountHt() > 0)?'convertible':'nonConvertible';
            $allParties[$party->getId()][$sign]['totalAmount'] += $settlement->getAmountHt();
            array_push($allParties[$party->getId()][$sign]['settlements'], $settlement);
        }

        foreach($allParties as $key_party => $my) {
            $mandataire = $my['party']->getMandataire();
            foreach($my['convertible']['settlements'] as $key_settlement => $settlement) {
                if($allParties[$key_party]['nonConvertible']['totalAmount'] < 0) {
                    // Mise de côté des settlements (convertibles en points) nécessaires pour "éponger" les avoirs (non convertibles en points) ...
                    array_push($allParties[$key_party]['nonConvertible']['settlements'], $settlement);
                    $allParties[$key_party]['nonConvertible']['totalAmount'] += $settlement->getAmountHt();
                } else {
                    // Conversion en réduction de Frais de Gestion (si Points suffisants ...)
                    $amountFee = ceil($this->rateFeeCooperons * $settlement->getAmountHt() * 100)/100;
                    $result = $settlementsManager->calculateSettlement($mandataire, $amountFee);
                    $description = 'Commission Mandataire ('.sprintf("%.2f%%", $this->rateFeeCooperons * 100).' CA '.number_format($settlement->getAmountHt(), 2, ',', ' ').' €)';
                    if(!$this->convertPoints($my['party'], $settlement, $description, $result, $listeEmails)) {
                        array_push($allParties[$key_party]['nonConvertible']['settlements'], $settlement);
                        $allParties[$key_party]['nonConvertible']['totalAmount'] += $settlement->getAmountHt();
                    };
                }
            }
            unset($allParties[$key_party]['convertible']);
        }

        foreach($allParties as $my) {
            $mandataire = $my['party']->getMandataire();
            $amountFee = ceil($this->rateFeeCooperons * $my['nonConvertible']['totalAmount'] * 100)/100;
            $result = $settlementsManager->calculateSettlement($mandataire, $amountFee);

            $description = 'Commission Mandataire ('.sprintf("%.2f%%", $this->rateFeeCooperons * 100).' CA '.number_format($my['nonConvertible']['totalAmount'], 2, ',', ' ').' €)';
            $settlementFee = $settlementsManager->createSettlement($mandataire, $description, $result, 'fee');
            $this->persist($settlementFee);

            foreach($my['nonConvertible']['settlements'] as $settlement) {
                $settlement->setSettlementFee($settlementFee);
                $this->persist($settlementFee);
            }

            $this->flush();

            $settlementsManager->updateWaitingSettlements($mandataire, $listeEmails);
        }
    }

    public function createSettlementFeeCoopAE(Mandataire $mandataire, $totalAmount, &$listeEmails) {
        // Base Frais Coopérons = prestations service (pas recrutement ...)
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $contract = $mandataire->getContract();
        $corporate = $contract->getClient()->getCorporate();

        if(!$cooperonsManager->isCorporateCooperons($corporate)) {
            $corporateMandataire = $corporate->getMandataire();
            $amountFee = ceil($this->rateFeeCooperons * $totalAmount * 100)/100;
            $result = $settlementsManager->calculateSettlement($corporateMandataire, $amountFee);

            $transfer = $mandataireManager->createTransfer($mandataire, $corporateMandataire, $result['amountTtc'], $listeEmails);
            if($transfer) {
                //$this->persistAndFlush($transfer);

                $description = 'Garantie Sociale ('.sprintf("%.2f%%", $this->rateFeeCooperons * 100).' CA '.$contract->getOwner()->getLabel()
                    .' '.number_format($totalAmount, 2, ',', ' ').' €)';
                $settlement = $settlementsManager->createSettlement($corporateMandataire, $description, $result, 'fee');
                $this->persistAndFlush($settlement);

                $settlementsManager->updateWaitingSettlements($corporateMandataire, $listeEmails);
            }
        }
    }

    public function getEmailCGVCoopAEAgreed($collaborator){
        if(!is_null($collaborator)) {
            $member = $collaborator->getMember();

            if(!is_null($member)) {
                $corporate = $collaborator->getCorporate();
                $subject = 'Ouverture du compte Coopérons AE de '.$corporate->getRaisonSocial();
                return array(
                    'to' => $member->getEmail(),
                    'subject' => $subject,
                    'body' => array(
                        'template' => 'AprAdminBundle:Emails/CoopAE:agreedCGVCoopAE.html.twig',
                        'parameter' => array(
                            'member' => $member,
                            'corporate' => $corporate,
                        )),
                );

            }
        }
    }
}