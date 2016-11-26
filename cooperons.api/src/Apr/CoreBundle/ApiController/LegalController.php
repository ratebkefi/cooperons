<?php

/**
 * This file defines the Legal controller for returning legal HTML page
 *
 * @category CoreBundle
 * @package ApiController
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\CoreBundle\ApiController;

use Apr\CoreBundle\ApiResponse\ApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Apr\CoreBundle\ApiException\ApiException;

/**
 * Class LegalController for legal pages
 *
 * @category CoreBundle
 * @package Controller
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */
class LegalController extends Controller
{

    /**
     * Get program's operations
     *
     * @ApiDoc(
     *     section="***. Legal HTML returns",
     *     description="Get program's operations",
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *         400 ={
     *            "40024"="Program not found",
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
     * @Route("/programs/{idProgram}/operations", name="legal_program_operations")
     * @Method("GET")
     */
    
    public function getProgramsOperationsAction($idProgram)
    {
        $user = $this->getUser();
        $programManager = $this->get('apr_program.program_manager');
        $program = $programManager->loadProgramById($idProgram);

        $programManager->securityCheck($user, $program, false);
        return $this->render('AprProgramBundle:Legal:cg_program_func_view_operations.html.twig', array('program' => $program));
    }

    /**
     * Get program's clauses
     *
     * @ApiDoc(
     *     section="***. Legal HTML returns",
     *     description="Get program's clauses",
     *     views = {"html"},
     *     requirements={
     *      {"name"="idProgram", "requirement"="\d+", "dataType"="integer", "required"=true, "description"="Id of program"},
     *     },
     *     statusCodes={
     *          200={
     *            "200"="The request has succeeded"
     *            },
     *         400 ={
     *            "40024"="Program not found",
     *          },
     *        403={
     *            "4031"="Denied access to program"
     *          },     *        500={
     *            "5001"="An internal error has occurred"
     *            }
     *
     *     }
     * )
     * @Route("/programs/{idProgram}/clauses", name="legal_program_clauses")
     * @Method("GET")
     */
    
    public function getProgramsClausesAction($idProgram)
    {
        $user = $this->getUser();
        $programManager = $this->get('apr_program.program_manager');
        $program = $programManager->loadProgramById($idProgram);
        $programManager->securityCheck($user, $program, false);

        $clauses = array(
            1 => array(
                'title' => 'Définitions',
                'content' => $this->render('AprProgramBundle:Legal:clause1.html.twig')->getContent()
            ),
            2 => array(
                'title' => 'Description du Programme',
                'content' => $this->render('AprProgramBundle:Legal:clause2.html.twig', array('program' => $program))->getContent()
            ),
            3 => array(
                'title' => 'Fonctionnement du Programme',
                'content' => $this->render('AprProgramBundle:Legal:clause3.html.twig', array('program' => $program))->getContent()
            ),
            4 => array(
                'title' => 'Clause Limitative de Responsabilité',
                'content' => $this->render('AprProgramBundle:Legal:clause4.html.twig')->getContent()
            ),
            5 => array(
                'title' => 'Durée du contrat - Résiliation',
                'content' => $this->render('AprProgramBundle:Legal:clause5.html.twig')->getContent()
            ),
            6 => array(
                'title' => 'Modifications',
                'content' => $this->render('AprProgramBundle:Legal:clause6.html.twig')->getContent()
            ),
            7 => array(
                'title' => 'Divers',
                'content' => $this->render('AprProgramBundle:Legal:clause7.html.twig')->getContent()
            ),
        );
        return new ApiResponse(array('clauses' => $clauses));
    }

    /**
     * Get provision footer for programs
     *
     * @ApiDoc(
     *     section="***. Legal HTML returns",
     *     description="Get provision footer for programs",
     * )
     * @Route("/programs/provision/footer", name="legal_program_provision_footer")
     * @Method("GET")
     */
    public function GetProgramsProvisionFooterAction()
    {
        return $this->render('AprProgramBundle:Legal:provision_footer.html.twig');
    }
}
