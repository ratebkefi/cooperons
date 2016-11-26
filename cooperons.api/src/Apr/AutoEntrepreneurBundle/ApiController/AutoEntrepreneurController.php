<?php

/**
 * This file defines the AutoEntrepreneur controller in the Bundle AutoEntrepreneurBundle for REST API
 *
 * @category AutoEntrepreneurBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\AutoEntrepreneurBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\AutoEntrepreneurBundle\Form\Type\AutoEntrepreneurExternalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AutoEntrepreneurController for API services
 *
 * @category AutoEntrepreneurBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("AutoEntrepreneur")
 */
class AutoEntrepreneurController extends Controller
{

    /**
     * Register new AutoEntrepreneur
     *
     * @ApiDoc(
     *     section="05.01. AutoEntrepreneur services",
     *     description="Register new autontrepreneur",
     
     *     requirements={
     *      {"name"="userId", "dataType"="integer", "required"=true, "description"="user id"},
     *      },
     *    input={
     *      "class"="Apr\AutoEntrepreneurBundle\Form\Type\AutoEntrepreneurExternalType",
     *      "name"=""
     *    },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          403={
     *            "403"="Denied access",
     *            },
     *          400={
     *             "4000"="Failed data validation",
     *             "40076"="User not found"
     *           },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        // TODO use Security Component of symfony after upgrading to 2.7
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $userManager = $this->get('apr_user.user_manager');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $formValidator = $this->get('api.form.validator');
        $mailsManager = $this->get('apr_user.mailer');


        $userId = $request->request->get('userId');
        $user = $userManager->loadUserById($userId);

        if ($user === null) {
            throw new ApiException(40076);
        }

        $data = $request->request->all();

        if ($formValidator->validateData(new AutoEntrepreneurExternalType(), $data)) {
            $emails = array();
            $autoEntrepreneur = $autoEntrepreneurManager->editAutoEntrepreneur($user->getMember(), $formValidator->getData(), $emails);
            if(isset($data['typeActivation']) && $data['typeActivation'] == 'now') $autoEntrepreneurManager->activate($autoEntrepreneur);
            $mailsManager->sendMails($emails);

            return new ApiResponse();
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
    }

    /**
     * Partial updating AutoEntrepreneur
     * <br><strong>- Resend contract invitation </strong>
     * <br> Request format : [{"op": "bank", "path": "/"}]
     * @ApiDoc(
     *     section="05.01. AutoEntrepreneur services",
     *     description="Partial updating AutoEntrepreneur",
     *     views = {"rest"},
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            "40079"="autoEntrepreneur not found",
     *
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function patchAction($id, Request $request)
    {
        $patchValidator = $this->get('api.patch.data.format.validator');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $mailsManager = $this->get('apr_user.mailer');

        // TODO use Security Component of symfony after upgrading to 2.7
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'auto_entrepreneur');

        $autoEntrepreneur = $autoEntrepreneurManager->loadAutoEntrepreneurById($id);

        if ($autoEntrepreneur === null) {
            throw new ApiException(40079);
        }

        $emails = array();

        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'bank':
                    $autoEntrepreneurManager->updateBankAccount($autoEntrepreneur, $patchOperation);
                    break;
                case 'external':
                    $autoEntrepreneurManager->updateExternal($autoEntrepreneur, $patchOperation);
                    break;
                case 'activate':
                    $autoEntrepreneurManager->activate($autoEntrepreneur, true, $emails);
                    break;
                case 'reschedule':
                    $autoEntrepreneurManager->rescheduleQuarterlyTaxation($autoEntrepreneur, $emails);
                    break;
                case 'quarterly':
                    $autoEntrepreneurManager->createQuarterlyTaxation($autoEntrepreneur, $patchOperation->amount, $emails);
                    break;
            }
        }
        $mailsManager->sendMails($emails);
        return new ApiResponse();

    }

    /**
     * Get autoEntrepreneur party
     *
     * @ApiDoc(
     *     section="05.01. AutoEntrepreneur services",
     *     description="Get autoentrepreneur party",
     *     views = {"rest"},
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40315"="Denied access to auto entrepreneur",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getPartyAction($id)
    {
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $autoEntrepreneur = $autoEntrepreneurManager->loadAutoEntrepreneurById($id);
        if ($autoEntrepreneur === null) {
            throw new ApiException(400102);
        }

        if ($autoEntrepreneur->getMember() !== $this->getUser()->getMember()
            && !$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')
        ) {
            throw new ApiException(40315);
        }

        return new ApiResponse(array('party' => $autoEntrepreneur->getParty()));
    }
}
