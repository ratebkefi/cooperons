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

use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class InvitationController for API services
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
class InvitationController extends Controller
{
    /**
     * Create invitation
     *
     * @ApiDoc(
     *     section="01.02. Invitation services",
     *     description="Create contract invitation",
     
     *     parameters={
     *      {"name"="typeInvitation", "dataType"="string", "required"=false, "description"="Invitation type"},
     *      {"name"="collaboratorId", "dataType"="integer", "required"=false, "description"="Collaborator id"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Invitation token"},
     *      {"name"="firstname", "dataType"="string", "required"=true, "description"="Invitation token"},
     *      {"name"="lastname", "dataType"="string", "required"=true, "description"="Invitation token"},
     *     },
     *
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
    public function postAction(Request $request)
    {
        $coopPlusManager = $this->get('apr_admin.coop_plus_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $user = $this->getUser();
        $member = $user->getMember();
        $collaboratorId = $request->get('collaboratorId');
        $typeInvitation = $request->get('typeInvitation');
        $filleuls = array(array(
            'email' => $request->get('email'),
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
        ));
        $participatesTo = $coopPlusManager->loadParticipatesToPlus($member);

        $invitations = $invitationManager->createMultipleInvitations($participatesTo, $filleuls);

        $emails = array();

        $coopPlusManager->postInvitationPlus($invitations, $collaboratorId, $typeInvitation, $emails);
        $mailsManager->sendMails($emails);

        $data = array();
        if(isset($invitations['error'])) {
            if($typeInvitation == 'default:owner') {
                $data['collaborators'] = $invitations['collaborators'];
            } elseif(in_array($typeInvitation, array('default:client', 'affair:client', 'affair:owner'))) {
                $data['autoEntrepreneur'] = $invitations['autoEntrepreneur'];
            }
        }
        return new ApiResponse($data);
    }
}
