<?php

/**
 * This file defines the Contract controller in the Bundle ContractBundle for REST API
 *
 * @category ContractBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\ContractBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Apr\AutoEntrepreneurBundle\Form\Type\RecruitmentSettingsType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ContractController for API services
 *
 * @category ContractBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("Contract")
 */
class ContractController extends Controller
{

    /**
     * Get contract
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Get contract",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400100"="Contract not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $contract = $contractManager->loadContractById($id);

        $party = $contractManager->securityCheck($this->getUser(), $contract);
        $counterParty = $contract->getCounterParty($party);
        
        return new ApiResponse(array(
            'contract' => $contract,
            'party' => $counterParty,
            'collaborator' => $contract->getRelevantCollaborator($counterParty),
            'local' => $counterParty->getLocal(),
            'legalDocuments' => $contract->getAllLegalDocuments()
        ));
    }

    /**
     * Get contract mandataire
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Get contract mandataire",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400100"="Contract not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getMandataireAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');

        $contract = $contractManager->loadContractById($id);

        $contractManager->securityCheck($this->getUser(), $contract);

        return new ApiResponse(array('mandataire' => $contract->getMandataire()));
    }

    /**
     * Delete contract
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Delete contract",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of contract"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400100"="Contract not found",
     *            "400120"="Can not delete contract"
     *            },
     *        403={
     *            "403102"="Denied access to contract"
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
        $contractManager = $this->get('apr_contract.contract_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $contract = $contractManager->loadContractById($id);
        $contractManager->securityCheck($this->getUser(), $contract);

        $emails = array();
        $contractManager->removeContract($contract, $emails);
        $mailsManager->sendMails($emails);
        return new ApiResponse(null,204);
    }

    /**
     * Get contract recruitment
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Get contract recruitment",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400100"="Contract not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getRecruitmentAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $contract = $contractManager->loadContractById($id);
        $member = $this->getUser()->getMember();

        if ($contract == null) {
            throw new ApiException(400100);
        }

        if (($contract->getClient() || $contract->getOwner()) && $contract->getAuthorizedParty($member) === null) {
            throw new ApiException(403102);
        }

        return new ApiResponse(array('recruitment' => $contract->getRecruitment()));
    }

    /**
     * Get contract recruitment settings
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Get contract recruitment settings",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400100"="Contract not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getRecruitmentsettingsAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');

        $contract = $contractManager->loadContractById($id);

        $contractManager->securityCheck($this->getUser(), $contract);

        return new ApiResponse(array('recruitmentSettings' => $contract->getRecruitmentSettings()));
    }


    /**
     * Edit contract recruitment settings
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Edit contract recruitment settings",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *      input = {
     *      "class" = "Apr\AutoEntrepreneurBundle\Form\Type\RecruitmentSettingsType",
     *      "name" = ""
     *      },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "4000"="Failed data validation",
     *            "400100"="Contract not found",
     *            "400600"="Recruitment settings not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function putRecruitmentsettingsAction(Request $request, $id)
    {

        $contractManager = $this->get('apr_contract.contract_manager');
        $formValidator = $this->get('api.form.validator');

        $contract = $contractManager->loadContractById($id);

        $contractManager->securityCheck($this->getUser(), $contract, true, false);

        $recruitmentSettings = $contract->getRecruitmentSettings();

        if ($recruitmentSettings === null) {
            throw new ApiException(400600);
        }

        $data = $request->request->all();

        if ($formValidator->validateData(new RecruitmentSettingsType(), $data, $recruitmentSettings)) {
            $contractManager->persistAndFlush($formValidator->getData());
            return new ApiResponse(array('idContract' => $id));
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
    }

    /**
     * Partial updating contract
     * <br><strong>- Resend contract invitation </strong>
     * <br> Request format : [{"op": "reinvite", "path": "/invitation"}]
     * <br><strong>- Reactivate a contract </strong>
     * <br> Request format : [{"op": "reactivate", "path": "/"}]
     * <br><strong>- Check sponsorable </strong>
     * <br> Request format : [{"op": "check", "path": "/sponsorable"}]
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Partial updating contract",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            "400100"="Contract not found"
     *
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function patchAction($contractId, Request $request)
    {
        $patchValidator = $this->get('api.patch.data.format.validator');
        $contractManager = $this->get('apr_contract.contract_manager');
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');
        $coopAEManager = $this->get('apr_admin.coop_ae_manager');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $recruitmentManager = $this->get('apr_affair.recruitment_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $contract = $contractManager->loadContractById($contractId);
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'contract');
        $member = $this->getUser()->getMember();

        $emails = array();
        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'reinvite':
                    $invitation = $contract->getInvitation();
                    if ($invitation) {
                        $emails = array($collaboratorManager->getEmailInvitationContract($invitation));
                    }
                    break;
                case 'reactivate':
                    $contractManager->securityCheck($this->getUser(), $contract, true, false);
                    $contractManager->reactivateContract($contract, $emails);
                    break;
                case 'check':
                    if ($patchOperation->path == '/sponsorable') {
                        $contract = $contractManager->loadContractById($contractId);
                        $contractManager->securityCheck($this->getUser(), $contract);
                        if(!$contract or (!$contract->getInvitation() &&!$contract->getAuthorizedParty($member))) { 
                            throw new ApiException(400100);
                        } else {  
                            return new ApiResponse($autoEntrepreneurManager->isSponsorableContract($contract, $member));
                        }
                    }
                    break;
                case 'createRecruitment':
                    if ($patchOperation->path == '/recruitment') {
                        $contractManager->securityCheck($this->getUser(), $contract, true);
                        
                        $customerContract = $contractManager->loadContractById($patchOperation->customerContractId);
                        $contractManager->securityCheck($this->getUser(), $customerContract, true);

                        $result = $recruitmentManager->createRecruitment($contract, $customerContract, $emails);
                        if (isset($result['recruitment'])) {
                            unset($result['recruitment']);
                        }
                        $mailsManager->sendMails($emails);
                        return new ApiResponse($result);
                    }
                    break;
            }
            $mailsManager->sendMails($emails);
        }
        return new ApiResponse(array());
    }

    /**
     * Create contract settlements
     *
     * @ApiDoc(
     *     section="02.03. Contract Services",
     *     description="Create contract settlements",
     
     *      parameters={
     *      {"name"="unitAmount_1", "dataType"="number", "required"=true, "description"="Unit amount of serviceType with id 1"},
     *      {"name"="quantity_1", "dataType"="number", "required"=true, "description"="quantity of serviceType with id 1"},
     *      },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "400100"="Contract not found",
     *            "40081"="Wrong settlements parameters"
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postSettlementsAction($id, Request $request)
    {

        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $contractManager = $this->get('apr_contract.contract_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $contract = $contractManager->loadContractById($id);
        if (!$contract) {
            throw new ApiException(400100);
        }

        $autoEntrepreneur = $this->getUser()->getMember()->getAutoEntrepreneur();
        $allServiceTypes = $contract->getAllServiceTypes();

        if (!$contract->getOwner()->getAutoEntrepreneur() or $contract->getOwner()->getAutoEntrepreneur() != $autoEntrepreneur
            or count($allServiceTypes) == 0 or $contract->getSuspensionDate()
        ) {
            throw new ApiException(400100);
        }

        $params = $request->request->all();
        $emails = array();
        if ($autoEntrepreneurManager->createServiceSettlements($contract, $params, $emails)) {
            if(count($emails)) {
                $mailsManager->sendMails($emails);
            }
        }
        return new ApiResponse();
    }
}
