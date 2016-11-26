<?php
/**
 * This file defines the Affair controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\ProgramBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\ProgramBundle\Entity\Affair;
use Apr\ProgramBundle\Form\Type\AffairType;
use Apr\ProgramBundle\Entity\PreProdAffair;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;


/**
 * Class AffairController for API services
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
 * @RouteResource("Affair")
 */
class AffairController extends Controller

{
    /**
     * Get program affairs for participate
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Get program affairs for participate",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"}
     *     },
     *     filters={
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=false, "description"="Id of participate"},
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
     *            },
     *
     *     }
     * )
     */
    public function cgetAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $affairManager = $this->get('apr_program.affair_manager');


        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);
        $participateId = $request->get('participateId');

        if ($participateId) {
            $participate = $participatesToManager->loadParticipatesToById($program, $participateId);
            if ($participate == null) {
                return new ApiResponse(array('affairs' => array()));
            }
        } else {
            $participate = null;
        }

        $affairs = $affairManager->loadAffairByParticipatesTo($program, $participate);

        return new ApiResponse(array('affairs' => $affairs));
    }

    /**
     * Get program affair
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Get program affair",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="affairId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of affair"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40045"="Affair not found",
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getAction($programId, $affairId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $affairManager = $this->get('apr_program.affair_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $affair = $affairManager->loadAffairById($program, $affairId);

        if($affair === null){
            throw new ApiException(40045);
        }

        return new ApiResponse(array('affair' => $affair));
    }

    /**
     * Create affair for participate
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Create affair for participate",
     
     *     input = {
     *      "class" = "Apr\ProgramBundle\Form\Type\AffairType",
     *      "name" = ""
     *      },
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     parameters={
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found",
     *            "40043"="ParticipateId is required"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function postAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $affairManager = $this->get('apr_program.affair_manager');
        $validator = $this->get('api.form.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);
        $participateId = $request->request->get('participateId');

        if (!$participateId) {
            throw new ApiException(40043);
        }

        $participate = $participatesToManager->loadParticipatesToById($program, $participateId);

        if ($participate == null) {
            throw new ApiException(40042);
        }

        if ($program->getStatus() == 'preprod') {
            $affair = new PreProdAffair($program, $participate);
        } else {
            $affair = new Affair($program, $participate);
        }

        if ($validator->validateData(new AffairType(), $request->request->all(), $affair)) {
            $emails = array();
            $affairManager->processAffair($affair, null, $emails);
            $mailsManager->sendMails($emails);

            return new ApiResponse();
        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }
    }

    /**
     * Get affair participate
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Get affair participate",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="affairId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of affair"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40045"="Affair not found"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getParticipateAction($programId, $affairId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $affairManager = $this->get('apr_program.affair_manager');


        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $affair = $affairManager->loadAffairById($program, $affairId);

        if ($affair === null) {
            throw new ApiException(40045);
        }

        return new ApiResponse(array('participate' => $affair->getParticipatesTo()));
    }

    /**
     * Get affair commissions
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Get affair commissions",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="affairId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of affair"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40045"="Affair not found"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getCommissionsAction($programId, $affairId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $affairManager = $this->get('apr_program.affair_manager');


        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $affair = $affairManager->loadAffairById($program, $affairId);

        if ($affair === null) {
            throw new ApiException(40045);
        }

        return new ApiResponse(array('commissions' => $affair->getAllCommissions()));
    }

    /**
     * Get affair upline
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Get affair upline",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="affairId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of affair"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40045"="Affair not found"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getUplineAction($programId, $affairId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $affairManager = $this->get('apr_program.affair_manager');


        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $affair = $affairManager->loadAffairById($program, $affairId);

        if ($affair === null) {
            throw new ApiException(40045);
        }

        return new ApiResponse(array('upline' => $affairManager->buildEasyUpline($affair)));
    }

    /**
     * Partial updating affair
     *  <br><strong>- Process affair </strong>
     *  <br> Request format : [{"op": "process", "path": "/", "amount": "500"}]
     *  <br><strong>- Cancel affair </strong>
     *  <br> Request format : [{"op": "cancel", "path": "/", "message": "cancel message"}]
     *
     * @ApiDoc(
     *     section="06.07. Affair services",
     *     description="Process affair",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="affairId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of affair"},
     *     },
     *     parameters = {
     *      {"name"="amount", "requirement"="\d+", "dataType"="number", "required"=true, "description"="Affair amount"},
     *     },
     *     statusCodes={
     *        204={
     *            "204"="The resource is update"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40045"="Affair not found",
     *            "40046"="Amount must be a strictly positive number",
     *            "40048"="Impossible to change the status of a paid or cancelled affair",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function patchAction($programId, $affairId, Request $request)
    {

        $programManager = $this->get('apr_program.program_manager');
        $affairManager = $this->get('apr_program.affair_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $program = $programManager->loadProgramById($programId);
        $programManager->securityCheck($this->getUser(), $program, false);

        $affair = $affairManager->loadAffairById($program, $affairId);

        if ($affair === null) {
            throw new ApiException(40045);
        }

        if ($affair->getStatus() === 'paid' || $affair->getStatus() === 'cancel') {
            throw new ApiException(40048);
        }

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'affair');
        $emails = array();


        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'process':
                    if ($operation->amount <= 0) {
                        throw new ApiException(40046);
                    }
                    $affairManager->processAffair($affair, $operation->amount, $emails);
                    break;
                case 'cancel':
                    $emails = array();
                    $affairManager->cancelAffair($affair, $operation->message, $emails);
                    break;
            }
        }

        $mailsManager->sendMails($emails);

        return new ApiResponse();
    }

}