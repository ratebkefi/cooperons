<?php

/**
 * This file defines the QuarterlyTaxation controller in the Bundle AutoEntrepreneurBundle for REST API
 *
 * @category AutoEntrepreneurBundle
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
 * Class QuarterlyTaxationController for API services
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
 * @RouteResource("QuarterlyTaxation")
 */
class QuarterlyTaxationController extends Controller
{

    /**
     * Get quarterlyTaxations
     *
     * @ApiDoc(
     *     section="05.03. Quarterly Taxation management",
     *     description="Get waiting quarterly taxations",
     
     *     requirements={
     *     },
     *     filters={
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            },
     *        403={
     *            "40317"="Denied access to quarterly taxations",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction(Request $request) {
        // TODO check access right by Symfony security module after upgrade
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(40317);
        }
        $autoEntrepreneurManager = $this->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        return new ApiResponse(array('quarterlyTaxations' => $autoEntrepreneurManager->buildQuarterlyTaxations()));
    }



}
