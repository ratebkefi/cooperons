<?php

namespace Apr\MandataireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller; 
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DOMDocument;
use Apr\CoreBundle\Tools\Tools;

class PaymentController extends Controller
{

    private function LoadKey( $keyfile, $pub=true, $pass='' ) {         // chargement de la clé (publique par défaut)

        $fp = $filedata = $key = FALSE;                         // initialisation variables
        $fsize =  filesize( $keyfile );                         // taille du fichier
        if( !$fsize ) return FALSE;                             // si erreur on quitte de suite
        $fp = fopen( $keyfile, 'r' );                           // ouverture fichier
        if( !$fp ) return FALSE;                                // si erreur ouverture on quitte
        $filedata = fread( $fp, $fsize );                       // lecture contenu fichier
        fclose( $fp );                                          // fermeture fichier
        if( !$filedata ) return FALSE;                          // si erreur lecture, on quitte
        if( $pub )
            $key = openssl_pkey_get_public( $filedata );        // recuperation de la cle publique
        else                                                    // ou recuperation de la cle privee
            $key = openssl_pkey_get_private( array( $filedata, $pass ));
        return $key;                                            // renvoi cle ( ou erreur )
    }

    // comme precise la documentation Paybox, la signature doit être
    // obligatoirement en dernière position pour que cela fonctionne

    private function GetSignedData( $qrystr, &$data, &$sig ) {          // renvoi les donnes signees et la signature

        $pos = strrpos( $qrystr, '&' );                         // cherche dernier separateur
        $data = substr( $qrystr, 0, $pos );                     // et voila les donnees signees
        $pos= strpos( $qrystr, '=', $pos ) + 1;                 // cherche debut valeur signature
        $sig = substr( $qrystr, $pos );                         // et voila la signature
        $sig = base64_decode( urldecode( $sig ));               // decodage signature
    }

    // $querystring = chaine entière retournée par Paybox lors du retour au site (méthode GET)
    // $keyfile = chemin d'accès complet au fichier de la clé publique Paybox

    private function PbxVerSign( $qrystr, $keyfile ) {                  // verification signature Paybox

        $key = $this->LoadKey( $keyfile );                             // chargement de la cle
        if( !$key ) return -1;                                  // si erreur chargement cle
        //  penser à openssl_error_string() pour diagnostic openssl si erreur
        $data = $sig = null;
        $this->GetSignedData( $qrystr, $data, $sig );                  // separation et recuperation signature et donnees
        return openssl_verify( $data, $sig, $key );             // verification : 1 si valide, 0 si invalide, -1 si erreur
    }

    /**
     * Fondative: Don't remove. It's used by payment form
     * @Route("/public/paybox/response", name="call_response")
     */
    public function callResponseAction()
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $paymentsManager = $this->get('apr_mandataire.payments_manager');

        $keyfile = $this->container->getParameter('paybox.keyfile');

        $request = $this->getRequest();

        $amount = $request->get('Amount')/100;
        $reference = $request->get('Ref');
        // Récupération du timestamp ...
        $arr = explode("-", $reference, 2);
        $idMandataire = $arr[0];
        $timestamp = $arr[1];

        $auto = $request->get('Auto');
        $error = $request->get('Error');
        if($error == "00000") {
            $first = strpos($_SERVER['REQUEST_URI'],'?');			// recherche le ?
            $qrystr = substr($_SERVER['REQUEST_URI'], $first+1);

            $CheckSig = $this->PbxVerSign($qrystr, $keyfile);

            if( $CheckSig == 1 )       {
                $emails = array();
                $mandataire = $mandataireManager->loadMandataireById($idMandataire);
                $payment = $paymentsManager->createPayment($mandataire, 'CB', $amount, $timestamp."#".$auto);
                if(!is_null($payment)) {
                    $paymentsManager->validatePayment($mandataire, $payment, $emails);

                    if (!empty($emails)) {
                        $mailsManager = $this->get('apr_user.mailer');
                        $mailsManager->sendMails($emails);
                    }
                }
            }
            else if( $CheckSig == 0 )  {
                // Signature invalide : donnees alterees ou signature falsifiee
            }
            else {
                // Erreur lors de la vérification de la signature
            }

        }

        return new JsonResponse(true);
    }

}

?>
