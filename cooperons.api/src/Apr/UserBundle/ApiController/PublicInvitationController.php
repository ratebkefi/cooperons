<?php
/**
 * This file defines the Invitation controller in the Bundle UserBundle for REST API
 *
 * @category UserBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\UserBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Apr\CoreBundle\Tools\Tools;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class PublicInvitationController for API services
 *
 * @category UserBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("Invitation")
 */
class PublicInvitationController extends Controller
{
    /**
     * Get invitation
     *
     * @ApiDoc(
     *     section="01.02. Invitation services",
     *     description="Get invitation",
     
     *     requirements={
     *      {"name"="token", "dataType"="string", "required"=true, "description"="Invitation token"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "40019"="Member / Invitation not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAction($token)
    {
        $participatesToManager = $this->get('apr_program.participatesto_manager');
        $coopPlusManager = $this->get('apr_admin.coop_plus_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');

        $participate = $participatesToManager->loadParticipatesToByToken($token);
        $data = array();

        if (!$participate) {
            $program = $coopPlusManager->loadProgramPlus();
            $invitation = $invitationManager->loadInvitationByToken($token, $program);
            if (!$invitation) {
                throw new ApiException(40019);
            } else {
                $data['type'] = 'invitation';
                $data['invitation'] = $invitation;
            }

        } else {    
            $program = $participate->getProgram();
            $college = $participate->getMember()->getCollege();
            $data['type'] = 'participate';
            $data['participate'] = $participate;
            $data['college'] = $college;
            $corporate = $college ? $college->getCorporate() : null;
            $data['delegate'] = $college ? $corporate->getDelegate() : null;
        }

        $data['token'] = $token;
        $data['program']=$program;
        $data['isInvitationPlus'] =  $coopPlusManager->isProgramPlus($program);
        $data['corporate'] = $program->getCollaborator()->getCorporate();
        $data['operations'] = $program->getAllOperations();
        $data['htmlOperations'] = $this->render('AprProgramBundle:Legal:cg_program_func_view_operations.html.twig',
            array('program' => $program));

        return new ApiResponse($data);
    }

    /**
     * Create invitations. Takes list of emails [emailsFilleuls] OR [firstname, lastname, email]
     *
     * @ApiDoc(
     *     section="01.02. Invitation services",
     *     description="Create invitations",
     
     *     parameters={
     *      {"name"="token", "dataType"="string", "required"=true, "description"="ParticipatesTo token"},
     *      {"name"="typeInvitation", "dataType"="string", "required"=false, "description"="Invitation type"},
     *      {"name"="collaboratorId", "dataType"="integer", "required"=false, "description"="Collaborator id"},
     *      {"name"="emailsFilleuls", "dataType"="list", "required"=true, "description"="List of emails separated by comma[,]"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Invitation token"},
     *      {"name"="firstname", "dataType"="string", "required"=true, "description"="Invitation token"},
     *      {"name"="lastname", "dataType"="string", "required"=true, "description"="Invitation token"},
     *     },
     *
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "40019"="Member / Invitation not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction($token, Request $request)
    {
        $participatesToManager = $this->get('apr_program.participatesto_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');
        $coopPlusManager = $this->get('apr_admin.coop_plus_manager');

        $participatesTo = $participatesToManager->loadParticipatesToByToken($token);
        if (!$participatesTo) {
            throw new ApiException(40019);
        }
        $isInvitationPlus = $coopPlusManager->isProgramPlus($participatesTo->getProgram());
        $typeInvitation = $request->get('typeInvitation');
        $collaboratorId = $request->get('collaboratorId');

        if (!in_array($typeInvitation, array('standard', 'collaborator', 'college'))) {
            $typeInvitation = 'standard';
        }
        if ($request->get('emailsFilleuls')) {
            $filleuls = Tools::get_array_from_multiple_rfc_email($request->get('emailsFilleuls'));
        } else {
            // Provenance du formulaire firstName / lastName / email pour parrainage Coopérons Plus
            $filleuls = array(array(
                'email' => $request->get('email'),
                'firstName' => $request->get('firstName'),
                'lastName' => $request->get('lastName'),
            ));
        }
        if ($isInvitationPlus && $typeInvitation !== 'standard') {
            // Pas d'envoi d'email d'invitation pour Program Plus - géré par PostInvitationPlus
            $listeEmails = null;
        } else {
            $listeEmails = array();
        }
        $invitations = $invitationManager->createMultipleInvitations($participatesTo, $filleuls, $listeEmails);
        if ($isInvitationPlus && $typeInvitation !== 'standard') {
            $listeEmails = array();
            $coopPlusManager->postInvitationPlus($invitations, $collaboratorId, $typeInvitation, $listeEmails);
        }
        if (count($listeEmails)) {
            $mailsManager = $this->get('apr_user.mailer');
            $mailsManager->sendMails($listeEmails);
        }
        if (isset($invitations['error']) && count($invitations['error'])) {
            if ($isInvitationPlus && $typeInvitation !== 'standard') {
                $message = $invitations['error'];
            } else {
                $message = implode(",", $invitations['error']) . " déjà existante(s).";
            }
            $status = ApiResponse::STATUS_WARNING;
        } else {
            $status = ApiResponse::STATUS_SUCCESS;
            $message = null;
        }
        return new ApiResponse(null, 200, $message, $status);
    }
}
