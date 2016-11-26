<?php
/**
 * This file defines the party controller in the Bundle MandataireBundle for REST API
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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PartyController for API services
 *
 * @category MandataireBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("Party")
 */
class PartyController extends Controller
{

    /**
     * Get Party
     *
     * @ApiDoc(
     *     section="02.01. Party management",
     *     description="Get party",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of party"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400102"="Party not found",
     *            },
     *        403={
     *            "403100"="Denied acces to party",
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
        $cooperonsManager = $this->get('apr_admin.cooperons_manager');

        $user = $this->getUser();
        $member = $user->getMember();

        $party = $contractManager->loadPartyById($id);

        if($party === null){
            throw new ApiException(400102);
        }

        if(!$party->isAuthorized($member)){
            throw new ApiException(403100);
        }

        return new ApiResponse(array('party' => $party, 'status' => $cooperonsManager->getPartyStatus($party)));
    }


    /**
     * Get mandataire party
     *
     * @ApiDoc(
     *     section="02.01. Party management",
     *     description="Get mandataire party",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of party"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400102"="Party not found",
     *            },
     *        403={
     *            "403100"="Denied access to party",
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

        $user = $this->getUser();
        $member = $user->getMember();

        $party = $contractManager->loadPartyById($id);

        if($party === null){
            throw new ApiException(400102);
        }

        if(!$party->isAuthorized($member)){
            throw new ApiException(403100);
        }

        return new ApiResponse(array('mandataire' => $party->getMandataire()));

    }

    /**
     * Get party administrator
     *
     * @ApiDoc(
     *     section="02.01. Party management",
     *     description="Get party administrator",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of party"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400102"="Party not found",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAdministratorAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');

        $party = $contractManager->loadPartyById($id);
        if ($party === null) {
            throw new ApiException(400102);
        }

        return new ApiResponse(array('administrator' => $party->getAdministrator()));
    }

    /**
     * Get party collaborators
     *
     * @ApiDoc(
     *     section="02.01. Party management",
     *     description="Get party collaborators",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of party"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400102"="Party not found",
     *            },
     *        403={
     *             "403101"="Denied access to collaborators"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getCollaboratorsAction($id)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');

        $party = $contractManager->loadPartyById($id);
        if ($party === null) {
            throw new ApiException(400102);
        }

        $collaboratorManager->securityCheck($this->getUser()->getMember(), $party->getAdministrator(), true);

        return new ApiResponse(array('collaborators' => $party->getAllCollaborators()));
    }

}
