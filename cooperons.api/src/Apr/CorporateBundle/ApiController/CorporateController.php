<?php
/**
 * This file defines the Corporate controller in the Bundle CorporateBundle for REST API
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
use Apr\CoreBundle\ApiResponse\ApiFileResponse;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\CoreBundle\Tools\Tools;
use Apr\CorporateBundle\Entity\Corporate;
use Apr\CorporateBundle\Form\Type\CorporateEditType;
use Apr\CorporateBundle\Form\Type\CorporateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class CorporateController for API services
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
 * @RouteResource("Corporate")
 */
class CorporateController extends Controller
{


    /**
     * Returns a collection of Corporates
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get Corporates",
     
     *     filters={
     *      {"name"="siren", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate SIREN"},
     *      {"name"="tvaIntracomm", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate TVA Intracomm"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction(Request $request)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $siren = $request->get('siren');
        if ($siren) return new ApiResponse(array('corporates' => array($corporateManager->loadCorporateBySiren($siren, true))));

        $tvaIntracomm = $request->get('tvaIntracomm');
        if ($tvaIntracomm) return new ApiResponse(array('corporates' => array($corporateManager->loadCorporateByTVAIntracomm($tvaIntracomm, true))));

        return new ApiResponse(array('corporates' => $corporateManager->getAllCorporates(true)));
    }

    /**
     * Get data to create new corporate
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get data to create new corporate",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function newAction()
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $countries = $corporateManager->getCountries();
        return new ApiResponse(array('countries' => $countries));
    }

    /**
     * Get corporate
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get corporate",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
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
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }

        return new ApiResponse(array('corporate' => $corporate));
    }

    /**
     * Create corporate
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Create corporate",
     
     *     requirements={},
     *    input={
     *      "class"="Apr\CorporateBundle\Form\Type\CorporateType",
     *      "name"=""
     *    },
     *     statusCodes={
     *        201={
     *            "201"="The request has succeeded"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40063"="Corporate with same TVA Intracomm is already existing",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $validator = $this->get('api.form.validator');
        $mailsManager = $this->get('apr_user.mailer');

        $data = $request->request->all();

        $corporate = $corporateManager->loadCorporateByTVAIntracomm($data['tvaIntracomm']);
        if ($corporate) {
            if ($corporate->getAdministrator()) {
                $data = array(
                    'existingCorporate' => $corporate,
                    'administratorMember' => $corporate->getAdministrator()->getMember()
                );
                throw new ApiException(40063, $data);
            }

        } else {
            $corporate = new Corporate();
        }

        if ($validator->validateData(new CorporateType(), $data, $corporate)) {
            $emails = array();
            $corporateManager->createCorporateWithAdministrator($corporate, $this->getUser()->getMember(), $emails);
            $mailsManager->sendMails($emails);
        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }
        return new ApiResponse(null, 201);
    }

    /**
     * Update corporate
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Update corporate",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *    input={
     *      "class"="Apr\CorporateBundle\Form\Type\CorporateEditType",
     *      "name"=""
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *             "40029"="Corporate not found"
     *            },
     *        403={
     *             "40314"="Denied access to update corporate"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function putAction($id, Request $request)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $validator = $this->get('api.form.validator');

        $data = $request->request->all();
        $member = $this->getUser()->getMember();

        $corporate = $corporateManager->loadCorporateById($id);

        if ($corporate === null) {
            throw new ApiException(40029);
        }

        if ($corporate->getAdministrator() === null || $corporate->getAdministrator()->getMember() !== $member) {
            throw new ApiException(40314);

        }

        if ($validator->validateData(new CorporateEditType(), $data, $corporate)) {

            $emails = array();
            $corporateManager->createCorporateWithAdministrator($corporate, $member, $emails);

        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }
        return new ApiResponse(null, 204);
    }


    /**
     * Get corporate administrator
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get corporate administrator",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
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
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }

        return new ApiResponse(array('administrator' => $corporate->getAdministrator()));
    }

    /**
     * Get corporate delegate
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get corporate delegate",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getDelegateAction($id)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }
        return new ApiResponse(array('delegate' => $corporate->getDelegate()));
    }

    /**
     * Get corporate colleges
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get corporate colleges",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getCollegesAction($id)
    {
        $collegeManager = $this->get('apr_corporate.college_manager');
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }

        $member = $this->getUser()->getMember();
        $college = $member->getCollege();

        $isAdministrator = ($corporate->getAdministrator() && ($corporate->getAdministrator()->getMember() == $member));
        $colleges = $collegeManager->getAllCollegesByCorporate($corporate, ($isAdministrator or ($college && $college->isDelegate())));


        return new ApiResponse(array('colleges' => $colleges));
    }

    /**
     * Get attestations
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get attestations",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getYearlyattestationsAction($id)
    {
        $collegeManager = $this->get('apr_corporate.college_manager');
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }


        $user = $this->getUser();
        $attestations = array();
        $data = array();

        $year = Tools::DateTime('Y') - 1;

        $member = $user->getMember();
        $isAdministrator = $corporate->getAdministrator() && $corporate->getAdministrator()->getMember()->getId() === $member->getId();
        $attestations = $collegeManager->buildYearlyAttestations($corporate, $isAdministrator ? null : $member);

        $data['year'] = $year;
        $data['attestations'] = $attestations;

        return new ApiResponse($data);
    }

    /**
     * Partial modification
     *  <br><strong>- Create college </strong>
     *  <br> Request format : [{"op": "create", "path": "/college"}]
     *  <br><strong>- Validate corporate </strong>
     *  <br> Request format : [{"op": "validate", "path": "/"}]
     *  <br><strong>- Cancel corporate </strong>
     *  <br> Request format : [{"op": "cancel", "path": "/"}]
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Partial modification",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        204={
     *            "204"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found",
     *             "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *             "40061"="Wrong patch format",
     *             "40069"="You are already in college",
     *            },
     *        403={
     *              "403"="Denied access"
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
        $corporateManager = $this->get('apr_corporate.corporate_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }

        $user = $this->getUser();
        $member = $user->getMember();

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'corporate');
        $emails = array();

        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'create':
                    if ($operation->path === '/college') {
                        if ($member->getCollege()) {
                            throw new ApiException(40069);
                        }
                        $collegeManager = $this->get('apr_corporate.college_manager');
                        $emails = array();
                        $collegeManager->candidateCollege($member, $corporate, $emails);
                    }
                    break;
                case 'validate':
                    if (!$user->hasRole('ROLE_SUPER_ADMIN') && !$user->hasRole('ROLE_ADMIN')) {
                        throw new ApiException(403);
                    }
                    $corporateManager->signAccordCadre($corporate, $emails);
                    break;
                case 'cancel':
                    if (!$user->hasRole('ROLE_SUPER_ADMIN') && !$user->hasRole('ROLE_ADMIN')) {
                        throw new ApiException(403);
                    }
                    $corporateManager->cancelCorporate($corporate, $emails);
                    break;
            }
        }

        if (!empty($emails)) {
            $mailsManager = $this->get('apr_user.mailer');
            $mailsManager->sendMails($emails);
        }

        return new ApiResponse(null, 204);
    }

    /**
     * Get corporate party
     *
     * @ApiDoc(
     *     section="03.01. Corporate services",
     *     description="Get corporate party",
     
     *     requirements={
     *      {"name"="siren", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Corporate Id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "40029"="Corporate not found"
     *            },
     *        403={
     *             "4039"="Denied access to corporate configuration"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getPartyAction($id)
    {
        $corporateManager = $this->get('apr_corporate.corporate_manager');

        $corporate = $corporateManager->loadCorporateById($id);
        if ($corporate === null) {
            throw new ApiException(40029);
        }

        $member = $this->getUser()->getMember();
        if ($corporate->getAdministrator()->getMember()->getId() !== $member->getId()) {
            throw new ApiException(4039);
        }
        return new ApiResponse(array('party' => $corporate->getParty()));
    }

}