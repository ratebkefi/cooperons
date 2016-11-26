<?php
/**
 * This file defines the Collaborator controller in the Bundle CorporateBundle for REST API
 *
 * @category ContractBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\ContractBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CollaboratorController for API services
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
 * @RouteResource("Collaborator")
 */
class CollaboratorController extends Controller
{
    /**
     * Partial modification
     *  <br><strong>- Invite contract </strong>
     *  <br> Request format : [{"op": "invite_contract", "path": "/contract", "typeInvitation": "default:owner", "firstName": "John", "lastName": "Doe", "email":"john@doe.com"}]
     *  <br><strong>- Re-invite collaborator </strong>
     *  <br> Request format : [{"op": "re-invite", "path": "/"}]
     * <br><strong>- create recruitment </strong>
     * <br> Request format : [{"op": "createRecruitment", "path": "/recruitment", "customerContractId":10, "recruitmentContractId":23}]
     *  <br><strong>- Leave corporate </strong>
     *  <br> Request format : [{"op": "leave", "path": "/"}]
     *  <br><strong>- Transfer contract </strong>
     *  <br> Request format : [{"op": "transfer", "path": "/contract", "contractId": 12}]
     *
     * @ApiDoc(
     *     section="02.02. Collaborator Services",
     *     description="Partial modification",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Collaborator id"},
     *    },
     *     statusCodes={
     *        204={
     *            "204"="The request has succeeded"
     *            },
     *        400={
     *             "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *             "40061"="Wrong patch format",
     *             "40067"="Can not terminate corporate",
     *             "400100"="Contract not found",
     *             "400105"="Collaborator not found",
     *             "400122"="Can not transfer contract",
     *            },
     *        403={
     *             "403101"="Denied access to collaborator"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function patchAction($id, Request $request)
    {
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');

        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $collaborator = $collaboratorManager->loadCollaboratorById($id);
        if ($collaborator === null) {
            throw new ApiException(400105);
        }

        $party = $collaborator->getParty();
        $member = $this->getUser()->getMember();

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'collaborator');
        $emails = array();

        foreach ($patch as $operation) {
            switch ($operation->op) {
//                case 'invite_contract':
//                    $collaboratorManager->securityCheck($member, $collaborator);
//                    $filleul = array(
//                        'firstName' => $operation->firstName,
//                        'lastName' => $operation->lastName,
//                        'email' => $operation->email,
//                    );
//                    $result = $collaboratorManager->createInvitationContract($collaborator, $operation->typeInvitation, $filleul, $emails);
//                    $mailsManager->sendMails($emails);
//                    return new ApiResponse($result);
                case 're-invite':
                    $collaboratorManager->securityCheck($member, $collaborator, true);
                    $emails[] = $collaboratorManager->getMailInvitationCollaborator($collaborator);
                    break;
                case 'leave':
                    $collaboratorManager->securityCheck($member, $collaborator, true);
                    if ($operation->transferId) {
                        $transfer = $collaboratorManager->loadCollaboratorById($operation->transferId);
                        if ($transfer === null || $transfer->getParty() != $party) {
                            throw new ApiException(400105);
                        }
                    } else if ($collaborator->isAdministrator() && !$party->canTerminate()) {
                        throw new ApiException(40067);
                    } else {
                        $transfer = null;
                    }
                    $collaboratorManager->leaveCollaborator($collaborator, $transfer, $emails);
                    break;
                case 'transfer':
                    $collaboratorManager->securityCheck($member, $collaborator, true);
                    if ($operation->path = '/contract') {
                        $contractManager = $this->get('apr_contract.contract_manager');

                        $contract = $contractManager->loadContractById($operation->contractId);
                        if ($contract == null) {
                            throw new ApiException(400100);
                        }

                        if (is_null($contract->getCollaboratorSide($collaborator))
                            or ($collaborator == $contract->getRelevantCollaborator($party))
                        ) {
                            throw new ApiException(400122);
                        }

                        $contractManager->transferContract($contract, $collaborator, $emails);

                    }
                    break;
            }
        }

        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

    /**
     * Get collaborator contracts
     *
     * @ApiDoc(
     *     section="02.02. Collaborator Services",
     *     description="Get collaborator contracts",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Collaborator id"},
     *    },
     *    filters={
     *      {"name"="filterContract", "dataType"="string", "required"=false, "description"="type of contract"}
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400105"="Collaborator not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getContractsAction($id, Request $request)
    {
        $recruitmentManager = $this->get('apr_affair.recruitment_manager');
        $mailsManager = $this->get('apr_user.mailer');
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');

        $filterContract = $request->get('filterContract');

        $collaborator = $collaboratorManager->loadCollaboratorById($id);

        if ($collaborator === null) {
            throw new ApiException(400105);
        }

        if (is_null($filterContract)) {
            $contracts = $collaborator->getAllContracts();
        } else {
            $contractManager = $this->get('apr_contract.contract_manager');
            $party = $collaborator->getParty();
            $contracts = $contractManager->getAllContracts($party, $filterContract, $collaborator);
        }

        // TODO: CRON updateRecruitmentExpiryDates ...
        $emails = array();
        $recruitmentManager->updateRecruitmentExpiryDates($contracts, $emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse(array('contracts' => $contracts));
    }

    /**
     * create contract from Collaborator
     *
     * @ApiDoc(
     *     section="02.02. Collaborator Services",
     *     description="Create contract",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Collaborator id"}
     *     },
     *     parameters={
     *      {"name"="filterContract", "dataType"="string", "required"=true, "description"="contract filter"},
     *      {"name"="otherCollaboratorId", "dataType"="integer", "required"=true, "description"="Other CollaboratorId ID"}
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "4001"="No parameter input",
     *            "400105"="Collaborator not found"
     *            },
     *          403={
     *            "403101"="Denied access to collaborator",
     *            "403103" = "Owner has not subscribed to Contracts service"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postContractAction($id, Request $request)
    {
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');
        $contractManager = $this->get('apr_contract.contract_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $user = $this->getUser();
        $member = $user->getMember();
        $filterContract = $request->get('filterContract');
        $otherCollaboratorId = $request->get('otherCollaboratorId');

        if (is_null($filterContract) || is_null($otherCollaboratorId)) {
            throw new ApiException(4001);
        }

        $collaborator = $collaboratorManager->loadCollaboratorById($id);
        $otherCollaborator = $collaboratorManager->loadCollaboratorById($otherCollaboratorId);
        if ($collaborator === null or $otherCollaborator === null) {
            throw new ApiException(400105);
        }

        $collaboratorManager->securityCheck($member, $collaborator);

        $emails = array();
        $contractManager->createContract($collaborator, $otherCollaborator, $filterContract, $emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse();
    }

    /**
     * Create invitation for contract
     *
     * @ApiDoc(
     *     section="02.02. Collaborator Services",
     *     description="Create contract invitation",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Collaborator id"}
     *     },
     *     input={
     *      "class"="Apr\ContractBundle\Form\Type\ContractInvitationType",
     *      "name"=""
     *    },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "4001"="No parameter input",
     *            "400105"="Collaborator not found"
     *            },
     *          403={
     *            "403101"="Denied access to collaborator",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postContractinvitationAction($id, Request $request)
    {
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $user = $this->getUser();
        $member = $user->getMember();

        $collaborator = $collaboratorManager->loadCollaboratorById($id);
        if ($collaborator === null) {
            throw new ApiException(400105);
        }

        $collaboratorManager->securityCheck($member, $collaborator);
        $data = $request->request->all();

        $filleul = array(
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
        );
        $emails = array();
        $result = $collaboratorManager->createInvitationContract($collaborator, $data['invitationType'], $filleul, $emails);
        $mailsManager->sendMails($emails);

        return new ApiResponse($result);
    }


}