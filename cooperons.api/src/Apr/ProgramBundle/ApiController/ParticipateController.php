<?php

/**
 * This file defines the Participate controller in the Bundle ProgramBundle for REST API
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
use Apr\ProgramBundle\Form\Type\ParticipatesToType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ParticipateController for API services
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
 * @RouteResource("Participate")
 */
class ParticipateController extends Controller
{


    /**
     * Get all participates to program
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Get participates to program",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     filters={
     *      {"name"="search", "dataType"="string", "required"=false, "description"="Search label"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
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
    public function cgetAction($id, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $program = $programManager->loadProgramById($id);

        $search = $request->get('search');

        $programManager->securityCheck($this->getUser(), $program, false);
        $participates = $participatesToManager->loadParticipates($program, $search);

        return new ApiResponse(array('participates' => $participates));
    }


    /**
     * Get participate to program by id
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Get participate to program by id",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found"
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
    public function getAction($programId, $participateId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $participate = $participatesToManager->loadParticipatesToById($program, $participateId);


        if ($participate == null) {
            throw new ApiException(40042);
        }

        return new ApiResponse(array('participate' => $participate));
    }


    /**
     * Add new participate to program
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Add new participate to program",
     
     *     input = {
     *      "class" = "Apr\ProgramBundle\Form\Type\ParticipatesToType",
     *      "name" = ""
     *      },
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        201={
     *            "201"="The resource is created"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40015"="Member with email #EMAIL# already exists in program #PROGRAM#",
     *            "40016"="Member with id #ID# already exists in program #PROGRAM#",
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
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
        $formValidator = $this->get('api.form.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $program = $programManager->loadProgramById($programId);
        $programManager->securityCheck($this->getUser(), $program, false);

        $data = $request->request->all();

        if ($formValidator->validateData(new ParticipatesToType(), $data)) {
            $emails = array();

            $participatesToManager = $this->get('apr_program.participates_to_manager');
            $result = $participatesToManager->createParticipatesToWithData($program, $data, $emails);
            if (isset($result['error'])) {
                throw new ApiException($result['error'], null, $result['errorParams']);
            }

            $mailsManager->sendMails($emails);

            return new ApiResponse(array('participate' => $result['participatesTo']));
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
    }



    /**
     * Get participate filleuls
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Get participate filleuls",
     
     *     filters = {
     *      {"name"="year", "requirement"="\dddd", "dataType"="integer", "required"=false, "description"="Filter by year"},
     *      {"name"="search", "dataType"="itatstring", "required"=false, "description"="search label"},
     *     },
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found"
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
    public function getFilleulsAction($programId, $participateId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $memberManager = $this->get('apr_program.member_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $participatesTo = $participatesToManager->loadParticipatesToById($program, $participateId);

        if ($participatesTo == null) {
            throw new ApiException(40042);
        }
        $search = $request->get('search');
        $data = array();
        if ($search) {
            $sponsorWhoseUplineExcluded = $participatesToManager->loadParticipatesToById($program, $participateId);
            $data['filleuls'] = $participatesToManager->searchParticipatesTo($program, $search, $sponsorWhoseUplineExcluded);

        } else {
            $data = $memberManager->getFilleulsDetails($participatesTo->getMember(), $request->get('year'), $programId);

        }

        return new ApiResponse($data);
    }

    /**
     * Get account points history for participate
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Get account points history for participate",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found"
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
    public function getAccountpointshistoryAction($programId, $participateId)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        $participatesTo = $participatesToManager->loadParticipatesToById($program, $participateId);

        if ($participatesTo == null) {
            throw new ApiException(40042);
        }

        return new ApiResponse(array('accountPointsHistory' => $participatesTo->getAllAccountPointsHistory()));
    }

    /**
     * Calculate upline
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Calculate upline",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *     },
     *     parameters={
     *      {"name"="operationId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Operation id"},
     *      {"name"="amount", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Amount"},
     *     },
     *
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found"
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
    public function getUplineAction($programId, $participateId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $operationCreditManager = $this->get('apr_program.operation_credit_manager');

        $operationId = $request->get('operationId');
        $amount = $request->get('amount');
        $affiliateId = $request->get('affiliateId');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);
        $participatesTo = $participatesToManager->loadParticipatesToById($program, $participateId);

        if ($participatesTo == null) {
            throw new ApiException(40042);
        }

        if ($affiliateId) {
            $affiliate = $participatesToManager->loadParticipatesToById($program, $affiliateId);
            if ($affiliate == null) {
                throw new ApiException(40042);
            }
            $multiPoints = floor($affiliate->getTotal()['multiPoints'] * 2 / 3);
            $arrayPoints = $participatesToManager->buildUpline($participatesTo, $multiPoints);
            $arrayPoints[0] = array('participatesTo' => $participatesTo, 'points' => $multiPoints);
        } else {
            $operation = $operationCreditManager->getOperationById($operationId);
            $arrayPoints = $operation->isMulti() ? $participatesToManager->buildUpline($participatesTo, $amount) : array();
            $arrayPoints[0] = array('participatesTo' => $participatesTo, 'points' => $amount);
        }
        ksort($arrayPoints);
        return new ApiResponse(array('upline' => $arrayPoints));
    }

    /**
     * Partial updating program participate
     *  <br><strong>- Add points to participate </strong>
     *  <br> Request format : [{"op": "add", "path": "/points", "operation": 12, "value": 500}]
     *  <br><strong>- Resend welcome invitation </strong>
     *  <br> Request format : [{"op": "resend", "path": "/welcomeEmail"}]
     *  <br><strong>- Confirm sponsorship </strong>
     *  <br> Request format : [{"op": "confirm", "path": "/sponsorship", "affiliate": 24}]
     *
     * @ApiDoc(
     *     section="06.02. Participate services",
     *     description="Partial updating program participate",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="participateId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of participate"},
     *  },
     *     statusCodes={
     *        204={
     *            "204"="The resource is updated"
     *            },
     *        400={
     *            "40017"="No default amount for Operation",
     *            "40023"="Forbidden operation",
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40042"="Participate to program not found",
     *            "40059"="Credit operation not found in program",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            "40062"="Participate to program not found"
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
    public function patchAction($programId, $participateId, Request $request)
    {

        $programManager = $this->get('apr_program.program_manager');
        $participatesToManager = $this->get('apr_program.participates_to_manager');
        $mailsManager = $this->container->get('apr_user.mailer');
        $patchValidator = $this->get('api.patch.data.format.validator');


        $program = $programManager->loadProgramById($programId);
        $programManager->securityCheck($this->getUser(), $program, false);
        $participates = $participatesToManager->loadParticipatesToById($program, $participateId);
        if ($participates == null) {
            throw new ApiException(40042);
        }

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'participate');
        $emails = array();

        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'add':
                    if ($patchOperation->path == '/points') {
                        $operationId = isset($patchOperation->operation) ? $patchOperation->operation : 0;
                        $operationCreditManager = $this->get('apr_program.operation_credit_manager');
                        $accountManager = $this->get('apr_program.account_points_history_manager');

                        $operation = $operationCreditManager->getOperationById($operationId);
                        if ($operation === null || $operation->getProgram()->getId() != $programId) {
                            throw new ApiException(40059);
                        }

                        $datas = array(array('labelOperation' => $operation->getLabel(), 'amount' => $patchOperation->value));
                        $emails = array();

                        $result = $accountManager->addPoints($participates, $datas, $emails);
                        if (isset($result['error'])) {
                            throw new ApiException($result['error']);
                        }
                    }
                    break;
                case 'resend':
                    if ($patchOperation->path === '/welcomeEmail') {
                        $emails[] = $participatesToManager->getMailWelcomeEasy($participates);
                    }
                    break;
                case 'confirm':
                    if ($patchOperation->path == '/sponsorship') {
                        $affiliate = $participatesToManager->loadParticipatesToById($program, $patchOperation->affiliate);
                        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');
                        $sponsorShipManager->createSponsorship($program, $participates, $affiliate, $emails);
                    }
                    break;

            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

}
