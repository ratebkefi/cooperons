<?php

/**
 * This file defines the Settlement controller in the Bundle MandataireBundle for REST API
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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class SettlementController for API services
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
 * @RouteResource("Settlement")
 */
class SettlementController extends Controller {

    /**
     * Get settlements
     *
     * @ApiDoc(
     *     section="04.03. Settlement services",
     *     description="Get waiting settlements",
     
     *     requirements={
     *     },
     *     filters={
     *      {"name"="mandataireId", "dataType"="string", "required"=true, "description"="Id of mandataire"},
     *       {"name"="status", "dataType"="string", "required"=false, "description"="Payment status"},
     *       {"name"="isProgram", "dataType"="string", "required"=false, "description"="is mandataire for program"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40025"="Mandataire not found",
     *            "40038"="Invalid settlement status('waiting', 'waitingForNotify', 'waitingForPayment', 'settled', 'released')",
     *            },
     *        403={
     *            "4032"="Denied access to settlements",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction(Request $request) {
        // TODO check access right by Symfony security module after upgrade
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(4032);
        }
        $settlementsManager = $this->get('apr_mandataire.settlements_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');


        $mandataireId = $request->get('mandataireId');
        $status = trim($request->get('status')) ? trim($request->get('status')) : null;
        $isProgram = $request->get('isProgram');
        $mandataire = null;

        if ($mandataireId) {
            $mandataire = $mandataireManager->loadMandataireById($mandataireId);
            if ($mandataire == null) {
                throw new ApiException(40025);
            }
        }

        if ($status !== null && !in_array($status, array('waiting', 'waitingForNotify', 'waitingForPayment', 'settled', 'released'))) {
            throw new ApiException(40038);
        }

        $settlements = $settlementsManager->loadSettlements($mandataire, $status, $isProgram);
        return new ApiResponse(array('settlements' => $settlements));
    }

    /**
     * Delete Settlement
     *
     * @ApiDoc(
     *     section="04.03. Settlement services",
     *     description="Delete Settlement",
     
     *     requirements={
     *      {"name"="idSettlement", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of settlement"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        400={
     *            "40030"="Settlement not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function deleteAction($idSettlement) {
        $settlementsManager = $this->get('apr_mandataire.settlements_manager');
        $mandataireManager = $this->get('apr_mandataire.mandataire_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $settlement = $settlementsManager->loadSettlementById($idSettlement);

        if ($settlement == null) {
            throw new ApiException(40030);
        }

        $mandataire = $settlement->getMandataire();
        $mandataireManager->securityCheck($this->getUser(), $mandataire);
        $emails = array();
        $settlementsManager->cancelSettlement($mandataire, $settlement, $emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse();
    }

}