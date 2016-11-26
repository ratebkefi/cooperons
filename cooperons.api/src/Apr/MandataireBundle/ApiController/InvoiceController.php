<?php
/**
 * This file defines the Invoice controller in the Bundle MandataireBundle for REST API
 *
 * @category MandataireBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\MandataireBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiFileResponse;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class InvoiceController for API services
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
 * @RouteResource("Invoice")
 */
class InvoiceController extends Controller
{

    /**
     * Get invoice in json|pdf format
     *
     * @ApiDoc(
     *     section="04.04. Invoice services",
     *     description="Download Invoice",
     
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Invoice reference"},
     *      },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40041"="Invoice not found",
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
        $invoicesManager = $this->get('apr_mandataire.invoices_manager');

        $user = $this->getUser();
        $member = $user->getMember();
        $format = $request->get('_format');

        $invoice = $invoicesManager->loadInvoiceById($id);
        if ($invoice == null) {
            throw new ApiException(40026);
        }

        $mandataire = $invoice->getMandataire();
        $contract = $mandataire->getContract();
        if($contract) {
            $party = $contract->getAuthorizedParty($member);
            $notAuthorized = is_null($party);
        } else {
            $party = $mandataire->getClient();
            $notAuthorized = (is_null($party) or !$party->isAuthorized($member));
        }

        if ($notAuthorized) {
            throw new ApiException(40310);
        }

        if($format === 'pdf'){
            return new ApiFileResponse(basename($invoice->getFileName(), '.pdf'), 'pdf', ApiFileResponse::INVOICE);
        } else{
            return new ApiResponse(array('invoice' => $invoice));
        }
    }

    /**
     * Get pending invoices
     *
     * @ApiDoc(
     *     section="04.04. Invoice services",
     *     description="Get pending invoices",
     
     *
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        403={
     *            "403"="Denied access",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *     }
     * )
     */
    public function cgetAction()
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new ApiException(403);
        }

        $invoicesManager = $this->get('apr_mandataire.invoices_manager');

        return new ApiResponse(array('invoices' => $invoicesManager->buildInvoices()));
    }

}
