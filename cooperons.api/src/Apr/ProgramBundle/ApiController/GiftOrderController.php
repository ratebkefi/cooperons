<?php
/**
 * This file defines the GiftOrder controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\ProgramBundle\ApiController;

use Apr\CoreBundle\Tools\Tools;
use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiFileResponse;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class GiftOrderController for API services
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("GiftOrder")
 */
class GiftOrderController extends Controller
{

    /**
     * Returns a collection of Gifts
     *
     * @ApiDoc(
     *     section="06.05. Gift order services",
     *     description="Get Gifts History",
     
     *     requirements={},
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction()
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $memberManager = $this->get('apr_program.member_manager');
        $avantageManager = $this->get('apr_program.avantage_manager');
        $quarter = Tools::DateTime('Q');
        $year = Tools::DateTime('Y');
        $labelOperation = Tools::getLabelOperationById($quarter) . " " . $year;
        $allGiftOrders = $avantageManager->getAllGiftOrders();

        foreach ($allGiftOrders as $gift) {
            $gift->setLabelOperation($gift->getLabelOperation());
            $gift->setFileName($gift->getFileName());
        }

        $data = array(
            'year' => $year,
            'quarter' => $quarter,
            'labelOperation' => $labelOperation,
            'membersWithGiftsPending' => $memberManager->buildGiftsPendingDetails(),
            'giftOrders' => $allGiftOrders,
        );

        return new ApiResponse($data);
    }


    /**
     * Create Gif Order and generate excel file
     *
     * @ApiDoc(
     *     section="06.05. Gift order services",
     *     description="Create Gif Order",
     
     *     requirements={},
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function postAction()
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $avantageManager = $this->get('apr_program.avantage_manager');
        $avantageManager->createGiftOrder();

        return new ApiResponse();
    }

    /**
     * Partial updating for gift order
     *  <br><strong>- Confirm gift order </strong>
     *  <br> Request format : [{"op": "confirm", "path": "/"}]
     *
     * @ApiDoc(
     *     section="06.05. Gift order services",
     *     description="Confirm Gif Order",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of Gift Order"},
     * },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40027"="Gift Order not found"
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
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $avantageManager = $this->get('apr_program.avantage_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');
        $mailsManager = $this->get('apr_user.mailer');
        $giftOrder = $avantageManager->loadGiftOrderById($id);

        if ($giftOrder == null) {
            throw new ApiException(40027);
        }

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'gift_order');
        $emails = array();

        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'confirm':
                    $avantageManager->confirmGiftOrder($giftOrder, $emails);
                    break;
            }
        }

        $mailsManager->sendMails($emails);

        return new ApiResponse(null, 204);
    }

    /**
     * Get gift order in Xls|json format
     *
     * @ApiDoc(
     *     section="06.05. Gift order services",
     *     description="Download Gift Order",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of Gift Order"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40027"="Gift Order not found",
     *            "40028"="Gift Order file not found"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function getAction($id, Request $request)
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }
        $avantageManager = $this->get('apr_program.avantage_manager');

        $giftOrder = $avantageManager->loadGiftOrderById($id);

        if ($giftOrder == null) {
            throw new ApiException(40027);
        }
        $format = $request->get('_format');
        if ($format === 'xls') {
            return new ApiFileResponse(basename($giftOrder->getFileName(), '.xls'), 'xls', ApiFileResponse::GIFT_ORDER);
        } else {
            return new ApiResponse(array('giftOrder' => $giftOrder));
        }
    }

}
