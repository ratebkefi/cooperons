<?php
/**
 * This file defines the Api Invitation controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\UserBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\ProgramBundle\Form\Type\MemberType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/*
 * Dans ce controlleur, userId signifie memberProgramId - mais pour communication extérieure ...
 */

/**
 * @RouteResource("Invitation")
 */
class ApiInvitationController extends Controller {

    /**
     * Return a collection of Invitation
     *
     * @ApiDoc(
     *     section="15. Invitation services",
     *     description="Return a collection of Invitation",
     *     statusCodes={
     *        400={ 
     *              "40044"    = "Invitation not found"
     *            },
     *        401={ 
     *              "4011"= "Invalid API Key",
     *              "4015"= "Permission not ganted. Expired program"
     *              },
     *        500={
     *            "5001"="An internal error has occured"
     *            },
     *        200={
     *            "200"="The request has succeeded"
     *            }
     *     }
     * )
     */
    public function getAction($token) {
        $programManager = $this->get('apr_program.program_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');
        $memberManager = $this->get('apr_program.member_manager');
        $cooperonsManager = $this->get('apr_admin.cooperons_manager');
        $programId = $this->get('security.context')->getToken()->getAttribute('APIKeyProgramId');
        $program = $programManager->loadProgramById($programId);
        $invitation = $invitationManager->loadInvitationByToken($token, $program);

        if (!$invitation) {
            throw new ApiException(40044);
        }
        $isCooperons = $cooperonsManager->isCorporateCooperons($program->getCorporate());
        $sponsor = $invitation->getSponsor();
        $data = $memberManager->buildAPIJsonReturnData($sponsor, $isCooperons);
        $data ['invitation'] = array(
            'token' => $invitation->getToken(),
            'email' => $invitation->getEmail(),
            'lastName' => $invitation->getLastName(),
            'firstName' => $invitation->getFirstName(),
        );

        return new ApiResponse($data);
    }

    /**
     * Create a new invitation
     *
     * @ApiDoc(
     *     section="15. Invitation services",
     *     description="Create a new invitation",
     *     requirements={
     *          {
     *          "name"="userId",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Your Id for the Sponsor"
     *          },
     *      },
     *      input = {
     *           "class"="Apr\ProgramBundle\Form\Type\MemberType",
     *           "name"="",
     *      },
     *     parameters={
     *      {"name"="sendMail",  "dataType"="boolean", "required"=false, "description"="Set to true if invitations email should be sent"},
     *      {"name"="mailInvitation", "dataType"="string", "required"=false, "description"="Code of the invitation email template to use for this invitation - if different from Default or the one set with PUT member."}
     *     },
     *     statusCodes={
     *        400={
     *              "4000"="Failed data validation",
     *              "4001"="No parameter input",
     *            },
     *        401={ 
     *              "4011"= "Invalid API Key",
     *              "4015"= "Permission not ganted. Expired program"
     *              },
     *        500={
     *            "5001"="An internal error has occured"
     *            },
     *        200={
     *            "200"="The request has succeeded"
     *            }
     *     }
     * )
     */
    public function postAction(Request $request, $userId) {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participatesto_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');
        $cooperonsManager = $this->get('apr_admin.cooperons_manager');
        $memberManager = $this->get('apr_program.member_manager');
        $validator = $this->get('api.form.validator');

        $program = $programManager->loadProgramById($this->get('security.context')->getToken()->getAttribute('APIKeyProgramId'));
        $participate = $participatesToManager->loadParticipatesToByMemberProgramId($program, $userId);

        if ($participate === null) {
            throw new ApiException(40042);
        }
        $data = $request->request->all();
        $data['sendMail'] = $request->request->get('sendMail');
        $data['mailInvitation'] = $request->request->get('mailInvitation');

        if ($validator->validateData(new MemberType(), $data)) {

            if (isset($data['sendMail']) && $data['sendMail']) {
                $listeEmails = array();
            } else {
                $listeEmails = null;
            }
            $invitations = $invitationManager->createMultipleInvitations($participate, array(0 => $data), $listeEmails, true);

            if (isset($invitations['error'])){
                $params = array('EMAIL'=>$data['sendMail'], 'PROGRAM'=>$program->getLbel());
                throw new ApiException(40021, null, $params);
            }
            $mailsManager = $this->get('apr_user.mailer');
            $mailsManager->sendMails($listeEmails);
            $isCooperons = $cooperonsManager->isCorporateCooperons($program->getCorporate());
            $sponsor = $invitations[0]->getSponsor();
            $data = $memberManager->buildAPIJsonReturnData($sponsor, $isCooperons);
            $data ['invitation'] = array(
                'token' => $invitations[0]->getToken(),
                'email' => $invitations[0]->getEmail(),
                'lastName' => $invitations[0]->getLastName(),
                'firstName' => $invitations[0]->getFirstName(),
            );
            return new ApiResponse(count($invitations) ? $data : null);
        } else {
            $errors = $validator->getErrors();
            throw new ApiException(4000, array('errors' => $errors));
        }
    }

}