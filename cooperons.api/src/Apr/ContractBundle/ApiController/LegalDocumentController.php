<?php

/**
 * This file defines the Contract controller in the Bundle ContractBundle for REST API
 *
 * @category ContractBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\ContractBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Apr\AutoEntrepreneurBundle\Form\Type\RecruitmentSettingsType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ApiContractController for API services
 *
 * @category ContractBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */

/**
 * @RouteResource("LegalDocument")
 */
class LegalDocumentController extends Controller
{

    /**
     * Get legal document
     *
     * @ApiDoc(
     *     section="02.04. Legal Document Services",
     *     description="Get legal document",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Legal document id"},
     *    },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400106"="Legal Document not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract"
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
        $legalDocumentManager = $this->get('apr_contract.legal_document_manager');
        $legalDocument = $legalDocumentManager->loadLegalDocumentById($id);

        $legalDocumentManager->securityCheck($this->getUser(), $legalDocument);

        return new ApiResponse(array('legalDocument' => $legalDocument));
    }

    /**
     * Copy legal document for editing
     *
     * @ApiDoc(
     *     section="02.04. Legal Document Services",
     *     description="Create legal document copy",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Legal document id"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *             "400106"="Legal document not found",
     *            },
     *        403={
     *             "403102"="Denied access to contract",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function editAction($id)
    {
        $legalDocumentManager = $this->get('apr_contract.legal_document_manager');
        $legalDocument = $legalDocumentManager->loadLegalDocumentById($id);

        $legalDocumentManager->securityCheck($this->getUser(), $legalDocument, true);

        $legalDocumentCopy = $legalDocumentManager->copyLegalDocument($legalDocument);

        return new ApiResponse(array('legalDocumentCopy' => $legalDocumentCopy));
    }

    /**
     * Delete contract
     *
     * @ApiDoc(
     *     section="02.04. Legal Document Services",
     *     description="Delete legal document",
     
     *     requirements={
     *      {"name"="id", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of legal document"},
     *  },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "400106"="Legal document not found",
     *            "400121"="Can not delete legal document"
     *            },
     *        403={
     *            "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function deleteAction($id)
    {
        $legalDocumentManager = $this->get('apr_contract.legal_document_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $legalDocument = $legalDocumentManager->loadLegalDocumentById($id);

        $legalDocumentManager->securityCheck($this->getUser(), $legalDocument, true);

        $emails = array();
        $legalDocumentManager->removeLegalDocument($legalDocument, $emails);
        $mailsManager->sendMails($emails);
        return new ApiResponse(null,204);
    }

    /**
     * Partial updating legal document
     * <br><strong>- Publish legal document </strong>
     * <br> Request format : [{"op": "publish", "path": "/publish"}]
     * <br><strong>- Agree legal document </strong>
     * <br> Request format : [{"op": "agree", "path": "/agree"}]
     * @ApiDoc(
     *     section="02.04. Legal Document Services",
     *     description="Partial updating legal document",
     
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *          400={
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
     *            "400106"="Legal document not found",
     *
     *            },
     *        403={
     *             "403102"="Denied access to contract"
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *     }
     * )
     */
    public function patchAction($id, Request $request)
    {
        $patchValidator = $this->get('api.patch.data.format.validator');
        $legalDocumentManager = $this->get('apr_contract.legal_document_manager');
        $mailsManager = $this->get('apr_user.mailer');

        $legalDocument = $legalDocumentManager->loadLegalDocumentById($id);
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'legal_document');
        $member = $this->getUser()->getMember();

        $emails = array();
        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'publish':
                    $legalDocumentManager->securityCheck($this->getUser(), $legalDocument, true, false);
                    $legalDocumentManager->publishLegalDocument($legalDocument, $emails);
                    break;
                case 'agree':
                    $legalDocumentManager->securityCheck($this->getUser(), $legalDocument, false);
                    $legalDocumentManager->agreeLegalDocument($legalDocument, $emails);
                    break;
            }
            $mailsManager->sendMails($emails);
        }
        return new ApiResponse(array());
    }
}
