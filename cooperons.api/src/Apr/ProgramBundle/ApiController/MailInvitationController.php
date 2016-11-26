<?php

/**
 * This file defines the MailInvitation controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\ProgramBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ApiEasyController for API services
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("MailInvitation")
 */
class MailInvitationController extends Controller
{

    /**
     * Get mail invitations for program
     *
     * @ApiDoc(
     *     section="06.04. Mail invitation services",
     *     description="Get mails invitation",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function cgetAction($programId)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($programId);
        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('mailInvitations' => $program->getAllMailInvitations()));
    }

    /**
     * Create mail invitation for program
     *
     * @ApiDoc(
     *     section="06.04. Mail invitation services",
     *     description="Create mail invitation for program",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     parameters={
     *      {"name"="codeMail", "dataType"="string", "required"=true, "description"="Email code"},
     *      {"name"="subject", "dataType"="string", "required"=false, "description"="Email subject"},
     *      {"name"="content", "dataType"="string", "required"=false, "description"="Email content"},
     *      {"name"="header", "dataType"="string", "required"=false, "description"="Email header"},
     *      {"name"="footer", "dataType"="string", "required"=false, "description"="Email footer"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40051"="An email with same code already exists",
     *            "40052"="Email invitation code is required",
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $mailsManager = $this->get('apr_program.mails_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $codeMail = trim($request->get('codeMail'));
        if (!$codeMail) {
            throw new ApiException(40052);
        }
        $mail = $mailsManager->loadByCode($program, $codeMail);

        if ($mail !== null) {
            throw new ApiException(40051);
        }

        $params = array(
            'code' => $codeMail,
            'subject' => trim($request->get('subject')),
            'content' => trim($request->get('content')),
            'header' => trim($request->get('header')),
            'footer' => trim($request->get('footer')),
        );

        if ($program->isEasy())
            $params['signature'] = trim($request->get('signature'));

        $mailsManager->saveMail($program, $params);

        return new ApiResponse();
    }

    /**
     * Update mail invitation for program
     *
     * @ApiDoc(
     *     section="06.04. Mail invitation services",
     *     description="Update mail invitation for program",
     
     *     requirements={
     *      {"name"="programId", "dataType"="integer", "required"=true, "description"="program id"},
     *      {"name"="codeMail", "dataType"="string", "required"=true, "description"="Email code"},
     *     },
     *     parameters={
     *      {"name"="subject", "dataType"="string", "required"=false, "description"="Email subject"},
     *      {"name"="content", "dataType"="string", "required"=false, "description"="Email content"},
     *      {"name"="header", "dataType"="string", "required"=false, "description"="Email header"},
     *      {"name"="footer", "dataType"="string", "required"=false, "description"="Email footer"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40022"="Invitation email template not found in program #PROGRAM#",
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function putAction($programId, $codeMail, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $mailsManager = $this->get('apr_program.mails_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $mail = $mailsManager->loadByCode($program, $codeMail);

        if ($mail === null) {
            throw new ApiException(40022, null, array('PROGRAM' => $program));
        }

        $params = array(
            'code' => $codeMail,
            'subject' => trim($request->get('subject')),
            'content' => trim($request->get('content')),
            'header' => trim($request->get('header')),
            'footer' => trim($request->get('footer')),
        );

        if ($program->isEasy())
            $params['signature'] = trim($request->get('signature'));

        $mailsManager->saveMail($program, $params);

        return new ApiResponse();
    }

    /**
     * Delete invitation mail
     *
     * @ApiDoc(
     *     section="06.04. Mail invitation services",
     *     description="Delete invitation mail",
     
     *     requirements={
     *      {"name"="programId", "dataType"="integer", "required"=true, "description"="program id"},
     *      {"name"="codeMail", "dataType"="string", "required"=true, "description"="Email code"},
     *     },
     *     statusCodes={
     *        204={
     *            "204"="The resource is deleted"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40022"="Invitation email template not found in program #PROGRAM#",
     *            "40062"="Can not remove default email template",
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function deleteAction($programId, $codeMail)
    {
        $mailsManager = $this->get('apr_program.mails_manager');
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $mail = $mailsManager->loadByCode($program, $codeMail);

        if ($mail === null) {
            throw new ApiException(40022, null, array('PROGRAM' => $program));
        }

        if ($codeMail == 'default') {
            throw new ApiException(40062);
        }

        $mailsManager->deleteMail($mail);

        return new ApiResponse(null, 204);
    }

}
