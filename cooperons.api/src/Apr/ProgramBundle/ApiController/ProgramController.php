<?php

/**
 * This file defines the Program controller in the Bundle ProgramBundle for REST API
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
use Apr\ProgramBundle\Entity\Program;
use Apr\ProgramBundle\Form\Type\ProgramType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Prefix;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ProgramController for API services
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
 * @RouteResource("Program")
 */
class ProgramController extends Controller
{

    /**
     * Returns a collection of Programs
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get Programs list",
     
     *     requirements={},
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction()
    {
        $programManager = $this->get('apr_program.program_manager');
        $settlementsManager = $this->get('apr_mandataire.settlements_manager');

        $programs = $programManager->buildProgramsDetails();
        $waitingSettlements = $settlementsManager->loadSettlements(null, 'waiting', true);
        $waitingSettlementsByProgramId = array();
        foreach($waitingSettlements as $settlement){
            $program = $settlement->getProgram();
            if($program) {
                $waitingSettlementsByProgramId[$program->getId()] = 1;
            }
        }

        return new ApiResponse(array('programs' => $programs, 'haveWaitingSettlements' => $waitingSettlementsByProgramId));
    }

    /**
     * Get data to create new program
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="New program",
     
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function newAction()
    {
        $programManager = $this->get('apr_program.program_manager');
        return new ApiResponse($programManager->getDataforNewProgram($this->getUser()));
    }

    /**
     * Get program
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400 ={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *          },
     *        403={
     *            "4031"="Denied access to program"
     *          },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        $user = $this->getUser();
        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($user, $program, false);
        $isAdministrator = ($user->getMember()->getId() != $program->getCollaborator()->getMember()->getId());
        $isCorporateCooperons = $cooperonsManager->isCorporateCooperons($program);
        $data = array(
            'program' => $program,
            'isAdministrator' => $isAdministrator,
            'isCorporateCooperons' => $isCorporateCooperons,
        );

        return new ApiResponse($data);
    }

    /**
     * Delete program
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Delete program",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status"
     *            },
     *        403={
     *            "4031"="Denied access to program"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function deleteAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $user = $this->getUser();
        $program = $programManager->loadProgramById($id);

        $programManager->securityCheck($user, $program, false);

        $emails = array();
        $programManager->cancelProgram($program, $emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse();
    }

    /**
     * Create program copy
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Create program copy",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status",
     *            "40054"="Program has already a copy",
     *            "40055"="program is already a copy",
     *            },
     *        403={
     *            "4031"="Denied access to program"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function editAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');
        $program = $programManager->loadProgramById($id);

        $programManager->securityCheck($this->getUser(), $program, false);

        if ($program->getStatus() !== 'prod') {
            throw new ApiException(40032);
        }

        if ($program->getOldProgram() !== null) {
            throw new ApiException(40054);
        }

        if ($program->getNewProgram() !== null) {
            throw new ApiException(40055);
        }

        $newProgram = $programManager->copyProgram($program);
        $isAdministrator = ($this->getUser()->getMember()->getId() != $program->getCollaborator()->getMember()->getId());
        $data = array(
            'newProgram' => $newProgram,
            'isAdministrator' => $isAdministrator,
            'oldProgramId' => $program->getId(),
            'newProgramId' => null
        );

        return new ApiResponse($data);
    }

    /**
     * Create/Update program : Step 1
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Create/Update program",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     parameters={
     *      {"name"="isEasy", "dataType"="boolean", "required"=false, "description"="Is easy program ?"},
     *      {"name"="isEasy", "dataType"="boolean", "required"=false, "description"="Is easy program ?"},
     *      {"name"="label", "dataType"="string", "required"=true, "description"="program label"},
     *      {"name"="collaborator", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="collaborator id"},
     *      {"name"="image", "dataType"="file", "required"=true, "description"="program image"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status",
     *            "40058"="File must be an image",
     *            "40080"="Very large file size"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function postAction($id = 0, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $validator = $this->get('api.form.validator');

        $data = $request->request->all();

        // Api doc put id as {id} where it's null
        $id = intval($id);
        if ($id !== 0) {
            $data['id'] = $id;
            $program = $programManager->loadProgramById($id);
            $programManager->securityCheck($this->getUser(), $program, true);
            $imageFile = $program->getImage()->loadFile();
            $isEasy = 0;
        } else {
            $program = new Program();
            $imageFile = null;
            $isEasy = isset($data['isEasy']) ? $data['isEasy'] : 0;
        }
        $uploadedImage = $request->files->get('image');
        if ($uploadedImage && !$uploadedImage->getSize()) {
            throw new ApiException(40080);
        }

        if ($uploadedImage && !getimagesize($uploadedImage)) {
            throw new ApiException(40058);
        }

        $data['image']['file'] = $uploadedImage ? $uploadedImage : $imageFile;

        $arrCollaborators = $this->getUser()->getMember()->getAllCollaborators()->toArray();

        if ($validator->validateData(new ProgramType($arrCollaborators), $data, $program)) {

            $programManager->createProgram($program, $isEasy);
            $program->isEasy();
            return new ApiResponse(array('program' => $program));
        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }
    }

    /**
     * Get program subscription
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program subscription",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status"
     *            },
     *        403={
     *            "4031"="Denied access to program"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getSubscriptionorderAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');
        $coopPlusManager = $this->get('apr_admin.coop_plus_manager');

        $program = $programManager->loadProgramById($id);

        $programManager->securityCheck($this->getUser(), $program, false);

        $order = $coopPlusManager->calculateAbonnement($program);
        $mandataire = $program->getMandataire();

        if ($mandataire !== null) {
            $order['depot'] = $mandataire->calculateDepot($order['amountTtc']);
        } else {
            $order['depot'] = 0;
        }

        unset($order['program']);
        $order['total'] = $order['amountTtc'] + $order['depot'];

        return new ApiResponse(array('order' => $order));
    }

    /**
     * Get program mandataire
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program mandataire",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status"
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
    public function getMandataireAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('mandataire' => $program->getMandataire()));
    }

    /**
     * Get program payments
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program payments",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="program id"},
     *     },
     *     filters={
     *      {"name"="status", "dataType"="string", "required"=false, "description"="Payment status"},
     *      {"name"="paymentMode", "dataType"="string", "required"=true, "description"="Mode of payment"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status",
     *            "40039"="Invalid payment status(status available = {standby, payed, to be complited...})",
     *            "40040"="Invalid payment mode(status available = {virement to be complited...})",
     *            },
     *        403={
     *            "4032"="Denied access to all payments",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getPaymentsAction($id, Request $request)
    {
        $paymentsManager = $this->get('apr_mandataire.payments_manager');
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($id);

        $programManager->securityCheck($this->getUser(), $program, false);


        $status = trim($request->get('status')) ? trim($request->get('status')) : null;
        $paymentMode = trim($request->get('paymentMode')) ? trim($request->get('paymentMode')) : null;
        $mandataire = $program->getMandataire();
        $payments = array();

        if ($status !== null && !in_array($status, array('standby', 'payed'))) {
            throw new ApiException(40039);
        }
        if ($paymentMode !== null && !in_array($paymentMode, array('virement', 'CB'))) {
            throw new ApiException(40040);
        }

        if ($mandataire !== null) {
            $payments = $paymentsManager->loadPayments($mandataire, $status, $paymentMode);
        }


        return new ApiResponse(array('payments' => $payments));
    }


    /**
     * Get points history by program
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get points history by program",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status"
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
    public function getAccountpointshistoryAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');
        $accountHistoryManager = $this->get('apr_program.account_points_history_manager');

        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($this->getUser(), $program, false);
        $historyPoints = $accountHistoryManager->loadPointsByProgram($program);

        return new ApiResponse(array('historyPoints' => $historyPoints));
    }

    /**
     * Get program corporate
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program corporate",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
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
    public function getCorporateAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('corporate' => $program->getCorporate()));
    }

    /**
     * Get program collaborator
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program collaborator",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status"
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
    public function getCollaboratorAction($id)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('collaborator' => $program->getCollaborator()));
    }

    /**
     * Get program invitations
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program invitations",
     
     *     requirements={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     filters={
     *      {"name"="search", "dataType"="string", "required"=true, "description"="search label"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40032"="Program is not in prod status"
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
    public function getInvitationsAction($id, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $invitationManager = $this->get('apr_program.invitation_manager');

        $program = $programManager->loadProgramById($id);
        $programManager->securityCheck($this->getUser(), $program, false);
        $search = $request->get('search');
        $invitations = $invitationManager->loadInvitationsByProgram($program, $search);

        return new ApiResponse(array('invitations' => $invitations));
    }

    /**
     * Get program journals
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Get program journals",
     
     *     requirements={
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"}
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
    public function getJournalsAction($programId)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('journals' => $program->getAllJournals()));
    }


    /**
     * Partial updating program
     *  <br><strong>- Update landing url </strong>
     *  <br> Request format : [{"op": "replace", "path": "/landingUrl", "value": "www.site.com/landing/url"}]
     *  <br><strong>- Update sender Email </strong>
     *  <br> Request format : [{"op": "replace", "path": "/senderEmail", "value": "sender@email.dom"}]
     *  <br><strong>- Update sender name </strong>
     *  <br> Request format : [{"op": "replace", "path": "/senderName", "value": "sender name"}]
     *  <br><strong>- Activate program </strong>
     *  <br> Request format : [{"op": "activate", "path": "/"}]
     *  <br><strong>- Reactivate program </strong>
     *  <br> Request format : [{"op": "reactivate", "path": "/"}]
     *  <br><strong>- Renewal program </strong>
     *  <br> Request format : [{"op": "renewal", "path": "/"}]
     *  <br><strong>- Clear program </strong>
     *  <br> Request format : [{"op": "clear", "path": "/"}]
     *  <br><strong>- Describe program </strong>
     *  <br> Request format : [{"op": "describe", "path": "/", "value":"description"}]
     *  <br><strong>- Describe program operation </strong>
     *  <br> Request format : [{"op": "describe", "path": "/operation", "value":"description"}]
     *  <br><strong>- Confirm invitation </strong>
     *  <br> Request format : [{"op": "confirm", "path": "/invitation", "invitation": 27}]
     *  <br><strong>- Init program journals </strong>
     *  <br> Request format : [{"op": "init", "path": "/"}]
     *
     * @ApiDoc(
     *     section="06.01. Program services",
     *     description="Partial updating program",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40033"="Program is not cancelled",
     *            "40035"="Can not edit program in «prod» status",
     *            "40044"="Invitation not found",
     *            "40053"="Missing presentation document",
     *            "40056"="Creating corporate is not finalized"
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
    public function patchAction($id, Request $request)
    {
        $coopPlusManager = $this->get('apr_admin.coop_plus_manager');
        $programManager = $this->get('apr_program.program_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $program = $programManager->loadProgramById($id);

        $programManager->securityCheck($this->getUser(), $program, false);

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'program');
        $emails = array();
        $data = array();

        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'replace':
                    switch ($patchOperation->path) {
                        case '/landingUrl':
                            if (!$program->isEasy()) {
                                $program->setLandingUrl($patchOperation->value);
                            }
                            break;
                        case '/senderEmail':
                            $program->setSenderEmail($patchOperation->value);
                            break;
                        case '/senderName':
                            $program->setSenderName($patchOperation->value);
                            break;
                    }
                    $programManager->persistAndFlush($program);
                    break;
                case 'activate':
                    if ($program->isEasy() && ($program->getEasySetting() === null || $program->getEasySetting()->getDocument() === null)) {
                        throw new ApiException(40053);
                    }
                    if (!$program->getCorporate()->isAccordSigned()) {
                        throw new ApiException(40056);
                    }
                    $programManager->activateProgram($program, $emails);
                    break;
                case 'reactivate':
                    $contract = $program->getContract();
                    if (!$contract->isCancel()) {
                        throw new ApiException(40033);
                    }
                    $program->reactivate();
                    $programManager->persistAndFlush($program);
                    break;
                case 'renewal':
                    if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
                        throw new ApiException(403);
                    }
                    $coopPlusManager->updateAbonnement($program, $emails);
                    break;
                case 'clear':
                    if ($patchOperation->path === '/') {
                        $programManager->clearProgram($program);
                    } else if ($patchOperation->path === '/journals') {
                        $programManager->clearJournal($program);
                    }
                    break;
                case 'describe':
                    $programManager->securityCheck($this->getUser(), $program, true);
                    if ($patchOperation->path == '/') {
                        $program->setDescription($patchOperation->value);
                        $programManager->persistAndFlush($program);
                    } elseif ($patchOperation->path == '/operation') {
                        $operationManager = $this->get('apr_program.operation_credit_manager');
                        $operation = $operationManager->getOperationById($patchOperation->operation);
                        if ($operation === null || $operation->getProgram()->getId() !== $program->getId()) {
                            throw new ApiException(40059);
                        }
                        $operation->setDescription($patchOperation->value);
                        $operationManager->persistAndFlush($operation);
                    }
                    break;
                case 'confirm':
                    if ($patchOperation->path === '/invitation') {
                        $invitationManager = $this->get('apr_program.invitation_manager');
                        $invitation = $invitationManager->loadInvitationById($program, $patchOperation->invitation);

                        if ($invitation === null) {
                            throw new ApiException(40044);
                        }
                        $participatesToManager = $this->get('apr_program.participates_to_manager');
                        $data = array(
                            'email' => $invitation->getEmail(),
                            'firstName' => $invitation->getFirstName(),
                            'lastName' => $invitation->getLastName(),
                            'tokenInvitation' => $invitation->getToken(),
                        );
                        $result = $participatesToManager->createParticipatesToWithData($program, $data, $emails);
                        if (isset($result['error'])) {
                            throw new ApiException($result['error'], null, $result['errorParams']);
                        }
                        $data ['confirmedInvitation'] = array('participate' => $result['participatesTo']);
                    }
                    break;

            }
        }

        $mailsManager->sendMails($emails);

        return new ApiResponse($data);
    }

}
