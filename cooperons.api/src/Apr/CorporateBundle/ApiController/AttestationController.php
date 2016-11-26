<?php
/**
 * This file defines the Attestation controller in the Bundle CorporateBundle for REST API
 *
 * @category CorporateBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\CorporateBundle\ApiController;

use Apr\CoreBundle\Tools\Tools;
use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiFileResponse;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class AttestationController for API services
 *
 * @category CorporateBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("Attestation")
 */
class AttestationController extends Controller
{
    /**
     * Get attestations
     *
     * @ApiDoc(
     *     section="03.03. Attestation services",
     *     description="Get attestations",
     
     *     filters={
     *      {"name"="corporate", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate siren"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
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
        $collegeManager = $this->get('apr_corporate.college_manager');
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $siren = $request->get('corporate');

        if ($siren) {
            $corporate = $corporateManager->loadCorporateBySiren($siren);
            if ($corporate === null) {
                throw new ApiException(40029);
            }
        } else {
            $corporate = null;
        }

        $user = $this->getUser();
        $attestations = array();
        $data = array();

        $year = Tools::DateTime('Y') - 1;
        if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')) {
            if (Tools::DateTime('m') == 1) {
                $attestations = $collegeManager->buildYearlyAttestations(null, null, $year);
            }
        } else {
            $member = $user->getMember();
            $isAdministrator = $corporate && $corporate->getAdministrator()->getMember()->getId() === $member->getId();
            $attestations = $collegeManager->buildYearlyAttestations($corporate, $isAdministrator ? null : $member);
        }

        $data['year'] = $year;
        $data['attestations'] = $attestations;

        return new ApiResponse($data);
    }

    /**
     * Partial updating attestations
     *  <br><strong>- Validate Annual Attestations</strong>
     *  <br> Request format : [{"op": "validate", "path": "/"}]
     *
     * @ApiDoc(
     *     section="03.03. Attestation services",
     *     description="Partial updating attestations",
     
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
        $collegeManager = $this->get('apr_corporate.college_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'attestation');
        $emails = array();
        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'validate':
                    if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
                        throw new ApiException(403);
                    }
                    $year = Tools::DateTime('Y') - 1;

                    if (Tools::DateTime('m') == 1) {
                        $collegeManager->buildYearlyAttestations(null, null, $year, true, $emails);
                    }
                    break;
            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

    /**
     * Get attestation in json|pdf format
     * TODO not used : attestation retrieved by reference
     *
     * @ApiDoc(
     *     section="03.03. Attestation services",
     *     description="Get attestation in json|pdf format",
     
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40066"="Attestation not found"
     *            },
     *        403={
     *             "4036"="Denied access to Attestation"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAction($id, Request $request)
    {
        $collegeManager = $this->get('apr_corporate.college_manager');
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $attestation = $collegeManager->getAttestation($id);
        if ($attestation === null) {
            throw new ApiException(40066);
        }

        $member = $this->getUser()->getMember();
        if ($attestation->getMember() !== $member){
            $corporateManager->securityCheck($member, $attestation->getCorporate(), 4036);
        }

        $format = $request->get('_format');
        if ($format == 'pdf') {
            return new ApiFileResponse($attestation->getRef(), 'pdf', ApiFileResponse::ATTESTATION);

        } else {
            return new ApiResponse(array('attestation' => $attestation));
        }
    }

    /**
     * Download attestation
     *
     * @ApiDoc(
     *     section="03.03. Attestation services",
     *     description="Get attestation in pdf format",
     
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40066"="Attestation not found"
     *            },
     *        403={
     *             "4036"="Denied access to Attestation"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getDownloadAction($fileName)
    {
        // TODO Can not control access => download by id
        return new ApiFileResponse($fileName, 'pdf', ApiFileResponse::ATTESTATION);
    }

}