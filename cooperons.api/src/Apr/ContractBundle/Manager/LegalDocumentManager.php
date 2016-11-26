<?php

namespace Apr\ContractBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\Classes\Tools;
use \Apr\ContractBundle\Entity\Contract;
use \Apr\ContractBundle\Entity\LegalDocument;
use \Apr\ContractBundle\Entity\Habilitation;

class LegalDocumentManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function securityCheck($user, $legalDocument, $isOwner = false, $isClient = false)
    {
        $contractManager = $this->container->get('apr_contract.contract_manager');
        
        if ($legalDocument == null) {
            throw new ApiException(400106);
        }

        return $contractManager->securityCheck($user, $legalDocument->getContract(), $isOwner, $isClient);
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprContractBundle:LegalDocument');
    }

    public function loadLegalDocumentById($id)
    {
        return $id ? $this->getRepository()->find($id) : null;
    }

    public function createLegalDocument(Contract $contract, $ownerLabel) {
        $legalDocument = new LegalDocument($contract, $ownerLabel);
        return $legalDocument;
    }

    public function publishLegalDocument(LegalDocument $legalDocument, &$listeEmails)
    {
        $ownerHabilitation = $legalDocument->getContract()->getOwnerCollaborator()->getHabilitation();
        $legalDocument->publish($ownerHabilitation);
        $this->persistAndFlush($legalDocument);
        array_push($listeEmails, $this->getEmailLegalDocumentNotification($legalDocument));
    }

    public function agreeLegalDocument(LegalDocument $legalDocument, &$listeEmails)
    {
        $contract = $legalDocument->getContract();
        $clientHabilitation = $contract->getClientCollaborator()->getHabilitation();
        // Vérification que le client Corporate a signé le contrat cadre ...
        $corporate = $contract->getClient()->getCorporate();
        if ($corporate && !$corporate->isAccordSigned()) return;

        if ($legalDocument->getOldLegalDocument()) {
            $legalDocument = $this->replaceLegalDocument($legalDocument);
        }

        $legalDocument->agree($clientHabilitation);
        $this->persistAndFlush($legalDocument);

        array_push($listeEmails, $this->getEmailLegalDocumentAgreed($legalDocument));
    }

    public function removeLegalDocument(LegalDocument $legalDocument)
    {
        if ($legalDocument->isRemovable()) {
            $this->removeAndFlush($legalDocument);
        } else {
            throw new ApiException(400121);
        }
    }

    public function terminateLegalDocument(LegalDocument $legalDocument, &$listeEmails = null)
    {
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        if ($legalDocument->canTerminate()) {
            if ($legalDocument->getMandataire()) $mandataireManager->liquidation($legalDocument->getMandataire(), $listeEmails);
            $legalDocument->terminate();
            $this->persistAndFlush($legalDocument);
            if (!is_null($listeEmails)) array_push($listeEmails, $this->getEmailLegalDocumentCanceled($legalDocument));
            return true;
        } else {
            return false;
        }
    }

    public function copyLegalDocument(LegalDocument $legalDocument)
    {
        if (is_null($legalDocument->getOldLegalDocument())) {
            $newLegalDocument = clone $legalDocument;
            $newLegalDocument->cloneFinish($legalDocument);
            $this->persistAndFlush($legalDocument);
            return $newLegalDocument;
        } else {
            return null;
        }
    }

    public function replaceLegalDocument(LegalDocument $legalDocument)
    {
        $oldLegalDocument = $legalDocument->getOldLegalDocument();

        if ($oldLegalDocument) {
            // RecruitmentSettings
            $oldRecruitmentSettings = $oldLegalDocument->getRecruitmentSettings();
            if($oldRecruitmentSettings) {
                $newRecruitmentSettings = $legalDocument->clearRecruitmentSettings();
                $oldRecruitmentSettings->setNewRecruitmentSettings($newRecruitmentSettings);
                $this->persistAndFlush($oldRecruitmentSettings);

                $oldLegalDocument->setRecruitmentSettings($newRecruitmentSettings);
            }

            // ServiceTypes
            $allNewServiceTypes = $legalDocument->getAllServiceTypes();
            foreach($oldLegalDocument->getAllServiceTypes() as $serviceType) {
                $this->removeAndFlush($serviceType);
            }
            foreach($allNewServiceTypes as $serviceType) {
                $oldLegalDocument->addServiceType($serviceType);
            }
            $legalDocument->clearAllServiceTypes();

            $this->terminateLegalDocument($legalDocument);

            return $oldLegalDocument;
        }
    }

    public function prepareEmailLegalDocument(LegalDocument $legalDocument, $type)
    {
        $contract = $legalDocument->getContract();
        if ($type == 'publish') {
            $toMember = $contract->getClientMember();
            $otherMember = $contract->getOwnerMember();
        } else {
            $toMember = $contract->getOwnerMember();
            $otherMember = $contract->getClientMember();
        }

        return array(
            'to' => $toMember->getEmail(),
            'cc' => array($otherMember->getEmail()),
            'body' => array(
                'template' => 'AprContractBundle:Emails:notificationLegalDocument.html.twig',
                'parameter' => array(
                    'member' => $toMember,
                    'otherMember' => $otherMember,
                    'contract' => $contract,
                    'legalDocument' => $legalDocument,
                    'type' => $type
                )
            )
        );
    }

    public function getEmailLegalDocumentNotification(LegalDocument $legalDocument)
    {
        $mailParam = $this->prepareEmailLegalDocument($legalDocument, 'publish');
        $mailParam['subject'] = 'Votre contrat avec ' . $legalDocument->getContract()->getOwner()->getLabel() . ' est disponible';
        return $mailParam;
    }

    public function getEmailLegalDocumentAgreed(LegalDocument $legalDocument)
    {
        $mailParam = $this->prepareEmailLegalDocument($legalDocument, 'agree');
        $mailParam['subject'] = 'Signature de votre contrat avec ' . $legalDocument->getContract()->getClient()->getLabel();
        return $mailParam;
    }

    public function getEmailLegalDocumentCanceled(LegalDocument $legalDocument)
    {
        $mailParam = $this->prepareEmailLegalDocument($legalDocument, 'cancel');
        $mailParam['subject'] = 'Résiliation de votre contrat avec ' . $legalDocument->getContract()->getClient()->getLabel();
        return $mailParam;
    }
}

?>
