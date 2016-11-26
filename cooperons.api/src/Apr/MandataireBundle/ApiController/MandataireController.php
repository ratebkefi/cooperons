<?php
/**
 * This file defines the mandataire controller in the Bundle MandataireBundle for REST API
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
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MandataireController for API services
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
 * @RouteResource("mandataire")
 */
class MandataireController extends Controller
{
    /**
     * Returns mandataire informations
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire informations",
     
     *     requirements={},
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function cgetInfosAction()
    {
        $user = $this->getUser();
        $member = $user->getMember();
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $data = $mandataireManager->getInfosMandataireAllCollaborators($member);

        return new ApiResponse($data);
    }

    /**
     * Get mandataire
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        403={
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        return new ApiResponse(array('mandataire' => $mandataire));
    }


    /**
     * Get mandataire contract
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire contract",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        403={
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getContractAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        return new ApiResponse(array('contract' => $mandataire->getContract()));

    }

    /**
     * Get mandataire operations
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire operations",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getOperationsAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $recordManager = $this->get('apr_mandataire.record_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $member = $this->getUser()->getMember();
        $mandataireManager->securityCheck($this->getUser(), $mandataire);
        $contract = $mandataire->getContract();
        $party = $contract ? $contract->getAuthorizedParty($member) : $mandataire->getClient();

        $allRecords = $recordManager->loadMandataireRecords($party, $mandataire);
        $operations = $recordManager->buildMandataireOperations($party, $mandataire, $allRecords);

        return new ApiResponse(array('operations' => $operations));

    }


    /**
     * Get mandataire payments
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire payments",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="program id"},
     *     },
     *     filters={
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
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getPaymentsAction($id, Request $request)
    {
        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);


        $status = trim($request->get('status')) ? trim($request->get('status')) : null;
        $paymentMode = trim($request->get('paymentMode')) ? trim($request->get('paymentMode')) : null;

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
     * Get mandataire settlements
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get waiting settlements",
     
     *     requirements={
     *       {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     *     },
     *     filters={
     *       {"name"="status", "dataType"="string", "required"=false, "description"="Payment status"},
     *       {"name"="isProgram", "dataType"="string", "required"=false, "description"="is mandataire for program"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            "40038"="Invalid settlement status(status available = {'waiting', 'waitingForNotify', 'waitingForPayment', 'settled', 'released'})",
     *            },
     *        403={
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getSettlementsAction($id, Request $request)
    {
        $settlementsManager = $this->get('apr_mandataire.settlements_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $status = $request->get('status');
        $isProgram = $request->get('isProgram');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        if ($status !== null && !in_array($status, array('waiting', 'waitingForNotify', 'waitingForPayment', 'settled', 'released'))) {
            throw new ApiException(40038);
        }

        $settlements = $settlementsManager->loadSettlements($mandataire, $status, $isProgram);

        return new ApiResponse(array('settlements' => $settlements));
    }


    /**
     * Partial updating mandataire
     *  <br><strong>- Update min deposit</strong>
     *  <br> Request format : [{"op": "replace", "path": "/mindeposit", "value": 500}]
     *  <br><strong>- Confirm invoice </strong>
     *  <br> Request format : [{"op": "confirm", "path": "/invoice"}]
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Partial updating mandataire",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     *     },
     *     statusCodes={
     *        204={
     *            "204"="The resource is updated"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            },
     *        403={
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function patchAction($id, Request $request)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'mandataire');
        $emails = array();

        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'replace':
                    if ($operation->path === '/mindeposit') {
                        $mandataireManager->securityCheck($this->getUser(), $mandataire, true, false);
                        $mandataireManager->updateMinDepot($mandataire, $operation->value, $emails);
                    }
                    break;
                case 'confirm':
                    if ($operation->path === '/invoice') {
                        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
                            throw new ApiException(403);
                        }
                        $invoicesManager = $this->get('apr_mandataire.invoices_manager');
                        $invoicesManager->confirmInvoice($mandataire, $emails);
                    }
                    break;
            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

    /**
     * Get mandataire owner
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire owner",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getOwnerAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        return new ApiResponse(array('owner' => $mandataire->getOwner()));

    }

    /**
     * Get mandataire client
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire client",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"}
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getClientAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        return new ApiResponse(array('client' => $mandataire->getClient()));

    }

    /**
     * Get payment server parameters
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get data for new payment",
     
     *      requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"}
     *      },
     *     parameters={
     *        {"name"="amount", "dataType"="float", "required"=true, "description"="Amount"},
     *      },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *     }
     * )
     */
    public function newPaymentAction($id, Request $request)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $paymentsManager = $this->get('apr_mandataire.payments_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);

        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        $amount = $request->get('amount');
        $env = $this->container->get('kernel')->getEnvironment();
        $data = array();
        if ($env != 'dev') {
            $data['params'] = $paymentsManager->getPaymentParams($mandataire, $amount);
            $data['url'] = $data['params']['SERVEUR_OK'];
            unset($data['params']['SERVEUR_OK']);
        }

        return new ApiResponse(array('server' => $data));
    }

