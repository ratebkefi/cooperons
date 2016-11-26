<?php

namespace Apr\CorporateBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\CorporateBundle\Entity\Corporate;
use Apr\ContractBundle\Entity\Collaborator;

class CorporateManager extends BaseManager
{
    
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    protected function getRepository() {
            return $this->em->getRepository('AprCorporateBundle:Corporate');
    }

    public function getCountryFR() {
        return $this->em->getRepository('AprCorporateBundle:Country')->getCountryFR();
    }

    /**
     * @author Fondative <devteam@fondative.com>
     */
    public function getCountries()
    {
        return $this->em->getRepository('AprCorporateBundle:Country')->findAll();
    }

    /**
     * @author Fondative <devteam@fondative.com>
     */
    public function getCountry($id)
    {
        return $this->em->getRepository('AprCorporateBundle:Country')->find($id);
    }

    // ToDo: constructeur Corporate ? Doit pouvoir accéder à getCountryFR ??? Query dans Model ? ...
    public function initCorporate() {
        $corporate = new Corporate();
        $countryFR = $this->getCountryFR();
        $corporate->setCountry($countryFR);
        $this->persist($corporate);
        return $corporate;
    }

    public function loadCorporateById($id)
    {
        return $id?$this->getRepository()->find($id):null;
    }

    public function loadCorporateBySiren($siren, $activeOnly = false)
    {
        return $siren?$this->getRepository()->getCorporateBySiren($siren, $activeOnly):null;
    }

    public function loadCorporateByTVAIntracomm($tvaIntracomm, $activeOnly = false)
    {
        return $this->getRepository()->getAllCorporates($activeOnly, $tvaIntracomm);
    }

    public function getAllCorporates($activeOnly = false)
    {
        return $this->getRepository()->getAllCorporates($activeOnly);
    }

    public function getAllCorporatesByAdministratorMember($member)
    {
        return $this->getRepository()->getAllCorporates(null, null, $member);
    }

    public function createCorporateWithAdministrator(Corporate $corporateData, $member, &$listeEmails)
    {
        $welcomeCorporate = false;
        $tvaIntracomm = $corporateData->getTvaIntracomm();
        // Vérification qu'un corporate n'a pas déjà été créé par le passé ...
        $corporate = $this->getRepository()->getAllCorporates(false, $tvaIntracomm);
        if($corporate) {
            if($corporate->getAdministrator()) {
                if($corporate->getAdministrator()->getMember() != $member) {
                    return null;
                }
            } else {
                // Corporate désactivé par le passé ...
                $corporateData->setId($corporate->getId());
                $welcomeCorporate = true;
            }
        } else {
            $welcomeCorporate = true;
        }

        if($welcomeCorporate) {
            $collaborator = new Collaborator($corporateData->getParty(), $member);
            $corporateData->getParty()->setAdministrator($collaborator);
            array_push($listeEmails, $this->getMailWelcomeCorporate($corporateData));
            $this->generateAccordPDF($corporateData);
        }

        $this->persistAndFlush($corporateData);

        return $corporateData;
    }

    public function signAccordCadre($corporate, &$listeEmails) {
        $corporate->signAccordCadre();
        $this->persistAndFlush($corporate);
        array_push($listeEmails, $this->getMailConfirmationAccord($corporate));
    }

    public function cancelCorporate($corporate, &$listeEmails) {
        $administrator = $corporate->getAdministrator();
        $hasCanceled = $corporate->cancelAccordCadre();
        if($hasCanceled) {
            $this->persistAndFlush($corporate);
            array_push($listeEmails, $this->getMailResiliationAccord($corporate, $administrator->getMember()));
            foreach($corporate->getAllCollaborators() as $collaborator) {
                $this->removeAndFlush($collaborator);
            }
        }
    }

    public function getMailWelcomeCorporate($corporate){
        $member = $corporate->getAdministrator()->getMember();
        return array(
            'to' => $member->getEmail(),
            'subject' =>'Le compte Entreprise de  '.
                $corporate->getRaisonSocial() . ' a bien été créé',
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:welcomeCorporate.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate
                )),
        );
    }

    public function getMailConfirmationAccord($corporate)
    {
        $member = $corporate->getAdministrator()->getMember();
        return array(
            'to' => $member->getEmail(),
            'subject' => "Merci ! Grâce à vous, les salariés de " . $corporate->getRaisonSocial() . " peuvent désormais augmenter leur rémunération jusqu'à 1'000 € par trimestre sans coût pour " . $corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:confirmationAccord.html.twig',
                'parameter' => array(
                    'corporate' => $corporate
                )),
        );
    }

    public function getMailResiliationAccord($corporate, $member)
    {
        return array(
            'to' => $member->getEmail(),
            'subject' => "Résiliation du Contrat Cadre Coopérons !",
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:resiliationAccord.html.twig',
                'parameter' => array(
                    'corporate' => $corporate,
                )),
        );
    }

    public function generateAccordPDF(Corporate $corporate){
        $html = $this->container->get('templating')->render(
            'AprCorporateBundle:PDF:accordCadre.html.twig', array(
            'corporate' => $corporate,
            'baseurl' => $this->container->getParameter('baseUrl'),
        ));
        $uploadsPath = $this->container->getParameter('uploadsPath');
        $filePath = $uploadsPath . 'CorporateAccords/' . $corporate->getAccordRef() . '.pdf';
        $options = array(
            'margin-left'                  => 0,
            'margin-right'                 => 0,
            'margin-top'                   => 0,
        );

        $this->container->get('knp_snappy.pdf')->generateFromHtml($html, $filePath, $options);

    }
    
}
