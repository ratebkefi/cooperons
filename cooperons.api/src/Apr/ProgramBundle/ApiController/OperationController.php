<?php

/**
 * This file defines the Operation controller in the Bundle ProgramBundle for REST API
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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Apr\ProgramBundle\Entity\OperationCredit;
use Apr\ProgramBundle\Form\Type\OperationCreditType;

/**
 * Class OperationController for API services
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
 * @RouteResource("Operation")
 */
class OperationController extends Controller
{

    /**
     * Get program's operations
     *
     * @ApiDoc(
     *     section="06.03. Operation services",
     *     description="Get program's operations",
     
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status"
     *          },
     *        403={
     *            "4031"="Denied access to program"
     *          },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function cgetAction($idProgram)
    {
        $user = $this->getUser();
        $programManager = $this->get('apr_program.program_manager');
        $program = $programManager->loadProgramById($idProgram);

        $programManager->securityCheck($user, $program, false);

        return new ApiResponse(array('operations' => $program->getAllOperations()));
    }

    /**
     * Adding program's operation
     *
     * @ApiDoc(
     *     section="06.03. Operation services",
     *     description="Adding program's operation",
     
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     input = {
     *           "class"="Apr\ProgramBundle\Form\Type\OperationCreditType",
     *          "name"="",
     *    },
     *     statusCodes={
     *          201={
     *            "201"="The ressource is created"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40024"="Program not found",
     *            "4035"="Can not edit program in «prod» status"
     *          },
     *        403={
     *            "4031"="Denied access to program"
     *          },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function postAction($idProgram, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $operationManager = $this->get('apr_program.operation_credit_manager');
        $validator = $this->get('api.form.validator');

        $program = $programManager->loadProgramById($idProgram);

        $programManager->securityCheck($this->getUser(), $program, true);

        $data = $request->request->all();
        // If label is not passed as a parameter, validation return true !!!
        isset($data['label']) ? null : $data['label'] = null;
        $operation = new OperationCredit($program);

        if ($validator->validateData(new OperationCreditType, $data, $operation)) {
            $operationManager->persistAndFlush($operation);
        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }

        return new ApiResponse(array('operation' => $operation), 201);
    }

    /**
     *  Update program's operation
     *
     * @ApiDoc(
     *     section="06.03. Operation services",
     *     description="Update program's operation",
     
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="idOperation", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of credit operation"},
     *     },
     *     input = {
     *           "class"="Apr\ProgramBundle\Form\Type\OperationCreditType",
     *          "name"="",
     *    },
     *     statusCodes={
     *          204={
     *            "204"="The ressource is updated"
     *            },
     *        400={
     *            "4000"="Failed data validation",
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40059"="Credit operation not found in program",
     *          },
     *        403={
     *            "4031"="Denied access to program",
     *          },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function putAction($idProgram, $idOperation, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $operationManager = $this->get('apr_program.operation_credit_manager');
        $validator = $this->get('api.form.validator');

        $program = $programManager->loadProgramById($idProgram);

        $programManager->securityCheck($this->getUser(), $program, true);

        $operation = $operationManager->getOperationById($idOperation);
        if ($operation === null || $operation->getProgram()->getId() !== $program->getId()) {
            throw new ApiException(40059);
        }

        $data = $request->request->all();
        // If label is not passed as a parameter, validation return true !!!
        isset($data['label']) ? null : $data['label'] = null;

        if ($validator->validateData(new OperationCreditType, $data, $operation)) {
            $operationManager->persistAndFlush($operation);
        } else {
            throw new ApiException(4000, array('errors' => $validator->getErrors()));
        }

        return new ApiResponse(204);
    }

    /**
     *  Delete program's operation
     *
     * @ApiDoc(
     *     section="06.03. Operation services",
     *     description="Delete program's operation",
     
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="idOperation", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of credit operation"},
     *     },
     *     statusCodes={
     *          204={
     *            "204"="The ressource is updated"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40059"="Credit operation not found in program",
     *          },
     *        403={
     *            "4031"="Denied access to program",
     *          },
     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     */
    public function deleteAction($idProgram, $idOperation)
    {
        $programManager = $this->get('apr_program.program_manager');
        $operationManager = $this->get('apr_program.operation_credit_manager');

        $program = $programManager->loadProgramById($idProgram);
        $programManager->securityCheck($this->getUser(), $program, true);

        $operation = $operationManager->getOperationById($idOperation);
        if ($operation === null || $operation->getProgram()->getId() !== $program->getId()) {
            throw new ApiException(40059);
        }

        $operationManager->removeAndFlush($operation);

        return new ApiResponse(null, 204);
    }

    /**
     * Partial updating program journals
     *  <br><strong>- Update operation description </strong>
     *  <br> Request format : [{"op": "describe", "path": "/"}]
     *
     * @ApiDoc(
     *     section="06.03. Operation services",
     *     description="Partial updating program journals",
     
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *      {"name"="idOperation", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of credit operation"},
     *     },
     *     statusCodes={
     *        204={
     *            "204"="The resource is updated"
     *            },
     *        400={
     *            "40024"="Program not found",
     *            "40035"="Can not edit program in «prod» status",
     *            "40060"="Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
     *            "40061"="Wrong patch format",
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
    public function patchAction($idProgram, $idOperation, Request $request)
    {
        $programManager = $this->get('apr_program.program_manager');
        $operationManager = $this->get('apr_program.operation_credit_manager');
        $patchValidator = $this->get('api.patch.data.format.validator');

        $program = $programManager->loadProgramById($idProgram);
        $programManager->securityCheck($this->getUser(), $program, true);

        $operation = $operationManager->getOperationById($idOperation);
        if ($operation === null || $operation->getProgram()->getId() !== $program->getId()) {
            throw new ApiException(40059);
        }

        $data = json_decode($request->getContent());
        $patch = $patchValidator->validateDataFormat($data, 'operation');

        foreach ($patch as $operation) {
            switch ($operation->op) {
                case 'describe':
                    $operation->setDescription($operation->value);
                    $operationManager->persistAndFlush($operation);
                    break;
            }
        }

        return new ApiResponse(null, 204);
    }

}