    /**
     * Create payment
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Create payment",
     
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"}
     *      },
     *     parameters={
     *        {"name"="amount", "dataType"="float", "required"=true, "description"="Amount"},
     *     },
     *     statusCodes={
     *        204={
     *            "200"="The request has succeeded"
     *            },
     *        204={
     *            "204"="The resource is created"
     *            },
     *        400={
     *            "40025"="Mandataire not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function postPaymentAction($id, Request $request)
    {
        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $amount = $request->get('amount');

        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        $payment = $paymentsManager->createPayment($mandataire, 'virement', $amount);

        return new ApiResponse(array('payment' => $payment), 201);
    }


    /**
     * Get mandataire records
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire client",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        403={
     *            "40310"="Denied access to mandataire",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getRecordsAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $recordManager = $this->get('apr_mandataire.record_manager');
        $paymentsManager = $this->get('apr_mandataire.payments_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);
        $mandataireManager->securityCheck($this->getUser(), $mandataire);

        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        $contract = $mandataire->getContract();
        $member = $this->getUser()->getMember();
        $party = $contract ? $contract->getAuthorizedParty($member): $mandataire->getClient();
        $result = array();
        if ($mandataire == $party->getMandataire() && $party->getAutoEntrepreneur()) {
            $result = $recordManager->buildRecords($party);
            $result['provision'] = $paymentsManager->calculateProvision($party);
        }

        return new ApiResponse($result);
    }


    /**
     * Get get authorized party
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get mandataire client",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAuthorizedpartyAction($id)
    {
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);

        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        $contract = $mandataire->getContract();
        $member = $this->getUser()->getMember();
        $party = null;
        if ($contract) {
            $party = $contract->getAuthorizedParty($member);
        } else {
            $client = $mandataire->getClient();
            if (!is_null($client) && $client->isAuthorized($member)) {
                $party = $client;
            }
        }

        return new ApiResponse(array('party' => $party));
    }

    /**
     * Get pending invoices
     *
     * @ApiDoc(
     *     section="04.01. mandataire management",
     *     description="Get pending invoices",
     *      requirements={
     *           {"name"="id", "dataType"="integer", "required"=true, "description"="Id of mandataire"},
     *      },
     
     *
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        400={
     *            "40025"="Mandataire not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *     }
     * )
     */
    public function getInvoiceAction($id)
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $invoicesManager = $this->get('apr_mandataire.invoices_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $settlementsManager = $this->get('apr_mandataire.settlements_manager');
        $recordManager = $this->get('apr_mandataire.record_manager');
        $cooperonsManager = $this->get('apr_admin.cooperons_manager');

        $mandataire = $mandataireManager->loadMandataireById($id);

        if ($mandataire == null) {
            throw new ApiException(40025);
        }

        $allSettlements = $settlementsManager->loadSettlementsForInvoicing($mandataire);
        $allRecords = $recordManager->loadMandataireRecords($mandataire->getOwner(), $mandataire, true);
        $allOperations = $recordManager->buildMandataireOperations($mandataire->getOwner(), $mandataire, $allRecords);

        $invoice = $invoicesManager->prepareInvoice($mandataire, $allSettlements, $allRecords);

        return new ApiResponse(array(
            'invoice' => $invoice,
            'ownerIsCooperons' => $cooperonsManager->isCorporateCooperons($mandataire->getOwner()->getCorporate()),
            'summaryTva' => $settlementsManager->summaryTva($mandataire, $allSettlements),
            'operations' => $allOperations,
            'endBalance' => $invoice->getEndBalance(),
        ));
    }

}
