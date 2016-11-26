<?php
namespace Apr\ProgramBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Program;
use Apr\ProgramBundle\Entity\Journal;
use Apr\CoreBundle\Tools\Tools;

class ProgramManager extends BaseManager {
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprProgramBundle:Program');
    }

    public function loadProgramById($id){
        return $this->getRepository()->findOneBy(array('id' => $id));
    }

    public function loadProgramByApiKey($apiKey){
        return $this->getRepository()->findOneBy(array('apiKey' => $apiKey));
    }

    public function bypassMandataireActions(Program $program) {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        return $program->getStatus() != "prod" or $cooperonsManager->isCorporateCooperons($program->getCorporate());
    }

    public function createProgram(Program $program, $isEasy = false)
    {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        // Collaborator
        $collaborator = $program->getCollaborator();
        $program->setSenderEmail($collaborator->getMember()->getEmail());
        $program->setSenderName($collaborator->getMember()->getFirstName()." ".strtoupper($collaborator->getMember()->getLastName()));

        // Image
        $image = $program->getImage();
        if(!is_null($image) && (!$image->getId() or $image->hasChanged())) {
            $image->uploadFile('img_'.$program->getFileName(), 'ProgramLogos');
            $program->setImage($image);
        }

        // Easy
        if($isEasy) $program->initEasy();

        // Program - cascade persist Image, Mandataire ...
        $this->persist($program);
        $this->flush();
        return $program;
    }

    public function preProdProgram($program)
    {
        $mailsManager = $this->container->get('apr_program.mails_manager');
        $program->setStatus('preprod');
        if(!$program->getOldProgram()) {
            $mailsManager->createDefaultMail($program);
        }
        $this->persist($program);
        $this->flush();
        return $program;
    }

    public function renewal(Program $program, &$listeEmails) {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        $program->setNewDateExpiration();
        // Check first program - Bonus Coopérons Plus ...
        $coopPlusManager->givePointsSponsorPlusIfFirstActive($program, $listeEmails);
        if($program->getStatus() == "standby") {
            $this->preProdProgram($program);
            array_push($listeEmails, $coopPlusManager->getEmailWelcomeProgram($program));
        } elseif($program->getNewProgram() && $program->getNewProgram()->getStatus()=='standby') {
            // Modification d'un programme ... passage en PreProd du NewProgram
            $this->preProdProgram($program->getNewProgram());
        }
        $program->confirmSettlementAbonnement();
        $this->persistAndFlush($program);
    }

    public function updateLegal(Program $program, $legalData) {
        $program->setDescription($legalData['description']);
        foreach($program->getAllOperations() as $operation){
            if(isset($legalData['description_op_'.$operation->getId()])){
                $operation->setDescription($legalData['description_op_'.$operation->getId()]);
            }
        }
        $this->persistAndFlush($program);
        return $program;
    }

    public function addActionAPIToJournal(Program $program, $url, $method, $parameters){
        $journal = new Journal();
        $journal->setProgram($program);
        $journal->setUrl($url);
        $journal->setMethod($method);
        $journal->setParameters($parameters);
        $this->persistAndFlush($journal);
    }

    public function clearJournal(Program $program){
        $allJournals = $program->getAllJournals();
        foreach($allJournals as $journal) {
            $this->removeAndFlush($journal);
        }
    }

    public function clearProgram(Program $program){
        $this->clearJournal($program);

        if($program->getStatus() == 'preprod') {
            // On commence par les Sponsorships pour éviter les boucles cascade remove ...
            $allSponsorships = $program->getAllSponsorships();
            foreach($allSponsorships as $sponsorship) {
                $sponsorship->getAffiliate()->setSponsorship(null);
                $this->removeAndFlush($sponsorship);
            };

            // Puis AccountHistoryPoints ...
            $allAccountHistoryPoints = $program->getAllAccountHistoryPoints();
            foreach($allAccountHistoryPoints as $accountHistoryPoints) {
                $this->removeAndFlush($accountHistoryPoints);
            };


            $isEasy = $program->isEasy();

            // Et enfin ParticipatesTo (avec Invitation en cascade ...)
            $allParticipatesTo = $program->getAllParticipatesTo();
            $allMembers = array();
            foreach($allParticipatesTo as $participatesTo) {
                if($isEasy) {
                    foreach($participatesTo->getAllAffairs() as $affair) {
                        $this->removeAndFlush($affair);
                    };
                }
                array_push($allMembers, $participatesTo->getMember());
                $this->removeAndFlush($participatesTo);
            };

            foreach($allMembers as $member) {
                $allParticipatesTo = $member->getAllParticipatesTo();
                if(count($allParticipatesTo) <= 1) {
                    // Suppression des Members ne participant plus à aucun autre programme que Coopérons Plus - et sans parrain ni filleul Plus ...
                    foreach($allParticipatesTo as $participatesToPlus) {
                        if(!$participatesToPlus->getCountAffiliates() && !$participatesToPlus->getSponsorship()) {
                            // Déclenche aussi la suppression d'éventuelles invitations Plus ... a priori impossible car formulaire masqué en preprod ...
                            // Pas nécessaire a priori de supprimer AccountHistoryPoints / Sponsorships  ...
                            $this->removeAndFlush($participatesToPlus);
                        }
                    }
                    // Suppression de l'User associé
                    $user = $member->getUser();
                    if($user) {
                        $member->unsetUser();
                        $this->removeAndFlush($user);
                    }
                    $this->removeAndFlush($member);
                }
            }

        }
    }

    public function replaceProgram(Program &$program, Program &$oldProgram) {
        $mailsManager = $this->container->get('apr_program.mails_manager');

        // Préparation à la suppression du new_program - sans toucher au Collaborator et Image ...
        $program->setImage(null);

        $oldProgram->setDescription($program->getDescription());
        $oldProgram->setDateValidate(Tools::DateTime());

        // Easy
        if($program->isEasy()) {
            $oldEasySetting = $oldProgram->getEasySetting();
            $oldProgram->clearEasySetting();
            $this->removeAndFlush($oldEasySetting);

            $easySetting = $program->getEasySetting();
            $program->clearEasySetting();
            $oldProgram->setEasySetting($easySetting);
        }

        // Operations
        $allNewOperations = $program->getAllOperations();
        foreach($oldProgram->getAllOperations() as $operation) {
            $this->removeAndFlush($operation);
        }

        foreach($allNewOperations as $operation) {
            $oldProgram->addOperation($operation);
        }

        // MailInvitations
        $allNewMails = $program->getAllMailInvitations();
        $allNewMailsByCodeMail = array();

        foreach($allNewMails as $mailInvitation) {
            $allNewMailsByCodeMail[$mailInvitation->getCodeMail()] = $mailInvitation;
        }

        foreach($oldProgram->getAllMailInvitations() as $oldMailInvitation) {
            $codeMail = $oldMailInvitation->getCodeMail();
            if(array_key_exists($codeMail, $allNewMailsByCodeMail)) {
                $newMailInvitation = $allNewMailsByCodeMail[$codeMail];
                $oldMailInvitation->setSubject($newMailInvitation->getSubject());
                $oldMailInvitation->setContent($newMailInvitation->getContent());
                $oldMailInvitation->setHeader($newMailInvitation->getHeader());
                $oldMailInvitation->setFooter($newMailInvitation->getFooter());
                $oldMailInvitation->setSignature($newMailInvitation->getSignature());
                $this->persist($oldMailInvitation);
                $mailsManager->deleteMail($newMailInvitation);
                unset($allNewMailsByCodeMail[$codeMail]);
            } else {
                $mailsManager->deleteMail($oldMailInvitation);
            }
        }

        foreach($allNewMailsByCodeMail as $mailInvitation) {
            $mailInvitation->setProgram($oldProgram);
            $this->persist($mailInvitation);
        }

        $this->removeAndFlush($program);
    }

    public function activateProgram(Program $program, &$emails) {
        if($program->getCorporate()->isAccordSigned()) {
            $this->clearProgram($program);

            $oldProgram = $program->getOldProgram();
            if($oldProgram) {
                $this->replaceProgram($program, $oldProgram);
                array_push($emails,$this->getEmailConfirmationActivateProgram($oldProgram));
                foreach($oldProgram->getAllParticipatesTo() as $participatesTo) {
                    array_push($emails,$this->getEmailNotificationModificationProgram($participatesTo));
                }
            } else {
                $program->setDateValidate(Tools::DateTime());
                $program->setStatus('prod');
                $this->persistAndFlush($program);
                array_push($emails,$this->getEmailConfirmationActivateProgram($program));
            }
        }
    }

    public function cancelProgram(Program $program, &$emails) {
        $mailsManager = $this->container->get('apr_program.mails_manager');
        $contractManager = $this->container->get('apr_contract.contract_manager');
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        if($program->getStatus() == 'preprod') {
            // Pas possible de résilier un programme en PreProd => doit être activé en production ...
            return;
        } elseif($program->getStatus() == 'standby') {
            if($program->isEasy()) {
                $easySetting = $program->getEasySetting();
                $program->clearEasySetting();
                $this->removeAndFlush($easySetting);
            }
            foreach($program->getAllOperations() as $operation) {
                $this->removeAndFlush($operation);
            }
            foreach($program->getAllMailInvitations() as $mailInvitation) {
                $mailsManager->deleteMail($mailInvitation);
            }

            if($program->getOldProgram()) {
                // On ne supprime pas l'image - utilisée par l'ancien Program...
                $program->setImage(null);
            }

            $this->removeAndFlush($program);
        } else {
            if($program->getStatus() != 'standby') {
                array_push($emails,$coopPlusManager->getEmailResiliationProgram($program));
                foreach($program->getAllParticipatesTo() as $participatesTo) {
                    array_push($emails,$this->getEmailNotificationResiliationProgram($participatesTo));
                }
            }

            $program->cancel();
            $this->persistAndFlush($program);
        }
    }

    public function copyProgram(Program $program){
        $newProgram = clone $program;
        $newProgram->cloneFinish($program);
        $this->persistAndFlush($newProgram);
        return $newProgram;
    }

    public function buildProgramsDetails(){
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        $allPrograms = $this->getRepository()->findAll();
        $result = array();
        foreach($allPrograms as $program) {
            if(!$program->getOldProgram()) {
                $my = array(
                    'id' => $program->getId(),
                    'label' => $program->getLabel(),
                    'member' => $program->getCollaborator()->getMember(),
                );

                if(!$this->bypassMandataireActions($program)) {
                    $my['mandataire_id'] = $program->getMandataire()->getId();
                    $my['minDepot'] = $program->getMinDepot();
                }

                $status = $program->getStatus();
                if($program->getNewProgram()) {
                    $my['status'] = 'En cours de modification';
                } elseif($status == 'preprod') {
                    $my['status'] = 'Pré-production';
                } elseif ($status == 'prod') {
                    $my['status'] = 'En production';
                } elseif ($status == 'cancel') {
                    $my['status'] = 'Résilié';
                } else {
                    $my['status'] = 'En cours de création';
                }
                $dateExp = $program->getDateExpiration();
                if($status == 'prod' && !$cooperonsManager->isCorporateCooperons($program->getCorporate()) && Tools::DateTime("+1 month") >= $dateExp) {
                    $dateExp = clone $dateExp;
                    $my['renewal'] = "(Expiration le ".$dateExp->modify('-1 day')->format('d/m/Y').")";
                }
                array_push($result, $my);
            }
        }

        return $result;
    }

    public function getEmailConfirmationActivateProgram(Program $program){
        $member = $program->getCollaborator()->getMember();
        $mailParam = array();
        $mailParam['subject'] = "Félicitations ! Le programme ".$program->getLabel()." est désormais activé en production";
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprProgramBundle:Emails:confirmationActivateProgram.html.twig';
        $mailParam['body']['parameter'] = array(
            'member'      => $member,
            'program'     => $program,
        );
        return $mailParam;
    }

    public function getEmailConfirmationTransferProgram(Program $program, $oldCollaborator){
        $member = $program->getCollaborator()->getMember();
        $label = 'programme '.$program->getLabel();
        $mailParam = array();
        $mailParam['subject'] = "Changement de gestionnaire du ".$label;
        $mailParam['cc'] = array($oldCollaborator->getMember()->getEmail());
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprProgramBundle:Emails:confirmationTransferCollaborator.html.twig';
        $mailParam['body']['parameter'] = array(
            'member'      => $member,
            'label'     => $label,
        );
        return $mailParam;
    }

    public function getEmailNotificationModificationProgram($participatesTo){
        $member = $participatesTo->getMember();
        $program = $participatesTo->getProgram();
        $mailParam = array();
        $mailParam['subject'] = 'Modification du programme '.$program->getLabel();
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprProgramBundle:Emails:notificationModificationProgram.html.twig';
        $mailParam['body']['parameter'] = array(
            'member'      => $member,
            'program'     => $program,
        );
        return $mailParam;
    }

    public function getEmailNotificationResiliationProgram($participatesTo){
        $member = $participatesTo->getMember();
        $program = $participatesTo->getProgram();
        $mailParam = array();
        $mailParam['subject'] = 'Résiliation du programme '.$program->getLabel();
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprProgramBundle:Emails:notificationResiliationProgram.html.twig';
        $mailParam['body']['parameter'] = array(
            'member'      => $member,
            'program'     => $program,
        );
        return $mailParam;
    }

    /**
     * Load Data for create a new program by API
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param $user \Apr\UserBundle\Entity\User Connected user
     * @return array
     */
    public function getDataForNewProgram($user)
    {
        $collaborators = $user->getMember()->getAllCollaborators();
        $collaboratorsData = array();
        foreach ($collaborators as $collaborator) {
            $collaboratorsData[$collaborator->getId()] = $collaborator->getCorporate()->getRaisonSocial();
        }
        return array('collaborators' => $collaboratorsData);
    }

    public function securityCheck($user, $program, $editOperation = true)
    {
        if($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')){
            return true;
        }

        if (is_null($program)) {
            throw new ApiException(40024);
        }

        if ($user->getMember()->getId() != $program->getCollaborator()->getMember()->getId() &&
            $user->getMember()->getId() != $program->getCorporate()->getAdministrator()->getMember()->getId()
        ) {
            throw new ApiException(4031);
        }

        if ($editOperation && $program->getStatus() == 'prod') {
            throw new ApiException(40035);
        }

        return true;
    }
}