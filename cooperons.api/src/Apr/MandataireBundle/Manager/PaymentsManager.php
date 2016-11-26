<?php

namespace Apr\MandataireBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ContractBundle\Entity\Party;
use Apr\MandataireBundle\Entity\Mandataire;
use Apr\MandataireBundle\Entity\Payment;

class PaymentsManager extends BaseManager
{

    protected $em;
    protected $container;
    private $request;
    private $router;
    private $paybox;
    private $frontRedirection;

    public function __construct(EntityManager $em, $container, Request $request, Router $router, $paybox, $frontRedirection)
    {
        $this->em = $em;
        $this->container = $container;
        $this->request = $request;
        $this->router = $router;
        $this->paybox = $paybox;
        $this->frontRedirection = $frontRedirection;
    }

    public function getRepository() {
        return $this->em->getRepository('AprMandataireBundle:Payment');
    }

    public function createPayment(Mandataire $mandataire, $mode = 'virement', $amount = 0, $authCode = null){
        if($mandataire->getLiquidationDate()) {
            $amount = -$mandataire->getDepot();
        }
        if($amount &&
            ($mode == 'virement' or ($mode == 'CB' && $this->checkCBPayment($mandataire, $authCode))) &&
            ($amount > 0 or is_null($mandataire->getStandByPaymentOut()))
        ) {
            $payment = new Payment($mandataire, $amount, $mode, $authCode);

            if($amount < 0) {
                $mandataire->setStandByPaymentOut($payment);
            }

            $this->persistAndFlush($payment);
            return $payment;
        } else {
            return null;
        }
    }

    public function cancelPayment(Payment $payment){
        $mandataire = $payment->getMandataire();
        if(!$mandataire->getLiquidationDate()) {
            if($mandataire->getStandByPaymentOut() == $payment) {
                $mandataire->setStandByPaymentOut(null);
                $this->persist($mandataire);
            }
            $this->removeAndFlush($payment);
        }
    }

