<?php
/**
 * This file defines the Payment controller in the Bundle MandataireBundle for REST API
 *
 * @category MandataireBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\MandataireBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class PaymentController for API services
 *
 * @category MandataireBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("Payment")
 */
class PaymentController extends Controller
{
    /**
     * Get payments
     *
     * @ApiDoc(
     *     section="04.02. Payment services",
     *     description="Get payments",
     
     *     requirements={
     *     },
     *     filters={
     *      {"name"="mandataireId", "dataType"="string", "required"=true, "description"="Id of mandataire"},
     *      {"name"="status", "dataType"="string", "required"=false, "description"="Payment status"},
     *      {"name"="paymentMode", "dataType"="string", "required"=true, "description"="Mode of payment"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            "40039"="Invalid payment status(status available = {standby, payed, to be complited...})",
     *            "40040"="Invalid payment mode(status available = {virement to be complited...})",
     *            },
     *        403={
     *            "4032"="Denied access to payments",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction(Request $request)
    {
        // TODO Use Symfony security component after upgrading version to 2.7
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(4033);
        }

        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataireId = $request->get('mandataireId');
        $status = trim($request->get('status')) ? trim($request->get('status')) : null;
        $paymentMode = trim($request->get('paymentMode')) ? trim($request->get('paymentMode')) : null;

        if ($mandataireId) {
            $mandataire = $mandataireManager->loadMandataireById($mandataireId);
            if ($mandataire == null) {
                throw new ApiException(40025);
            }
        } else {
            $mandataire = null;
        }
        if ($status !== null && !in_array($status, array('standby', 'payed'))) {
            throw new ApiException(40039);
        }
        if ($paymentMode !== null && !in_array($paymentMode, array('virement', 'CB'))) {
            throw new ApiException(40040);
        }

        $payments = $paymentsManager->loadPayments($mandataire, $status, $paymentMode);

        return new ApiResponse(array('payments' => $payments));
    }

    /**
     * Delete Payment
     *
     * @ApiDoc(
     *     section="04.02. Payment services",
     *     description="Delete Payment",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of payment to be deleted"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40036"="Payment not found",
     *            "40037"="Payment not in «standby» status"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function deleteAction($id)
    {
        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $payment = $paymentsManager->loadPaymentById($id);

        if ($payment == null) {
            throw new ApiException(40036);
        }

        $mandataireManager->securityCheck($this->getUser(), $payment->getMandataire());

        if ($payment->getStatus() != 'standby') {
            throw new ApiException(40037);
        }

        $paymentsManager->removeAndFlush($payment);

        return new ApiResponse();
    }

    /**
     * Partial updating payments
     *  <br><strong>- Confirm payments</strong>
     *  <br> Request format : [{"op": "confirm", "path": "/", "payment": "12"}]
     *
     * @ApiDoc(
     *     section="04.02. Payment services",
     *     description="Partial updating payments",
     
     *     statusCodes={
     *        204={
     *            "204"="The resource is updated"
     *            },
     *        400={
     *            "40036"="Payment not found",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function cpatchAction(Request $request)
    {
        // TODO use Security component of symfony after upgrading to version 2.7
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'payment');
        $emails = array();

        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'confirm':
                    $payment = $paymentsManager->loadPaymentById($operation->payment);
                    if ($payment == null) {
                        throw new ApiException(40036);
                    }
                    $paymentsManager->validatePayment($payment->getMandataire(), $payment, $emails);
                    break;
                case 'cancel':
                    $payment = $paymentsManager->loadPaymentById($operation->payment);
                    if ($payment == null) {
                        throw new ApiException(40036);
                    }
                    $paymentsManager->cancelPayment($payment);
                    break;
            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }


    /**
     * Export Payments in Xls file
     *
     * @ApiDoc(
     *     section="04.02. Payment services",
     *     description="Export Payments",
     
     *     requirements={
     *      {"name"="ids", "dataType"="array(integer)", "required"=true, "description"="Ids of Payments to be exported"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40031"="Payments ids list is required"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function exportAction(Request $request)
    {
        $idsPayments = json_decode($request->get('ids'));
        if (count($idsPayments) == 0) {
            throw new ApiException(40031);
        }

        $response = new Response();
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET");
        $response->headers->set('Access-Control-Allow-Headers', '*');
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="payments.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $payments = array();
        foreach ($idsPayments as $idPayment) {
            $payment = $paymentsManager->loadPaymentById($idPayment);
            array_push($payments, $payment);
        }
        $objPHPExcel = $paymentsManager->prepareTransferFile($payments);

        $response->sendHeaders();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        return $response;
    }


    /**
     * Get net balance payments
     *
     * @ApiDoc(
     *     section="04.02. Payment services",
     *     description="Get net balance payments",
     
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *     }
     * )
     */
    public function cgetNetbalancesAction()
    {
        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $mailsManager = $this->get('apr_user.mailer');

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $emails = array();
        $netBalancePayments =  $paymentsManager->buildNetBalancePayments($emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse(array('netbalances' => $netBalancePayments));
    }

}