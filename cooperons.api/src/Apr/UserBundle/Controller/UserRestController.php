<?php
namespace Apr\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;                  // @ApiDoc(resource=true, description="Filter",filters={{"name"="a-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}})
use FOS\RestBundle\Controller\Annotations\NamePrefix;       // NamePrefix Route annotation class @NamePrefix("bdk_core_user_userrest_")
use FOS\RestBundle\View\RouteRedirectView;                  // Route based redirect implementation
use FOS\RestBundle\View\View AS FOSView;                    // Default View implementation.
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation; 
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;  

/**
 * Controller that provides Restful sercies over the resource Users.
 *
 * @NamePrefix("apr_userrest_")
 * @author Santiago Diaz <santiago.diaz@me.com>
 */
class UserRestController extends Controller
{

    /**
     * Returns the overall user list.
     */
    public function getUsersAction()
    {
        $userManager = $this->get('apr_user.user_manager');
        $data = $userManager->findUsers();
        return array("data" => $data);
    }
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * 
     * Use the Security of the SensioFrameworkExtraBundle instead of the use of JMSSecurityExtraBundle
     */
    
    /**
     * Returns an user by username/email.
     *
     * @param string $slug Username or Email
     *
     * @return FOSView
     * @Security("has_role('ROLE_USER')") 
     
     */
    public function getUserAction($slug)
    {
        $view = FOSView::create();
        $userManager = $this->get('apr_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($slug);

        if ($user) {
            $view->setStatusCode(200)->setData($user);
        } else {
            $view->setStatusCode(404);
        }

        return $view;
    }

    /**
     * Creates a new User entity.
     * Using param_fetcher_listener: force
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="username", requirements="\d+", default="", description="Username.")
     * @RequestParam(name="email", requirements="\d+", default="", description="Email.")
     * @RequestParam(name="plainPassword", requirements="\d+", default="", description="Plain Password.")
     * @RequestParam(name="role", requirements="\d+", default="", description="Role.")
     *
     */
    public function postUsersAction(ParamFetcher $paramFetcher)
    {
        $request = $this->getRequest();
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setPlainPassword($request->get('plainPassword'));
        $user->addRole($request->get('role'));

        $validator = $this->get('validator');
        //UTILIZAR GRUPO DE VALIDACION 'Registration' DEL FOSUserBundle
        $errors = $validator->validate($user, array('Registration'));
        if (count($errors) == 0) {
            $userManager->updateUser($user);
            $param = array("slug" => $user->getUsername());
            $view = RouteRedirectView::create("byexample_demo_userrest_get_user", $param);
        } else {
            $view = $this->get_errors_view($errors);
        }
        return $view;
    }

    /**
     * Update an user by username/email.
     *
     * @param string $slug Username or Email
     */
    public function putUserAction($slug)
    {
        $request = $this->getRequest();
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByUsernameOrEmail($slug);
        if (!$user) {
            $view = FOSView::create();
            $view->setStatusCode(204);
            return $view;
        }

        if ($request->get('username')) {
            $user->setUsername($request->get('username'));
        }
        if ($request->get('email')) {
            $user->setEmail($request->get('email'));
        }
        if ($request->get('plainPassword')) {
            $user->setPlainPassword($request->get('plainPassword'));
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($user, array('Registration'));
        if (count($errors) == 0) {
            $userManager->updateUser($user);
            $view = FOSView::create();
            $view->setStatusCode(204);
        } else {
            $view = $this->get_errors_view($errors);
        }
        return $view;
    }

    /**
     * Delete an user by username/email.
     *
     * @param string $slug Username or Email
     *
     */
    public function deleteUserAction($slug)
    {
        $view = FOSView::create();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($slug);
        if ($user) {
            $userManager->deleteUser($user);
            $view->setStatusCode(204)->setData("User removed.");
        } else {
            $view->setStatusCode(204)->setData("No data available.");
        }
        return $view;
    }

    /**
     * Get the validation errors
     *
     * @param ConstraintViolationList $errors Validator error list
     *
     * @return FOSView
     */
    private function get_errors_view($errors)
    {
        $msgs = array();
        $it = $errors->getIterator();
        //$val = new \Symfony\Component\Validator\ConstraintViolation();
        foreach ($it as $val) {
            $msg = $val->getMessage();
            $params = $val->getMessageParameters();
            //using FOSUserBundle translator domain 'validators'
            $msgs[$val->getPropertyPath()][] = $this->get('translator')->trans($msg, $params, 'validators');
        }
        $view = FOSView::create($msgs);
        $view->setStatusCode(400);
        return $view;
    }

}

?>
