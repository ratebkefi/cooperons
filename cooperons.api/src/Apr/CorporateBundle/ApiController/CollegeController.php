<?php
/**
 * This file defines the College controller in the Bundle CorporateBundle for REST API
 *
 * @category CorporateBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\CorporateBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiCorporateController for API services
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
 * @RouteResource("College")
 */
class CollegeController extends Controller
{


    /**
     * Partial modification
     *
     * @ApiDoc(
     *     section="03.02. College Services",
     *     description="Partial modification",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="college id"},
     *    },
     *     statusCodes={
     *        204={
     *            "204"="The request has succeeded"
     *            },
     *        400={
     *             "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *             "40061"="Wrong patch format",
     *             "40071"="College not found"
     *            },
     *        403={
     *             "4037"="Denied access to update college"
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
        $collegeManager = $this->get('apr_corporate.college_manager');
        $mailsManager = $this->get('apr_user.mailer');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $college = $collegeManager->loadCollegeById($id);

        if ($college === null) {
            throw new ApiException(40071);
        }

        $member = $this->getUser()->getMember();
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'college');
        $emails = array();

        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'broadcast':
                    $collegeManager->securityCheck($member, $college, true);
                    $collegeManager->broadcastNewCandidateAllColleges($college, $emails);
                    break;
                case 'leave':
                    $collegeManager->securityCheck($member, $college, true);
                    $invitation = $college->getInvitation();
                    $collegeManager->leaveCollege($college, $emails);
                    $invitation ? $collegeManager->removeAndFlush($invitation) : null;
                    break;
                case 'accept':
                    $collegeManager->securityCheck($member, $college, false);
                    $collegeManager->confirmCollege($college, $emails);
                    break;
                case 'confirm':
                    $collegeManager->securityCheck($member, $college, true);
                    $collegeManager->confirmCollege($college, $emails);
                    break;
                case 'change':
                    if ($operation->path === '/delegate') {
                        $collegeManager->securityCheck($member, $college, false);
                        $collegeManager->changeDelegate($college, $emails);
                    }
            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }


}