<?php

/**
 * This file defines the User controller in the Bundle UserBundle for REST API
 *
 * @category UserBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\UserBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\UserBundle\Form\Type\RegistrationFormType;
use Apr\UserBundle\Form\Type\EditFormType;
use Apr\AutoEntrepreneurBundle\Form\Type\AutoEntrepreneurType;
use Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class UserController for API services
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
 * @RouteResource("User")
 */
class UserController extends Controller
{

    /**
     * Get connected user
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Get connected user",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          403={
     *            "403"="Denied access"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAction()
    {
        return new ApiResponse(array('user' => $this->getUser()));
    }

    /**
     * Allocated For Administrator : get token & roles to connect as a member
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Get user authentication token",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          403={
     *            "403"="Denied access"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getConnectAction($id)
    {
        $userManager = $this->get('apr_user.user_manager');
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');


        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        };

        $user = $userManager->loadUserById($id);
        $data = array(
            'token' => $jwtManager->create($user),
            'roles' => $user->getRoles()
        );

        return new ApiResponse($data);
    }

    /**
     * Get member by user id
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Get member by user id",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          403={
     *            "403"="Denied access"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getMemberAction($id)
    {
        $userManager = $this->get('apr_user.user_manager');

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        };

        $user = $userManager->loadUserById($id);

        return new ApiResponse(array('member' => $user->getMember()));
    }

    /**
     * Get users
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Get users",
     
     *     filters = {
     *     {"name"="key", "dataType"="string", "required"=false, "description"="search by key"},
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
    public function cgetAction(Request $request)
    {
        $key = $request->get('key');
        $userManager = $this->get('apr_user.user_manager');
        $users = $userManager->searchAPI($key);

        return new ApiResponse(array('users' => $users));
    }

    /**
     * Update user account
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Update user account",
     
     *     parameters={
     *      {"name"="user[email]", "dataType"="string", "required"=true, "description"="user email"},
     *      {"name"="user[plainPassword]", "dataType"="string", "required"=true, "description"="user password"},
     *      {"name"="user[lastName]", "dataType"="string", "required"=true, "description"="user lastName"},
     *      {"name"="user[firstName]", "dataType"="string", "required"=true, "description"="user firstName"},
     *      {"name"="contact[phone]", "dataType"="string", "required"=true, "description"="user phone"},
     *      {"name"="contact[address]", "dataType"="string", "required"=true, "description"="user address"},
     *      {"name"="contact[secondAddress]", "dataType"="string", "required"=false, "description"="user additional address"},
     *      {"name"="contact[city]", "dataType"="string", "required"=true, "description"="user city"},
     *      {"name"="contact[postalCode]", "dataType"="string", "required"=true, "description"="user postalCode"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *              "4000"="Failed data validation",
     *              "4001"="No parameter input",
     *              "40073"="Invalid password"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function putAction(Request $request)
    {
        $data = $request->request->all();

        if (count($data) === 0) {
            throw new ApiException(4001);
        }
        $validator = $this->get('api.form.validator');
        if ($validator->validateData(new EditFormType(), $data)) {
            $registration = $validator->getData();
            $newUser = $registration['user'];
            $contact = $registration['contact'];

            $userManager = $this->get('apr_user.user_manager');
            $user = $this->getUser();
            $emailChanged = $newUser->getEmail() != $user->getEmail();
            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $encodedNewPassword = $encoder->encodePassword($newUser->getPlainPassword(), $user->getSalt());

            if ($user->getPassword() != $encodedNewPassword) {
                throw new ApiException(40073);
            }
            $user = $userManager->updateUserContact($user, $newUser, $contact);
            $data = array();
            if ($emailChanged) {
                $memberManager = $this->get('apr_program.member_manager');
                $mailsManager = $this->get('apr_user.mailer');
                $activationUrl = $this->container->getParameter('front.activate.account') . '/' . $user->getConfirmationToken();
                $emails = array($memberManager->getActivateMail($user, $activationUrl));
                $mailsManager->sendMails($emails);
                $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
                $data['jwtAuth'] = array(
                    'username' => $user->getUsername(),
                    'roles' => $user->getRoles(),
                    'token' => $jwtManager->create($user),
                );
            }
            return new ApiResponse($data);
        } else {
            $errors = $validator->getErrors();
            throw new ApiException(4000, array('errors' => $errors));
        }
    }

    /**
     * Partial modification
     *  <br>- Resend activation email <strong> Request format : </strong> [{"op": "resend", "path": "/email"}]
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Partial modification",
     
     *     statusCodes={
     *          204={
     *            "204"="The request has succeeded"
     *            },
     *          400={
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function patchAction(Request $request)
    {
        $memberManager = $this->get('apr_program.member_manager');
        $mailsManager = $this->get('apr_user.mailer');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $user = $this->getUser();

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'user');

        $emails = array();
        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'resend':
                    if ($operation->path === '/email') {
                        $activationUrl = $this->container->getParameter('front.activate.account') . '/' . $user->getConfirmationToken();
                        $emails[] = $memberManager->getActivateMail($user, $activationUrl);
                    }
                    break;
                case 'connect' :
                    if ($operation->path === '/token') {
                        $memberManager->connectToken($user, $operation->token, $emails);
                    }
                    break;
            }
        }
        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

    /**
     * Get autontrepreneur
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Get autontrepreneur",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40076"="User not found"
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAutoentrepreneurAction($id)
    {
        $userManager = $this->get('apr_user.user_manager');
        $user = $userManager->loadUserById($id);

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        if ($user === null) {
            throw new ApiException(40076);
        }

        $autoEntrepreneur = $user->getMember()->getAutoEntrepreneur();

        return new ApiResponse(array('autoEntrepreneur' => $autoEntrepreneur));
    }

}
