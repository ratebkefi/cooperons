<?php
/**
 * This file defines the Token controller in the Bundle UserBundle for REST API
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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class PublicTokenController for API services
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
 * @RouteResource("Token")
 */
class PublicTokenController extends Controller
{
    /**
     * Get token
     *
     * @ApiDoc(
     *     section="01.03. Token services",
     *     description="Get token",
     
     *     requirements={
     *      {"name"="token", "dataType"="string", "required"=true, "description"="token"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "400200"="Token not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function getAction($token)
    {
        $tokenManager = $this->get('apr_user.token_manager');

        return new ApiResponse($tokenManager->loadToken($token));
    }
}