    public function loadPaymentById($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Get payments
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param Mandataire|null $mandataire
     * @param null $status
     * @param null $mode
     * @return array
     */
    public function loadPayments(Mandataire $mandataire = null, $status = null, $mode = null){

        $params = array();
        $status === null ? null : $params['status'] = $status;
        $mandataire === null ? null : $params['mandataire'] = $mandataire;
        $mode === null ? null : $params['mode'] = $mode;
        return $this->getRepository()->findBy($params);
    }

    public function loadPaymentsByStatus(Mandataire $mandataire, $status = "standby"){
        if(!is_null($mandataire)) {
            return $this->getRepository()->findBy(array('mandataire' => $mandataire, 'status' => $status),
                array('createdDate' => 'DESC'));
        }
    }

    public function getWaitingVirements(Mandataire $mandataire = null){
        $params = array('status' => 'standby', 'mode' => 'virement');
        if(!is_null($mandataire)) $params['mandataire'] = $mandataire;
        return $this->getRepository()->findBy($params);
    }

    public function checkCBPayment($mandataire, $authCode) {
        if(!is_null($mandataire)) {
            return $this->getRepository()->checkCBPayment($mandataire, $authCode);
        }
    }

    public function validatePayment(Mandataire $mandataire, Payment $payment, &$listeEmails){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');
        $recordManager = $this->container->get('apr_mandataire.record_manager');
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        $payment->validate();
        if($payment->getStatus() == 'payed') {
            $recordManager->recordPayment($mandataire, $payment);

            if($cooperonsManager->isMandataireCooperons($mandataire)) $afterPayment = $cooperonsManager->afterPayment($mandataire, $payment, $listeEmails);
            $this->flush();

            if($payment->getMode() != 'debit') {
                // Emails de confirmation de prélèvements gérés par afterPayment ...
                array_push($listeEmails, $this->getEmailConfirmationPayment($mandataire, $payment));
                if($mandataire->getLiquidationDate()) {
                    $mandataireManager->liquidation($mandataire, $listeEmails);
                } elseif($payment->getAmount() > 0) {
                    $settlementsManager->updateWaitingSettlements($mandataire, $listeEmails);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function calculateProvision(Party $party) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $provision = $party->getTotalDepots();
        $autoEntrepreneur = $party->getAutoEntrepreneur();
        if($autoEntrepreneur) {
            $provision += ceil($settlementsManager->getTotalSettlementsForQuarterlyTaxation($autoEntrepreneur) *
                    $autoEntrepreneur->getParty()->getProvisionRate())/100;
        }
        return $provision;
    }

    public function buildNetBalancePayments(&$listeEmails) {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $contractManager = $this->container->get('apr_contract.contract_manager');
        $result = array();

        $coopAEManager->createSettlementFeeMandataire($listeEmails);

        $allParties = $contractManager->loadAllParties();
        foreach($allParties as $party) {
            if(!$party->isCooperons()) {
                $mandataire = $party->getMandataire();
                $depot = $mandataire->getDepot();
                $provision = $this->calculateProvision($party);

                if(is_null($mandataire->getStandByPaymentOut()) && ($depot > $provision)) {
                    $result[$mandataire->getId()] = array(
                        'label' => $party->getLabel(),
                        'depot' => $depot,
                        'provision' => $provision,
                        'amount' => $depot - $provision
                    );
                }
            }
        }
        return $result;
    }

    public function buildProvision(Mandataire $mandataire)
    {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $result = array('mandataire' => $mandataire, 'amountTtc' => 0, 'amountTva' => 0, 'amountHt' => 0, 'depot' => 0);

        $result['waitingSettlements'] = $settlementsManager->loadWaitingSettlements($mandataire);
        foreach( $result['waitingSettlements'] as $settlement) {
            if($settlement->isInvoiceable()) {
                $result['amountHt'] += $settlement->getAmountHt();
                $result['amountTva'] += $settlement->getAmount() - $settlement->getAmountHt();
                $result['amountTtc'] += $settlement->getAmount();
            } else {
                // Cas remboursement avances ...
                $result['depot'] += $settlement->getAmount();
            }
        }
        $result['depot'] += $mandataire->calculateDepot($result['depot']+$result['amountTtc']);

        $payment = $this->getWaitingVirements($mandataire);
        if($payment) {
            $payment = $payment[0];
        }
        $result['payment'] = $payment;

        $result['total'] = $result['amountTtc']+$result['depot'];

        return $result;
    }

    /*
     * Prélèvement ...
     */
    public function createDebitPayment(Mandataire $mandataire, $amount = 0){
        if(($amount < 0) && ($amount + $mandataire->getFreeDepot() >= 0)) {
            $payment = new Payment($mandataire, $amount, 'debit');
            $this->persist($payment);
            return $payment;
        } else {
            throw new ApiException(40089);
        }
    }

    public function getEmailConfirmationPayment(Mandataire $mandataire, Payment $payment, $extraMsg = ''){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire, $extraMsg);
        $mailParam['body']['parameter']['payment'] = $payment;
        if($payment->getAmount()>0) {
            $mailParam['subject'] = "Votre dépôt de ".number_format($payment->getAmount(),2,'.',' ')." € a bien été effectué";
        } else {
            $mailParam['subject'] = "Le paiement de ".number_format(-$payment->getAmount(),2,'.',' ')." € en votre faveur a bien été effectué";
        }
        $mailParam['body']['template'] = 'AprMandataireBundle:Emails:confirmationPayment.html.twig';
        return $mailParam;
    }

    public function prepareTransferFile($payments)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Coopérons")
            ->setLastModifiedBy("Coopérons")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("export virement");
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Date')
            ->setCellValue('B1', 'Compte')
            ->setCellValue('C1', 'Membre')
            ->setCellValue('D1', 'Mode de paiement')
            ->setCellValue('E1', 'Montant')
            ->setCellValue('F1', 'BIC')
            ->setCellValue('G1', 'IBAN');
        ;
        $i = 2;

        foreach ($payments as $payment) {
            $mandataire = $payment->getMandataire();
            $party = $mandataire->getClient();
            $autoEntrepreneur = $party->getAutoEntrepreneur();

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $payment->getCreatedDate()->format('d/m/Y'))
                ->setCellValue('B' . $i, $party->getLabel())
                ->setCellValue('C' . $i, $party->getMember()->getLabel())
                ->setCellValue('D' . $i, $payment->getMode())
                ->setCellValue('E' . $i, number_format($payment->getAmount(),2,'.',''))
                ->setCellValue('F' . $i, $autoEntrepreneur?$autoEntrepreneur->getBIC():null)
                ->setCellValue('G' . $i, $autoEntrepreneur?"''".$autoEntrepreneur->getIBAN():null)
            ;
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('');
        $objPHPExcel->setActiveSheetIndex(0);
        return $objPHPExcel;
    }

    public function LoadKey($keyfile, $pub = true, $pass = '')
    {
        // chargement de la clé (publique par défaut)

        $fp = $filedata = $key = FALSE;                         // initialisation variables
        $fsize = filesize($keyfile);                         // taille du fichier
        if (!$fsize) return FALSE;                             // si erreur on quitte de suite
        $fp = fopen($keyfile, 'r');                           // ouverture fichier
        if (!$fp) return FALSE;                                // si erreur ouverture on quitte
        $filedata = fread($fp, $fsize);                       // lecture contenu fichier
        fclose($fp);                                          // fermeture fichier
        if (!$filedata) return FALSE;                          // si erreur lecture, on quitte
        if ($pub)
            $key = openssl_pkey_get_public($filedata);        // recuperation de la cle publique
        else                                                    // ou recuperation de la cle privee
            $key = openssl_pkey_get_private(array($filedata, $pass));
        return $key;                                            // renvoi cle ( ou erreur )
    }

    // comme precise la documentation Paybox, la signature doit être
    // obligatoirement en dernière position pour que cela fonctionne

    public function GetSignedData($qrystr, &$data, &$sig)
    {          // renvoi les donnes signees et la signature

        $pos = strrpos($qrystr, '&');                         // cherche dernier separateur
        $data = substr($qrystr, 0, $pos);                     // et voila les donnees signees
        $pos = strpos($qrystr, '=', $pos) + 1;                 // cherche debut valeur signature
        $sig = substr($qrystr, $pos);                         // et voila la signature
        $sig = base64_decode(urldecode($sig));               // decodage signature
    }

    // $querystring = chaine entière retournée par Paybox lors du retour au site (méthode GET)
    // $keyfile = chemin d'accès complet au fichier de la clé publique Paybox

    public function PbxVerSign($qrystr, $keyfile)
    {                  // verification signature Paybox

        $key = $this->LoadKey($keyfile);                             // chargement de la cle
        if (!$key) return -1;                                  // si erreur chargement cle
        //  penser à openssl_error_string() pour diagnostic openssl si erreur
        $data = $sig = null;
        $this->GetSignedData($qrystr, $data, $sig);                  // separation et recuperation signature et donnees
        return openssl_verify($data, $sig, $key);             // verification : 1 si valide, 0 si invalide, -1 si erreur
    }

    public function getPaymentParams($mandataire, $amount)
    {
        // --------------- VARIABLES A MODIFIER ---------------

        // Ennonciation de variables
        $pbx_site = $this->paybox['site'];
        $pbx_rang = $this->paybox['rang'];
        $pbx_identifiant = $this->paybox['identifiant'];
        // Timestamp sur identifiant cmd - pour empêcher blocage Paybox sur pbx_cmd non unique ...
        $pbx_cmd = $mandataire->getId() . "-" . time();
        $pbx_porteur = $mandataire->getContract()->getClientCollaborator()->getMember()->getEmail();
        $pbx_total = $amount * 100;
        // Suppression des points ou virgules dans le montant
        $pbx_total = str_replace(",", "", $pbx_total);
        $pbx_total = str_replace(".", "", $pbx_total);

        $schemeAndHttpHost = $this->request->getSchemeAndHttpHost();

        // Paramétrage des urls de redirection après paiement
        //$pbx_effectue = $pbx_annule = $pbx_refuse = $schemeAndHttpHost.$this->router->generate('my_programs');
        $pbx_effectue = $pbx_annule = $pbx_refuse = null;

        // Paramétrage des urls de redirection après paiement
        $pbx_effectue = $pbx_annule = $pbx_refuse = $this->frontRedirection;

        // Paramétrage de l'url de retour back office site
        $pbx_repondre_a = $schemeAndHttpHost.$this->router->generate('call_response');
        // Paramétrage du retour back office site
        $pbx_retour = 'Amount:M;Ref:R;Auto:A;Error:E;Sign:K';

        // Connection à la base de données
        // mysql_connect...
        // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que lon renseigne dans la variable $keyTest;
        //$keyTest = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';
        $keyTest = $this->paybox['keyHMAC'];

        // --------------- TESTS DE DISPONIBILITE DES SERVEURS ---------------


        $serveurs = array('tpeweb.paybox.com', //serveur primaire
            'tpeweb1.paybox.com'); //serveur secondaire
        $serveurOK = "";
        //phpinfo(); <== voir paybox
        foreach ($serveurs as $serveur) {
            $doc = new \DOMDocument();
            $doc->loadHTMLFile('https://' . $serveur . '/load.html');
            $server_status = "";
            $element = $doc->getElementById('server_status');
            if($element){
                $server_status = $element->textContent;}
            if($server_status == "OK"){
                // Le serveur est prêt et les services opérationnels
                $serveurOK = $serveur;
                break;
            }
            // else : La machine est disponible mais les services ne le sont pas.
        }
        //curl_close($ch); <== voir paybox
        if (!$serveurOK) {
            throw new ApiException(5031);
            // die("Erreur : Aucun serveur n'a été trouvé");
        }

        if ($this->paybox['preprod']) {
            // Activation de l'univers de préproduction
            $serveurOK = 'preprod-tpeweb.paybox.com';
        }

        //Création de l'url cgi paybox
        $serveurOK = 'https://' . $serveurOK . '/cgi/MYchoix_pagepaiement.cgi';
        // echo $serveurOK;


        // --------------- TRAITEMENT DES VARIABLES ---------------

        // On récupère la date au format ISO-8601
        $dateTime = date("c");

        // On crée la chaîne à hacher sans URLencodage
        $msg = "PBX_SITE=".$pbx_site.
            "&PBX_RANG=".$pbx_rang.
            "&PBX_IDENTIFIANT=".$pbx_identifiant.
            "&PBX_TOTAL=".$pbx_total.
            "&PBX_DEVISE=978".
            "&PBX_CMD=".$pbx_cmd.
            "&PBX_PORTEUR=".$pbx_porteur.
            "&PBX_REPONDRE_A=".$pbx_repondre_a.
            "&PBX_RETOUR=".$pbx_retour.
            "&PBX_EFFECTUE=".$pbx_effectue.
            "&PBX_ANNULE=".$pbx_annule.
            "&PBX_REFUSE=".$pbx_refuse.
            "&PBX_HASH=SHA512".
            "&PBX_TIME=".$dateTime;
        // echo $msg;

        // Si la clé est en ASCII, On la transforme en binaire
        $binKey = pack("H*", $keyTest);

        // On calcule l'empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et //
        // la clé binaire
        // On envoi via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
        // Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne //
        // suivante
        // print_r(hash_algos());
        $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));

        // La chaîne sera envoyée en majuscule, d'où l'utilisation de strtoupper()
        // On crée le formulaire à envoyer
        // ATTENTION : l'ordre des champs est extrêmement important, il doit
        // correspondre exactement à l'ordre des champs dans la chaîne hachée
        return array(
            "SERVEUR_OK" => $serveurOK,
            "PBX_SITE" => $pbx_site,
            "PBX_RANG" => $pbx_rang,
            "PBX_IDENTIFIANT" => $pbx_identifiant,
            "PBX_TOTAL" => $pbx_total,
            "PBX_DEVISE" => "978",
            "PBX_CMD" => $pbx_cmd,
            "PBX_PORTEUR" => $pbx_porteur,
            "PBX_REPONDRE_A" => $pbx_repondre_a,
            "PBX_RETOUR" => $pbx_retour,
            "PBX_EFFECTUE" => $pbx_effectue,
            "PBX_ANNULE" => $pbx_annule,
            "PBX_REFUSE" => $pbx_refuse,
            "PBX_HASH" => "SHA512",
            "PBX_TIME" => $dateTime,
            "PBX_HMAC" => $hmac,
        );
    }

}