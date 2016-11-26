<?php

/**
 * This file defines the Easy Setting controller in the Bundle ProgramBundle for REST API
 *
 * @category ProgramBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\ProgramBundle\ApiController;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Apr\CorporateBundle\Entity\UploadedFile;
use Apr\CorporateBundle\Form\Type\UploadedFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class EasySettingController for API services
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
 * @RouteResource("EasySetting")
 */
class EasySettingController extends Controller
{

    /**
     * Get easy setting for program
     *
     * @ApiDoc(
     *     section="06.06. Easy Setting services",
     *     description="Get easy setting for program",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getAction($programId)
    {

        $programManager = $this->get('apr_program.program_manager');
        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        return new ApiResponse(array('easySetting' => $program->getEasySetting()));
    }

    /**
     * Update setting easy program : Create/Update program Step 2
     *  <br><strong>- Update simpleRate </strong>
     *  <br> Request format : [{"op": "replace", "path": "/simplerate", "value": 5}]
     *  <br><strong>- Update multiRate </strong>
     *  <br> Request format : [{"op": "replace", "path": "/multirate", "value": 3}]
     *
     * @ApiDoc(
     *     section="06.06. Easy Setting services",
     *     description="Update setting easy program",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        204={
     *            "204"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40034"="Program is not easy",
     *            "40035"="Can not edit program in «prod» status"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function patchAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, true);

        $easySetting = $program->getEasySetting();
        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'easy_setting');

        if (!$program->isEasy()) {
            throw new ApiException(40034);
        }

        foreach ($patch as $patchOperation) {
            switch ($patchOperation->op) {
                case 'replace':
                    if ($patchOperation->path === '/simplerate' && $easySetting->getSimpleRate() != $patchOperation->value) {
                        $easySetting->setSimpleRate($patchOperation->value);
                    } elseif ($patchOperation->path === '/multirate' && $easySetting->getMultiRate() != $patchOperation->value) {
                        $easySetting->setMultiRate($patchOperation->value);
                    }
                    $programManager->persistAndFlush($easySetting);
                    break;
            }
        }

        return new ApiResponse(null, 204);
    }

    /**
     * Get easy setting document
     *
     * @ApiDoc(
     *     section="06.06. Easy Setting services",
     *     description="Get easy setting document",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40034"="Program is not easy",
     *            "40035"="Can not edit program in «prod» status",
     *            "40049"="Setting document not found"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function getDocumentAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);
        if (!$program->isEasy()) {
            throw new ApiException(40034);
        }
        $document = $program->getEasySetting()->getDocument();

        if ($document === null) {
            throw new ApiException(40049);
        }

        $baseUrl = "/Uploads/" . $document->getFilename();
        $path = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $url = $this->container->get('templating.helper.assets')->getUrl($path . $baseUrl);

        $response = new Response(file_get_contents($url));
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET");
        $response->headers->set('Access-Control-Allow-Headers', '*');
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s.pdf"', $document->getTitle()));

        // prints the HTTP headers followed by the content
        $response->send();

        return $response;
    }

    /**
     * Update Easy Setting document
     *
     * @ApiDoc(
     *     section="06.06. Easy Setting services",
     *     description="Update Easy Setting document",
     
     *     requirements={
     *      {"name"="programId", "dataType"="string", "required"=true, "description"="program id"},
     *     },
     *     parameters= {
     *      {"name"="document", "dataType"="file", "required"=true, "description"="Easy setting document"},
     *     },
     *     statusCodes={
     *        200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40024"="Program not found",
     *            "40034"="Program is not easy",
     *            "40035"="Can not edit program in «prod» status",
     *            "40057"="Document format must be PDF"
     *            },
     *        403={
     *            "4031"="Denied access to program",
     *            },
     *        500={
     *            "5001"="An internal error has occurred"
     *            },
     *
     *     }
     * )
     */
    public function postDocumentAction($programId, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $formValidator = $this->get('api.form.validator');

        $program = $programManager->loadProgramById($programId);

        $programManager->securityCheck($this->getUser(), $program, false);

        if (!$program->isEasy()) {
            throw new ApiException(40034);
        }

        $file = $request->files->get('document');

        if ($file === null) {
            throw new ApiException(4001);
        }

        if ($file->getMimeType() !== 'application/pdf') {
            throw new ApiException(40057);
        }

        $document = new UploadedFile();
        $program->getEasySetting()->setDocument($document);
        if ($formValidator->validateData(new UploadedFileType(), array('file' => $file), $document)) {
            $document->uploadFile('doc_' . $program->getFileName(), 'EasyDocs');

            $programManager->persistAndFlush($program);
        } else {
            throw new ApiException(4000, array('errors' => $formValidator->getErrors()));
        }
        return new ApiResponse();
    }

}
