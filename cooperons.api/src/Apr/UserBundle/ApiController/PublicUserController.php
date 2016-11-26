<?php
/**
 * This file defines the Public User controller in the Bundle UserBundle for REST API
 *
 * @category UserBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\UserBundle\ApiController;


use Apr\CoreBundle\Tools\Tools;
use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\UserBundle\Entity\User;
use Apr\UserBundle\Form\Model\Registration;
use Apr\UserBundle\Form\Type\ModifyPasswordType;
use Apr\UserBundle\Form\Type\RegistrationFormType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Prefix;

/**
 * Class ApiUserController for API services
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
class PublicUserController extends Controller
{
    /**
     * Register new account
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Register new account",
     
     *     parameters={
     *      {"name"="user[email]", "dataType"="string", "required"=true, "description"="user email"},
     *      {"name"="user[plainPassword][first]", "dataType"="string", "required"=true, "description"="user password"},
     *      {"name"="user[plainPassword][second]", "dataType"="string", "required"=true, "description"="confirm user password"},
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
     *            "4000"="Failed data validation",
     *            "4001"="No parameter input"
     *            },
     *          401={
     *            "401"="Not Authorized"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function postAction(Request $request, $token)
    {
        $formValidator = $this->get('api.form.validator');
        $memberManager = $this->get('apr_program.member_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $data = array();
        $data['user'] = $request->request->get('user');
        $data['contact'] = $request->request->get('contact');

        if (!count($data['user']) || !count($data['contact'])) {
            throw new ApiException(4001);
        }

        if ($formValidator->validateData(new RegistrationFormType(), $data, new Registration())) {
            $registration = $formValidator->getData();
            $user = $registration->getUser();
            $contact = $registration->getContact();
            $emails = array();
            $memberManager->createMemberWithUserContact($user, $contact, $token, $emails);

            $activationUrl = $this->container->getParameter('front.activate.account') . '/' . $user->getConfirmationToken();
            $emails[] = $memberManager->getActivateMail($user, $activationUrl);
            $roles = $user->getRoles();
            $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
            $jwtAuth = array(
                'username' => $user->getUsername(),
                'token' => $jwtManager->create($user),
                'roles' => $roles
            );

            $mailsManager->sendMails($emails);

            return new ApiResponse($jwtAuth);
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
    }

    /**
     * Partial modification
     *  <br><strong>- Activate account </strong>
     *  <br> Request format : [{"op": "activate", "path": "/", "token":"ABC..."}]
     *  <br><strong>- Check email </strong>
     *  <br> Request format : [{"op": "check", "path": "/email", "value":"test@test.com"}]
     *  <br><strong>- Check confirmation token </strong>
     *  <br> Request format : [{"op": "check", "path": "/confirmationToken", "value":"ABC..."}]
     *  <br><strong>- Send email to reset password </strong>
     *  <br> Request format : [{"op": "forget", "path": "/password", "email":"test@test.com"}]
     *  <br><strong>- Reset password </strong>
     *  <br> Request format : [{"op": "reset", "path": "/password", "value":"pwd", "token":"ABC..."}]
     *
     * @ApiDoc(
     *     section="01.01 User services",
     *     description="Partial modification",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "4003"="Invalid email address",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            "40073"="Invalid password"
     *            },
     *        401={
     *            "4013"="Invalid Token",
     *            "4012"="This user does not have access to this section"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function patchAction(Request $request)
    {
        $userManager = $this->get('apr_user.user_manager');
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'public_user');

        $response = array();
        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'activate':
                    $user = $userManager->findUserBy(array('confirmationToken' => $operation->token));
                    if ($user === null) {
                        throw new ApiException(4013);
                    }

                    $user->setStatus('Active');
                    $user->setMailStatus('Active');
                    $userManager->persistAndFlush($user);
                    $response['jwtAuth'] = array(
                        'username' => $user->getUsername(),
                        'roles' => $user->getRoles(),
                        'token' => $jwtManager->create($user),
                    );
                    break;
                case 'check':
                    if ($operation->path === '/email') {
                        $user = $userManager->findUserByEmail($operation->value);
                        $response['checkMail'] = array(
                            'email' => $operation->value,
                            'isAllocated' => $user !== null
                        );
                    } else if ($operation->path === '/confirmationToken') {
                        $user = $userManager->findUserByConfirmationToken($operation->value);
                        $response['checkConfirmationToken'] = array(
                            'token' => $operation->value,
                            'isValid' => $user !== null
                        );
                    }
                    break;
                case 'forget':
                    if ($operation->path === '/password') {
                        $user = $userManager->findUserByUsernameOrEmail($operation->email);
                        if ($user === null) {
                            throw new ApiException(4003);
                        }

                        if (null === $user->getConfirmationToken()) {
                            $tokenGenerator = $this->get('fos_user.util.token_generator');
                            $user->setConfirmationToken($tokenGenerator->generateToken());
                        }

                        $user->setPasswordRequestedAt(Tools::DateTime());
                        $user->setStatus('MPO');
                        $userManager->updateUser($user);
                        $mailsManager = $this->get('apr_user.mailer');
                        $url = $this->container->getParameter('front.forget.password') . '/' . $user->getConfirmationToken();
                        $mailsManager->sendResettingEmailMessage($user, $url);
                    }
                    break;
                case 'reset':
                    if ($operation->path === '/password') {
                        $user = $userManager->findUserByConfirmationToken($operation->token);
                        if ($user === null) {
                            throw new ApiException(4013);
                        }

                        if (!is_object($user) || !$user instanceof User) {
                            throw new ApiException(4012);
                        }

                        if ($userManager->validatePassword($operation->value)) {
                            $user->setPlainPassword($operation->value);
                            $userManager->updateUser($user);
                            $response['jwtAuth'] = array(
                                'username' => $user->getUsername(),
                                'roles' => $user->getRoles(),
                                'token' => $jwtManager->create($user),
                            );
                        } else {
                            throw new ApiException(40073);
                        }

                    }
                    break;
            }
        }
        return new ApiResponse($response);
    }

}