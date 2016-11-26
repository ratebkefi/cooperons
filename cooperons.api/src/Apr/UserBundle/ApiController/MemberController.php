<?php

/**
 * This file defines the Member controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class MemberController for API services
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
 * @RouteResource("Member")
 */
class MemberController extends Controller
{

    /**
     * Get member
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member",
     
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
    public function getAction()
    {
        $invitationManager = $this->container->get('apr_program.invitation_manager');
        $member = $this->getUser()->getMember();
        // TODO CRON
        $invitationManager->purgeOldInvitations($member);
        return new ApiResponse(array('member' => $member));
    }

    /**
     * Get member programs
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member programs",
     
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
    public function getProgramsAction()
    {
        $memberCollaborators = $this->getUser()->getMember()->getAllCollaborators();

        $programs = array();
        foreach ($memberCollaborators as $collaborator) {
            foreach ($collaborator->getAllContracts() as $contract) {
                $program = $contract->getProgram();
                $program !== null ? $programs[$program->getId()] = $program : null;
                $newProgram = $program->getNewProgram();
                $newProgram !== null ? $programs[$newProgram->getId()] = $newProgram : null;
            }
        }

        return new ApiResponse(array('programs' => array_values($programs)));
    }


    /**
     * Get member filleuls
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member filleuls",
     
     *     filters={
     *      {"name"="year", "requirement"="\dddd", "dataType"="integer", "required"=false, "description"="Filter by year"},
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=false, "description"="filter by program"},
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
    public function getFilleulsAction(Request $request)
    {
        $memberManager = $this->get('apr_program.member_manager');

        $year = $request->get('year');
        $programId = $request->get('programId');
        $user = $this->getUser();

        $data = $memberManager->getFilleulsDetails($user->getMember(), $year, $programId);
        $data['user'] = $user;

        return new ApiResponse($data);
    }

    /**
     * Get member points
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member points",
     
     *     requirements={
     *      {"name"="year", "requirement"="\dddd", "dataType"="integer", "required"=false, "description"="Filter by year"},
     *      {"name"="programId", "requirement"="\d+", "dataType"="integer", "required"=false, "description"="filter by program"},
     *     },
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
    public function getPointsAction(Request $request)
    {
        $memberManager = $this->get('apr_program.member_manager');

        $year = $request->get('year');
        $programId = $request->get('programId');
        $member = $this->getUser()->getMember();

        $data = $memberManager->BuildPoints($member, $programId, $year);

        return new ApiResponse($data);
    }

    /**
     * Get member collaborators
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member collaborators",
     
     *     filters={
     *      {"name"="party_id", "dataType"="integer", "required"=false, "description"="party id"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *              "400102"="Party not found"
     *            },
     *          500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getCollaboratorsAction(Request $request)
    {
        $contractManager = $this->get('apr_contract.contract_manager');
        $collaboratorManager = $this->get('apr_contract.collaborator_manager');

        $member = $this->getUser()->getMember();

        $partyId = $request->get('party_id');
        if ($partyId) {
            $party = $contractManager->loadPartyById($partyId);
            if ($party === null) {
                throw new ApiException(400102);
            }
            $collaborators = array($collaboratorManager->loadCollaboratorByMember($party, $member));
        } else {
            $collaborators = $member->getAllCollaborators();
        }

        return new ApiResponse(array('collaborators' => $collaborators));
    }

    /**
     * Get member participates
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member participates",
     
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
    public function getParticipatesAction(Request $request)
    {
        $member = $this->getUser()->getMember();
        $allParticipates = $member->getAllParticipatesTo();
        return new ApiResponse(array('participates' => $allParticipates));
    }

    /**
     * Get member parties
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member parties",
     
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
    public function getPartiesAction()
    {
        return new ApiResponse(array('parties' => $this->getUser()->getMember()->getAllParties()));
    }

    /**
     * Get member college
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member college",
     
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
    public function getCollegeAction()
    {
        $member = $this->getUser()->getMember();

        return new ApiResponse(array('college' => $member->getCollege()));
    }

    /**
     * Get member college
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member college",
     
     *     filters={
     *      {"name"="siren", "dataType"="integer", "required"=false, "description"="corporate siren"},
     *     },
     *     statusCodes={
     *        200={
     *          "200"="The request has succeeded"
     *         },
     *        400={
     *          "40029"="Corporate not found"
     *         },
     *        500={
     *          "5001"="An internal error has occurred"
     *        }
     *     }
     * )
     */
    public function getCollegesAction(Request $request)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $collegeManager = $this->get('apr_corporate.college_manager');

        $member = $this->getUser()->getMember();
        $siren = $request->get('siren');
        if ($siren) {
            $corporate = $corporateManager->loadCorporateBySiren($siren);
            if ($corporate === null) {
                throw new ApiException(40029);
            }
            $colleges = $collegeManager->getAllCollegesOfMemberForCorporate($member, $corporate);
        } else {
            $colleges = $collegeManager->getAllCollegesOfMember($member);
        }

        return new ApiResponse(array('colleges' => $colleges));
    }

    /**
     * Get member autoEntrepreneur
     *
     * @ApiDoc(
     *     section="01.04. Member Services",
     *     description="Get member autoEntrepreneur",
     
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
    public function getAutoentrepreneurAction()
    {
        $member = $this->getUser()->getMember();

        return new ApiResponse(array('autoEntrepreneur' => $member->getAutoEntrepreneur()));
    }

}
