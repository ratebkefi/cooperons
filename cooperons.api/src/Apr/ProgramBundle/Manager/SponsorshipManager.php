<?php
namespace Apr\ProgramBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Sponsorship;
use Apr\ProgramBundle\Entity\PreProdSponsorship;

class SponsorshipManager extends BaseManager {
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        // Nécessaire pour implémenter BaseManager - non utilisé
    }

    public function getProdOrPreProdRepository($status)
    {
        $programBundle = 'AprProgramBundle:';
        $entity = 'Sponsorship';
        $prefix = ($status == 'preprod')?'PreProd':'';

        return $this->em->getRepository($programBundle.$prefix.$entity);
    }

    public function loadSponsorshipByMember($program, $member)
    {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        $participatesTo = $participatesToManager->loadParticipatesToByMember($program, $member);
        return $participatesTo->getSponsor();
    }

    public function loadAllSponsorships($sponsor)
    {
        if(is_null($sponsor)) {
            return null;
        } else {
            return array_merge(
                $this->getProdOrPreProdRepository('preprod')->getAllSponsorships($sponsor),
                $this->getProdOrPreProdRepository('prod')->getAllSponsorships($sponsor)
            );
        }
    }

    public function createSponsorship($program, $sponsor, $affiliate, &$emails = null) {
        $invitationManager = $this->container->get('apr_program.invitation_manager');
        $accountManager = $this->container->get('apr_program.account_points_history_manager');

        /** Pas de création de parrainage si:
         *  - $affiliate a déjà un $sponsor
         *  - $affiliate = $sponsor
         *  - $sponsor est un des descendants de $affiliate (cycle)
         */

        if(
            is_null($program) or is_null($sponsor) or is_null($affiliate) or
            !($sponsor->getProgram() == $program && $affiliate->getProgram() == $program) or
            !is_null($affiliate->getSponsor()) or $sponsor == $affiliate or $affiliate == $sponsor->getKing()
        ) {
            return null;
        } else {
            $sponsorship = ($program->getStatus() == 'preprod')?new PreProdSponsorship($program, $sponsor, $affiliate):
                new Sponsorship($program, $sponsor, $affiliate);
            $this->persist($sponsorship);

            // Mise à jour sponsorships dont les kings étaient auparavant $affiliate -> maintenant $sponsor ... (Post-parrainage ...)
            $sponsorshipsWithKingToUpdate = $this->getProdOrPreProdRepository($program->getStatus())->findBy(array('king' => $affiliate));
            foreach ($sponsorshipsWithKingToUpdate as $sponsorship) {
                $sponsorship->updateKing();
            }

            // Suppression de l'invitation
            $invitation = $invitationManager->loadInvitationByEmail($program, $affiliate->getMember()->getEmail());
            if($invitation) $invitationManager->removeAndFlush($invitation);

            // Post-parrainage
            $multiPoints = floor($affiliate->getTotal()['multiPoints']*2/3);
            if($multiPoints) {
                $datas = array(
                    array('labelOperation' => '__multi', 'amount' => $multiPoints, 'info' => $affiliate->getMember()->getFirstName()." ".$affiliate->getMember()->getLastName()),
                );
                $accountManager->addPoints($sponsor, $datas, $emails);
            }

            $this->couplagePlus($program, $sponsor, $affiliate, $emails);

            if($affiliate->getMember()->hasAcceptedCGUMember()) array_push($emails, $this->getConfirmationSponsorship($sponsor, $affiliate, $program));

            $this->flush();

            return $this->getProdOrPreProdRepository($program->getStatus())->getSponsorship($program, $sponsor, $affiliate);
        }
    }

    public function couplagePlus($program, $sponsor, $affiliate, &$emails = null) {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        if((!is_null($sponsor) or !is_null($affiliate)) && !$coopPlusManager->isProgramPlus($program) && $program->getStatus() == 'prod') {
            if(is_null($sponsor)) {
                $sponsor = $affiliate->getSponsor();
            }

            // Si sponsor ou affiliate non encore inscrits comme User -> sponsorPlus/affiliatePlus = null...
            if($sponsor && $sponsor->getMember()->getUser()) {
                $programPlus = $coopPlusManager->loadProgramPlus();
                $sponsorPlus = $coopPlusManager->loadParticipatesToPlus($sponsor->getMember());

                if($sponsorPlus) {
                    if(is_null($affiliate)) {
                        $allAffiliates = $sponsor->getAllAffiliates();
                    } else {
                        $allAffiliates = array($affiliate);
                    }

                    foreach($allAffiliates as $affiliate) {
                        if($affiliate->getMember()->getUser()) {
                            $affiliatePlus = $coopPlusManager->loadParticipatesToPlus($affiliate->getMember());
                            $this->createSponsorship($programPlus, $sponsorPlus, $affiliatePlus, $emails);
                        }
                    }
                }
            }
        }
    }

    /**
     * Confirmation Sponsorship
     */
    public function getConfirmationSponsorship($sponsor, $affiliate, $program){
        $affiliateMember = $affiliate->getMember();
        $sponsorMember = $sponsor->getMember();
        $labelProgram = ($program)?$program->getLabel():'Coopérons';

        return array(
            'to' => $sponsorMember->getEmail(),
            'subject' => (($program->getStatus() == 'preprod')?'[PRE-PRODUCTION] ':'').'Votre filleul '.
                $affiliateMember->getFirstName().' '.$affiliateMember->getLastName().' a rejoint '.$labelProgram,
            'body' => array(
                'template' => 'AprProgramBundle:Emails:confirmationSponsorship.html.twig',
                'parameter' => array(
                    'member' => $sponsorMember,
                    'filleul' => $affiliateMember,
                    'program'   => $program,
                )
            )
        );
    }


}

?>
