<?php

namespace Apr\ProgramBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\CoreBundle\Tools\Tools;
use Apr\ProgramBundle\Entity\Member;
use Apr\ProgramBundle\Entity\Avantage;
use Apr\ProgramBundle\Entity\GiftOrder;
use Apr\CorporateBundle\Entity\Attestation;

class AvantageManager extends BaseManager
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
        return $this->em->getRepository('AprProgramBundle:Avantage');
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function loadGiftOrderById($id) {
        return $this->em->getRepository('AprProgramBundle:GiftOrder')->find($id);
    }

    public function getAllGiftOrders() {
        return $this->em->getRepository('AprProgramBundle:GiftOrder')->findBy(array(), array('id' => 'DESC'));
    }

    public function addAvantageAE(Member $member, $settlement, $description, $result, &$listeEmails) {
        $avantage = new Avantage($member, $result['amountTtc'], $description, $settlement);
        $this->persistAndFlush($avantage);
        array_push($listeEmails, $this->getAvantageConfirmationMail($avantage));
    }

    public function createGiftOrder() {
        $memberManager = $this->container->get('apr_program.member_manager');

        $year = Tools::DateTime('Y');
        $quarter = Tools::DateTime('Q');

        $membersToGift=$memberManager->getRepository()->getMembersWithGiftsPending();

        $giftOrder = new GiftOrder($membersToGift, $year, $quarter);
        $this->persistAndFlush($giftOrder);

        $this->saveExcelOrder($giftOrder);
    }

    public function confirmGiftOrder(GiftOrder $giftOrder, &$listeEmails) {
        $giftOrder->confirm();
        $this->persistAndFlush($giftOrder);
        foreach($giftOrder->getAllAvantages() as $avantage) {
            array_push($listeEmails, $this->getAvantageConfirmationMail($avantage));
        }
    }

    public function getAllAttestations($year, $memberCorporate = null) {
        $allAvantages = $this->getRepository()->getAvantagesForAttestations($year, $memberCorporate);

        $member = $corporate = $year = $quarter = null;
        $lastMember = $lastCorporate = $lastYear = $lastQuarter = null;
        $allAttestations = new ArrayCollection();
        foreach($allAvantages as $avantage) {
            $newAttestation = false;
            foreach(array('member', 'corporate', 'year', 'quarter') as $varName) {
                $methodName = 'get'.ucfirst($varName);
                $$varName = $avantage->$methodName();
                $lastVarName = 'last'.ucfirst($varName);
                $newAttestation = ($$varName != $$lastVarName) or $newAttestation;
            }
            if($newAttestation) {
                $attestation = new Attestation($year, $quarter, $member, $corporate);
                $allAttestations[] = $attestation;
            }

            $attestation->addAvantage($avantage);
        }
        return $allAttestations;
    }

    public function saveExcelOrder(GiftOrder $giftOrder)
    {
        $objPHPExcel = new \PHPExcel();

        $labelOperation = $giftOrder->getLabelOperation();

        $objPHPExcel->getProperties()->setCreator("Cooperons")
                ->setLastModifiedBy("Cooperons")
                ->setTitle($labelOperation)
                ->setSubject($labelOperation)
                ->setDescription($labelOperation);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Nom')
                ->setCellValue('B1', 'Prénom')
                ->setCellValue('C1', 'Email')
                ->setCellValue('D1', 'Montant')
                ->setCellValue('E1', 'Adresse postale')
                ->setCellValue('F1', 'Code postal');
        $i = 2;


        foreach ($giftOrder->getAllAvantages() as $avantage) {
            $member = $avantage->getMember();
            $user = ($member->getUser())?$member->getUser():null;
            $amount = $avantage->getAmount();
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $member->getFirstName())
                    ->setCellValue('B' . $i, $member->getLastName())
                    ->setCellValue('C' . $i, $member->getEmail())
                    ->setCellValue('D' . $i, $amount . '€')
                    ->setCellValue('E' . $i, $user->getContact()->getAddress())
                    ->setCellValue('F' . $i, $user->getContact()->getPostalCode());
            $i++;

        }
        $objPHPExcel->getActiveSheet()->setTitle($labelOperation);
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $uploadsPath = $this->container->getParameter('uploadsPath');
        $objWriter->save($uploadsPath.'GiftOrders/'.$giftOrder->getFileName());

    }

    public function getAvantageConfirmationMail(Avantage $avantage)
    {
        $member = $avantage->getMember();
        $mailParam = array();
        if($avantage->getType() == 'gift') {
            $mailParam['subject'] = 'Opération '.$avantage->getDescription().' : '
                .number_format($avantage->getAmount(),2,',',' ').' € en chèques cadeau pour vous !';
            // Si a un collège confirmé => CC Administrator ...
            if($member->getCollege() && $member->getCollege()->getStatus(true) == 'confirmed') {
                $mailParam['cc'] = $member->getCollege()->getCorporate()->getAdministrator()->getMember()->getEmail();
            }
        } else {
            $mailParam['subject'] = 'Félicitations ! Vous avez bénéficié de '
                .number_format($avantage->getAmount(),2,',',' ').' € de réduction sur votre facture Coopérons !';
        }
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprProgramBundle:Emails:confirmationAvantage.html.twig';
        $mailParam['body']['parameter'] = array(
            'member' => $member,
            'avantage' => $avantage,
            'month' => Tools::displayMonth(Tools::DateTime()->format("m")+2),
        );
        return $mailParam;
    }

}

?>
