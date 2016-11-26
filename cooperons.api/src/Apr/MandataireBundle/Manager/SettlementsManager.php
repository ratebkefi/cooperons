<?php

namespace Apr\MandataireBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\MandataireBundle\Entity\Mandataire;
use Apr\MandataireBundle\Entity\Settlement;

class SettlementsManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        return $this->em->getRepository('AprMandataireBundle:Settlement');
    }

    public function loadSettlements($mandataire = null, $status = null, $isProgram = false){
        return $this->getRepository()->getFilteredSettlements($mandataire, $status, $isProgram);
    }

    public function loadSettlementById($id) {
        return $this->getRepository()->find($id);
    }

    public function loadWaitingSettlements($mandataire){
        return $this->loadSettlements($mandataire, "waiting");
    }

    public function loadSettlementsForInvoicing($mandataire, $strFrequency = null, $cutOffDate = null){
        return $this->getRepository()->getSettlementsForInvoicing($mandataire, $strFrequency, $cutOffDate);
    }

    public function getTotalSettlementsForQuarterlyTaxation($autoEntrepreneur = null, $current = true){
        return $this->getRepository()->getTotalSettlementsForQuarterlyTaxation($autoEntrepreneur, $current);
    }

    public function loadSettlementsForMandataireFee(){
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        return $this->getRepository()->getSettlementsForMandataireFee($cooperonsManager->loadPartyCooperons());
    }

    public function getTvaDetails(Mandataire $mandataire, $rateTva = null){
        $corporateManager = $this->container->get('apr_corporate.corporate_manager');

        $owner = $mandataire->getOwner();
        $client = $mandataire->getClient();

        $isAutoEntrepreneur = !is_null($owner->getAutoEntrepreneur());

        $countryOwner = $owner->getCorporate()?$owner->getCorporate()->getCountry():null;
        $countryClient = $client->getCorporate()?$client->getCorporate()->getCountry():null;

        if(is_null($countryClient) or is_null($countryOwner)) {
            $countryFR = $corporateManager->getCountryFR();
            if(is_null($countryClient)) $countryClient = $countryFR;
            if(is_null($countryOwner)) $countryOwner = $countryFR;
        }

        if($isAutoEntrepreneur) {
            return array('statusTva' => 'autoEntrepreneur', 'rateTva' => 0);
        } else {
            $codeOwner = $countryOwner->getCode();
            $ueOwner = $countryOwner->getUE();
            $codeClient = $countryClient->getCode();
            $ueClient = $countryClient->getUE();

            $result = array('rateTva' => 0);
            if($ueClient) {
                if($ueOwner) {
                    $result['statusTva'] = ($codeOwner == $codeClient)? 'localUE':'intraUE';
                } else {
                    $result['statusTva'] = 'importUE';
                }
            } else {
                if($codeOwner == $codeClient) {
                    $result['statusTva'] = 'localIntl';
                } else {
                    $result['statusTva'] = $ueOwner? 'exportUE':'exportIntl';
                }
            }

            if(in_array($result['statusTva'], array('localUE', 'localIntl'))) {
                $result['rateTva'] = !is_null($rateTva)?$rateTva:$countryClient->getRateTva();
            }

            return $result;
        }
    }

    public function summaryTva(Mandataire $mandataire, $allSettlements) {
        $tvaDetails = $this->getTvaDetails($mandataire);

        $result = array('status' => $tvaDetails['statusTva']);

        if($tvaDetails['rateTva']) {
            $result['amounts'] = array();
            foreach ($allSettlements as $settlement) {
                if (!isset($result['amounts'][$settlement->getRateTva()])) $result['amounts'][$settlement->getRateTva()] = 0;
                $result['amounts'][$settlement->getRateTva()] += $settlement->getAmountTva();
            }
        }

        return $result;
    }

    public function calculateSettlement(Mandataire $mandataire, $unitAmount, $quantity = 1) {

        if($unitAmount && $quantity) {
            $result = $this->getTvaDetails($mandataire);

            $result['amountHt'] = $unitAmount * $quantity;
            $result['quantity'] = $quantity;
            $result['unitAmount'] = $unitAmount;
            // Arrondi au centime près
            $result['amountTva'] = round($result['amountHt'] * $result['rateTva']/100,2);
            $result['amountTtc'] = $result['amountHt']+$result['amountTva'];

            return $result;
        }
    }

    public function createSettlement(Mandataire $mandataire, $description, $calculateSettlement, $type = null){
        $owner = $mandataire->getOwner();

        if(!$owner->getCanSettle()) {
            throw new ApiException(400410);
        } elseif(!$mandataire->getCanSettle()) {
            throw new ApiException(40089);
        } else {
            $settlement = new Settlement ($mandataire, $description, $calculateSettlement, $type);
            return $settlement;
            // PersistAndFlush géré par Manager Métier + cascade  ...
        }
    }

    public function cancelSettlement(Mandataire $mandataire, Settlement $settlement, &$listeEmails = null){
        $contractManager = $this->container->get('apr_contract.contract_manager');

        if(!$settlement->getValidatedDate() && $contractManager->beforeCancelSettlement($mandataire, $settlement, $listeEmails)) {
            $this->removeAndFlush($settlement);
            if(!is_null($listeEmails)) array_push($listeEmails, $this->getEmailCancelSettlement($mandataire, $settlement));
            // updateWaitingSettlements pour débloquer éventuellement canSettle ...
            $this->updateWaitingSettlements($mandataire, $listeEmails);
        };
    }

    public function validateSettlements(Mandataire $mandataire, $allSettlements, &$listeEmails = null){
        $recordManager = $this->container->get('apr_mandataire.record_manager');

        $amount = 0;
        foreach($allSettlements as $settlement) {
            $amount += $settlement->getAmount();
        }

        if($mandataire->getDepot() >= $amount) {
            foreach($allSettlements as $settlement) {
                $settlement->validate();
            }
            $recordManager->recordSettlements($mandataire, $allSettlements);
            $this->flush();
            if(!is_null($listeEmails)) array_push($listeEmails, $this->getEmailConfirmationSettlements($mandataire, $allSettlements));
        }
    }

    public function updateWaitingSettlements(Mandataire $mandataire, &$listeEmails)
    {
        $contractManager = $this->container->get('apr_contract.contract_manager');
        $recordManager = $this->container->get('apr_mandataire.record_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        // GrossUp - permet de garantir marge de sécurité disponible pour permettre le paiement de frais ...
        $totalGrossUp = 0;
        $ownerIsCooperons = $cooperonsManager->isMandataireCooperons($mandataire);

        $allConfirmedSettlements = $allStandbySettlements = array();
        $hasWaitingForPayment = false;
        foreach($this->loadWaitingSettlements($mandataire) as $settlement) {
            $grossUpRate = $ownerIsCooperons?$cooperonsManager->getGrossUpRate($settlement):0;
            $settlement->validate($totalGrossUp, $grossUpRate);
            $status = $settlement->getStatus();
            if($settlement->getValidatedDate()) {
                // Arrondi au centime supérieur par sécurité ...
                $totalGrossUp += ceil($settlement->getAmount() * (1+$grossUpRate) * 100)/100;
                array_push($allConfirmedSettlements, $settlement);
            } elseif($status == "waitingForNotify") {
                $settlement->notify();
                array_push($allStandbySettlements, $settlement);
            } elseif($status == 'waitingForPayment') {
                $hasWaitingForPayment = true;
            }
        }

        if(count($allConfirmedSettlements)) {
            $contractManager->beforeRecordSettlements($mandataire, $allConfirmedSettlements, $listeEmails);
            $recordManager->recordSettlements($mandataire, $allConfirmedSettlements);

            array_push($listeEmails, $this->getEmailConfirmationSettlements($mandataire, $allConfirmedSettlements));
            $contractManager->afterSettlements($mandataire, $allConfirmedSettlements, $listeEmails);
        }

        $contract = $mandataire->getContract();
        if(count($allStandbySettlements)) {
            // Contract Suspension ...
            if($contract && $contract->isSuspendable()) $contract->suspend();
            $mandataire->notCanSettle();
            array_push($listeEmails, $this->getEmailStandbySettlements($mandataire, $allStandbySettlements));
        } elseif(!$hasWaitingForPayment) {
            if($contract && $contract->isSuspendable() && $contract->getSuspensionDate()) $contract->resume();
            $mandataire->updateCanSettle();
        }
        $this->persist($mandataire);

        $this->flush();
    }

    public function getEmailConfirmationSettlements(Mandataire $mandataire, $allSettlements, $extraMsg = ''){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire, $extraMsg);
        $mailParam['body']['parameter']['allSettlements'] = $allSettlements;
        $mailParam['subject'] = "Confirmation de Commande";
        $mailParam['body']['parameter']['status'] = 'confirm';
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationSettlement.html.twig';
        return $mailParam;
    }

    public function getEmailStandbySettlements(Mandataire $mandataire, $allSettlements, $extraMsg = ''){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire, $extraMsg);
        $mailParam['body']['parameter']['allSettlements'] = $allSettlements;
        $mailParam['subject'] = "ATTENTION - Commande en attente";
        $mailParam['body']['parameter']['status'] = 'standby';
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationSettlement.html.twig';
        return $mailParam;
    }

    public function getEmailCancelSettlement(Mandataire $mandataire, Settlement $settlement, $extraMsg = ''){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire, $extraMsg);
        $mailParam['body']['parameter']['allSettlements'] = array($settlement);
        $mailParam['subject'] = "Annulation de Commande: ".$settlement->getDescription();
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationSettlement.html.twig';
        $mailParam['body']['parameter']['status'] = 'cancel';
        return $mailParam;
    }
}
