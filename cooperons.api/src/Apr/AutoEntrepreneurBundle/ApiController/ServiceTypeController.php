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

namespace Apr\AutoEntrepreneurBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class ServiceTypeController for API services
 *
 * @category AutoEntrepreneurBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("ServiceType")
 */
class ServiceTypeController extends Controller
{

    /**
     * Get contract services types
     *
     * @ApiDoc(
     *     section="05.02. Contract service type",
     *     description="Get contract services types",
     
     *     requirements={
     *      {"name"="contractId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400100"="Contract not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction($contractId)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $contract = $contractManager->loadContractById($contractId);

        if ($contract == null) {
            throw new ApiException(400100);
        }

        $contractManager->securityCheck($this->getUser(), $contract);

        return new ApiResponse(array('serviceTypes' => $contract->getAllServiceTypes()));
    }

    /**
     * Create contract service type
     *
     * @ApiDoc(
     *     section="05.02. Contract service type",
     *     description="Create contract service type",
     
     *     requirements={
     *      {"name"="contractId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="contract id"},
     *     },
     *     parameters={
     *      {"name"="label", "dataType"="string", "required"=false, "description"="service type label"},
     *      {"name"="unitLabel", "dataType"="string", "required"=false, "description"="service type unitLabel"},
     *      {"name"="unitDefaultAmount", "dataType"="string", "required"=false, "description"="service type unitDefaultAmount"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "400100"="Contract not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction($contractId, Request $request)
    {

        $contractManager = $this->get('apr_contract.contract_manager');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $contract = $contractManager->loadContractById($contractId);

        $contractManager->securityCheck($this->getUser(), $contract, true, false);

        $label = $request->get('label');
        $unitLabel = $request->get('unitLabel');
        $unitDefaultAmount = $request->get('unitDefaultAmount');

        $autoEntrepreneurManager->addServiceType($contract, $label, $unitDefaultAmount, $unitLabel);
        return new ApiResponse();
    }

    /**
     * Edit contract service type
     *
     * @ApiDoc(
     *     section="05.02. Contract service type",
     *     description="Edit contract service type",
     
     *     requirements={
     *      {"name"="contractId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="id of contract"},
     *      {"name"="serviceTypeId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="id of service type"},
     *     },
     *     parameters={
     *      {"name"="label", "dataType"="string", "required"=false, "description"="service type label"},
     *      {"name"="unitLabel", "dataType"="string", "required"=false, "description"="service type unitLabel"},
     *      {"name"="unitDefaultAmount", "dataType"="string", "required"=false, "description"="service type unitDefaultAmount"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "400100"="Contract not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function putAction($contractId, $serviceTypeId, Request $request)
    {

        $contractManager = $this->get('apr_contract.contract_manager');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $contract = $contractManager->loadContractById($contractId);

        $contractManager->securityCheck($this->getUser(), $contract, true, false);

        $label = $request->get('label');
        $unitLabel = $request->get('unitLabel');
        $unitDefaultAmount = $request->get('unitDefaultAmount');

        $autoEntrepreneurManager->updateServiceType($serviceTypeId, $label, $unitDefaultAmount, $unitLabel);
        return new ApiResponse();
    }

    /**
     * Delete contract service type
     *
     * @ApiDoc(
     *     section="05.02. Contract service type",
     *     description="Delete contract service type",
     
     *     requirements={
     *      {"name"="contractId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="id of contract"},
     *      {"name"="serviceTypeId", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="id of service type"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "400100"="Contract not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function deleteAction($contractId, $serviceTypeId, Request $request)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $contract = $contractManager->loadContractById($contractId);
        $contractManager->securityCheck($this->getUser(), $contract, true, false);

        $serviceType = $autoEntrepreneurManager->getServiceTypeById($serviceTypeId);

       if($contract !== $serviceType->getContract()){
           throw new ApiException(40084);
       }

        $autoEntrepreneurManager->removeAndFlush($serviceType);

        return new ApiResponse();
    }
}
