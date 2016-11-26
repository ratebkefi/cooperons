<?php

namespace Apr\MandataireBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\CoreBundle\Tools\Tools;
use Apr\MandataireBundle\Entity\Invoice;
use Apr\MandataireBundle\Entity\Mandataire;

class InvoicesManager extends BaseManager
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
        return $this->em->getRepository('AprMandataireBundle:Invoice');
    }

    public function loadInvoiceById($id)
    {
        return $this->getRepository()->find($id);
    }
    
    public function loadInvoiceByRef($ref){
        return $this->getRepository()->findOneBy(array('ref' => $ref));
    }

    public function buildInvoices() {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $allInvoices = array();

        $allSettlements = array();
        foreach(array('week', 'month', 'quarter') as $strFrequency) {
            $cutOffDate = Tools::firstDayOf($strFrequency);
            $mySettlements = $settlementsManager->loadSettlementsForInvoicing(null, $strFrequency, $cutOffDate);
            if(count($mySettlements)) foreach($mySettlements as $settlement) array_push($allSettlements, $settlement);
        }

        foreach($allSettlements as $settlement) {
            $mandataire = $settlement->getMandataire();
            $idMandataire = $mandataire->getId();
            if(!isset($allInvoices[$idMandataire])) {
                $allInvoices[$idMandataire] = array(
                    'ownerLabel' => $mandataire->getOwner()->getLabel(),
                    'clientLabel' => $mandataire->getClient()->getLabel(),
                    'settlements' => array(),
                    'amountHt' => 0,
                    'amountTva' => 0,
                    'amountTtc' => 0,
                );
            }
            $allInvoices[$idMandataire]['settlements'][] = $settlement;
            $allInvoices[$idMandataire]['amountHt'] += $settlement->getAmountHt();
            $allInvoices[$idMandataire]['amountTva'] += $settlement->getAmountTva();
            $allInvoices[$idMandataire]['amountTtc'] += $settlement->getAmount();
        }

        return $allInvoices;
    }

    public function prepareInvoice(Mandataire $mandataire, $allSettlements, $allRecords ) {
        $amountHt = $amountTtc = 0;
        foreach($allSettlements as $settlement) {
            $amountHt += $settlement->getAmountHt();
            $amountTtc += $settlement->getAmount();
        }

        $invoice = new Invoice($mandataire, $amountHt, $amountTtc);

        foreach($allSettlements as $settlement) {
            $invoice->addSettlement($settlement);
        }

        foreach($allRecords as $record) {
            $invoice->addRecord($record);
        }

        return $invoice;
    }

    public function confirmInvoice(Mandataire $mandataire, &$listeEmails) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $recordManager = $this->container->get('apr_mandataire.record_manager');

        $allSettlements = $settlementsManager->loadSettlementsForInvoicing($mandataire);
        $allRecords = $recordManager->loadMandataireRecords($mandataire->getOwner(), $mandataire, true);
        $allOperations = $recordManager->buildMandataireOperations($mandataire->getOwner(), $mandataire, $allRecords);

        $invoice = $this->prepareInvoice($mandataire, $allSettlements, $allRecords);

        $this->generatePdfInvoice($invoice, $allSettlements, $allOperations);

        $this->persistAndFlush($invoice);

        array_push($listeEmails, $this->getEmailConfirmationInvoice($mandataire, $invoice, $allSettlements));
    }

    public function generatePdfInvoice(Invoice $invoice, $allSettlements, $allOperations)
    {
        // ToDo: Revoir TCPDF pour mise en page propre => Fondative reveiw html page
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $knpSnappyPdf =  $this->container->get('knp_snappy.pdf');

        $mandataire = $invoice->getMandataire();

        $html = $this->container->get('templating')->render('AprMandataireBundle:Invoice:main.html.twig', array(
            'invoice' => $invoice,
            'baseurl' => $this->container->getParameter('baseUrl'),
            'ownerIsCooperons' => $cooperonsManager->isCorporateCooperons($mandataire->getOwner()->getCorporate()),
            'summaryTva' => $settlementsManager->summaryTva($mandataire, $allSettlements),
            'operations' => $allOperations,
            'endBalance' => $invoice->getEndBalance(),
        ));

        $uploadsPath = $this->container->getParameter('uploadsPath');
        $filePath = $uploadsPath. 'Invoices/' . $invoice->getFileName();
        $knpSnappyPdf->generateFromHtml($html, $filePath);
    }

    public function getEmailConfirmationInvoice(Mandataire $mandataire, Invoice $invoice, $allSettlements, $extraMsg = ''){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire, $extraMsg);
        $mailParam['body']['parameter']['invoice'] = $invoice;
        $mailParam['body']['parameter']['allSettlements'] = $allSettlements;
        $mailParam['subject'] = "Votre facture est disponible (".$mandataire->getLabel().")";
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationInvoice.html.twig';
        return $mailParam;
    }


}

?>
