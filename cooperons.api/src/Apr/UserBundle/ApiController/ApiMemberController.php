<?php
/**
 * This file defines the Api Member controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\UserBundle\ApiController;

use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Apr\ProgramBundle\Form\Type\MemberType;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/*
 * Dans ce controlleur, userId signifie memberProgramId - mais pour communication extérieure ...
 */

/**
 * @RouteResource("Member")
 */
class ApiMemberController extends Controller
{

    /**
     * Get member
     *
     * @ApiDoc(
     *     section="01. Member services",
     *     description="Returns a collection of Members",
     *     requirements={
     *          {
     *          "name"="userId",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Your Id for the Member"
     *          }
     *      },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40042"= "Participate to program not found",
     *            },
     *        401={
     *            "4011"= "Invalid API Key",
     *            "4015"= "Permission not ganted. Expired program"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAction($userId)
    {

        $programManager = $this->container->get('apr_program.program_manager');
        $participatesToManager = $this->container->get('apr_program.participatesto_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $memberManager = $this->get('apr_program.member_manager');

        $programId = $this->get('security.context')->getToken()->getAttribute('APIKeyProgramId');
        $program = $programManager->loadProgramById($programId);
        $participate = $participatesToManager->loadParticipatesToByMemberProgramId($program, $userId);

        if ($participate === null) {
            throw new ApiException(40042);
        }
        $isCooperons = $cooperonsManager->isCorporateCooperons($program->getCorporate());
        return new ApiResponse(array('member' => $memberManager->buildAPIJsonReturnData($participate, $isCooperons)));
    }

    /**
     * Create a new Member
     *
     * @ApiDoc(
     *     section="01. Member services",
     *     description="Create a new Member",
     *     parameters={
     *        {"name"="userId", "dataType"="integer", "required"=true, "description"="Your Id for the Member"},
     *        {"name"="tokenInvitation", "dataType"="string", "required"=false, "description"="Invitation token if any"},
     *     },
     *     input = {
     *           "class"="Apr\ProgramBundle\Form\Type\MemberType",
     *           "name"="",
     *      },
     *
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        401={
     *            "4011"= "Invalid API Key",
     *            "4015"= "Permission not ganted. Expired program"
     *            },
     *          400={
     *              "4000"="Failed data validation",
     *              "4001"="No parameter input",
     *              "40015"="Member with email #EMAIL# already exists in program #PROGRAM#",
     *              "40016"="Member with id #ID# already exists in program #PROGRAM#",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $programManager = $this->container->get('apr_program.program_manager');
        $participatesToManager = $this->container->get('apr_program.participatesto_manager');
        $memberManager = $this->get('apr_program.member_manager');
        $cooperonsManager = $this->get('apr_admin.cooperons_manager');
        $mailsManager = $this->get('apr_user.mailer');
        $formValidator = $this->get('api.form.validator');

        $programId = $this->get('security.context')->getToken()->getAttribute('APIKeyProgramId');
        $program = $programManager->loadProgramById($programId);
        $data = $request->request->all();

        if ($formValidator->validateData(new MemberType(), $data)) {
            $emails = array();
            $result = $participatesToManager->createParticipatesToWithData($program, $data, $emails);

            if (isset($result['error'])) {
                throw new ApiException($result['error'], null, $result['errorParams']);
            }
            $mailsManager->sendMails($emails);
            $isCooperons = $cooperonsManager->isCorporateCooperons($program->getCorporate());
            $memberData = $memberManager->buildAPIJsonReturnData($result['participatesTo'], $isCooperons);

            return new ApiResponse(array('member' => $memberData), 201);
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
    }

    /**
     * Partial updating an existing member
     * <br><strong>- Update code email</strong>
     * <br> Request format : [{"op": "replace", "path": "/mailInvitation", "codeMail":"default"}]
     * <br><strong>- Add points to participate </strong>
     * <br> Request format : [{"op": "add", "path": "/points", "labelOperation": "default", "info" : "Client TEST", "amount": "50"}]
     * @ApiDoc(
     *     section="01. Member services",
     *     description="Partial updating an existing member",
     *     requirements={
     *     {"name"="userId", "dataType"="integer", "requirement"="\d+", "description"="Your Id for the Member"},
     *     },
     *     statusCodes={
     *        400={
     *            "4007"= "Operation #OPERATION# not found in program #PROGRAM#",
     *            "40022"="Invitation email template not found in program #PROGRAM#",
     *            "40042"= "Participate to program not found",
     *            "40052"= "Email invitation code is required",
     *            },
     *        401={
     *            "4011"= "Invalid API Key",
     *            "4015"= "Permission not ganted. Expired program"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *        200={
     *            "200"="The request has succeeded"
     *            }
     *     }
     * )
     */
    public function patchAction($userId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participatesto_manager');
        $accountManager = $this->get('apr_program.account_points_history_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $programId = $this->get('security.context')->getToken()->getAttribute('APIKeyProgramId');
        $program = $programManager->loadProgramById($programId);
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'member');
        $emails = array();
        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'replace':
                    if ($patchOperation->path == '/mailInvitation') {
                        $participate = $participatesToManager->loadParticipatesToByMemberProgramId($program, $userId);
                        $codeMail = $patchOperation->codeMail;

                        if ($participate === null) {
                            throw new ApiException(40042);
                        }

                        if(!$codeMail){
                            throw new ApiException(40052);
                        }

                        $result = $participatesToManager->updateCodeMail($participate, $codeMail);
                        if (isset($result['error'])) {
                            throw new ApiException($result['error'], null, $result['errorParams']);
                        }
                    }
                    break;
                case 'add':
                    if ($patchOperation->path == '/points') {
                        $data = array(array('labelOperation' => $patchOperation->labelOperation, 'info' => $patchOperation->info, 'amount' => $patchOperation->amount));
                        $participatesTo = $participatesToManager->loadParticipatesToByMemberProgramId($program, $userId);

                        if ($participatesTo === null) {
                            throw new ApiException(40042);
                        }
                        $result = $accountManager->addPoints($participatesTo, $data, $emails);

                        if (isset($result['error'])) {
                            $params = array("OPERATION" => $patchOperation->labelOperation, "PROGRAM" => $program->getLabel());
                            throw new ApiException($result['error'], null, $params);
                        }
                        // TODO amountPoints in get action
                        //$data['amountPoints'] = $result['amount'];
                    }
                    break;
            }
        }

        $mailsManager->sendMails($emails);

        return new ApiResponse(array());
    }

}