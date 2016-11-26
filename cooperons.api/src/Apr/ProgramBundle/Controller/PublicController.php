<?php

namespace Apr\ProgramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/public")
 */
class PublicController extends Controller
{

    private function securityCheck($tokenValue)
    {
        $tokenManager = $this->get('apr_user.token_manager');

        $token = $tokenManager->loadToken($tokenValue);

        if($token) {
            $tokenObject = $token->getObject();
            if ($token->isParticipatesTo()) {
                return array('participatesTo' => $tokenObject, 'member' => $tokenObject->getMember(), 'program' => $tokenObject->getProgram());
            }
            if ($token->hasProgram()) {
                return array('invitation' => $tokenObject, 'program' => $tokenObject->getProgram());
            }
            return array('invitation' => $tokenObject);
        }

        return null;
    }

    private function standardController($token = null)
    {
        if(is_null($token)) {
            $request = $this->getRequest();
            $token = $request->get('tkn');
        }
        $result = $this->securityCheck($token);
        if (isset($result['program'])) unset($result['program']);
        if (is_null($result)) {
            return $this->redirect($this->container->getParameter('frontend.base.uri'));
        } else {
            return array(
                'token' => $token,
            );
        }
    }

    /**
     * @Route("/contract/landing", name="landing_contract")
     * @Template("AprCorporateBundle:Marketing:landingContract.html.twig")
     */

    public function landingContractAction()
    {
        return $this->standardController();
    }

    /**
     * @Route("/auto_entrepreneur/landing", name="landing_auto_entrepreneur")
     * @Template("AprAutoEntrepreneurBundle:Marketing:landingAutoEntrepreneur.html.twig")
     */

    public function landingAutoEntrepreneurAction()
    {
        return $this->standardController();
    }

    /**
     * @Route("/college/landing", name="landing_college")
     * @Template("AprCorporateBundle:Marketing:landingCollege.html.twig")
     */

    public function landingCollegeAction()
    {
        return $this->standardController();
    }

    /**
     * @Route("/corporate/landing/", name="landing_invitation_plus")
     * @Template("AprProgramBundle:Marketing:landingInvitationPlus.html.twig")
     */

    public function landingInvitationPlusAction()
    {
        return $this->standardController();
    }

    /**
     * @Route("/easy/landing/", name="landing_easy")
     * @Template("AprProgramBundle:Marketing:landingEasy.html.twig")
     */

    public function landingEasyAction(Request $request)
    {
        $token = $request->get('tkn');
        $result = $this->securityCheck($token);
        if (is_null($result) || !$result['program']->isEasy()) return $this->redirect($this->container->getParameter('frontend.base.uri'));
        $result['token'] = $token;
        
        return $result;
    }

    /**
     * @Route("/faq/contract/{token}", defaults={"token"=null}, name="faq_contract")
     * @Template("AprCorporateBundle:Marketing:faqContract.html.twig")
     */

    public function faqContractAction($token = null)
    {
        return $this->standardController($token);
    }

    /**
     * @Route("/faq/auto_entrepreneur/{token}", defaults={"token"=null}, name="faq_auto_entrepreneur")
     * @Template("AprAutoEntrepreneurBundle:Marketing:faqAutoEntrepreneur.html.twig")
     */

    public function faqAutoEntrepreneurAction($token = null)
    {
        return $this->standardController($token);

    }

    /**
     * @Route("/faq/college/{token}", defaults={"token"=null}, name="faq_college")
     * @Template("AprCorporateBundle:Marketing:faqCollege.html.twig")
     */
    public function faqCollegeAction($token = null)
    {
        return $this->standardController($token);

    }

    /**
     * @Route("/faq/member/{token}", defaults={"token"=null}, name="faq_member")
     * @Template("AprProgramBundle:Marketing:faqMember.html.twig")
     */

    public function faqMemberAction($token = null)
    {
        return $this->standardController($token);
    }

    /**
     * @Route("/faq/corporate/{isEasy}/{token}", defaults={"token"=null, "isEasy"=0}, name="faq_corporate")
     * @Template("AprProgramBundle:Marketing:faqCorporate.html.twig")
     */

    public function faqCorporateAction($isEasy = 0, $token = null)
    {
        $result = $this->securityCheck($token);
        if (is_null($result)) {
            // return $this->redirect($this->generateUrl('dispatcher'));
            // Fondative
            return $this->redirect($this->container->getParameter('frontend.base.uri'));
        } else {
            // Pour ne pas afficher les CGV du Program dans le footer ...
            unset($result['program']);
            return array_merge($result, array(
                'isEasy' => $isEasy,
                'token' => $token
            ));
        }
    }

    /**
     * @Route("/cgu/cooperons/{token}", defaults={"token"=null}, name="view_cgu_cooperons_plus")
     * @Method("GET")
     * @Template("AprProgramBundle:Legal:cgu.html.twig")
     */
    public function viewCGUCooperonsPlusAction($token = null)
    {
        return $this->standardController($token);

    }


    /**
     * @Route("/cgv/cooperons/{token}", name="view_cgv_cooperons_plus")
     * @Method("GET")
     * @Template("AprProgramBundle:Legal:cgv.html.twig")
     */
    public function viewCGVCooperonsPlus($token)
    {
        return $this->standardController($token);

    }

    /**
     * @Route("/cgv/program/{token}", name="view_cgv_program")
     * @Method("GET")
     * @Template("AprProgramBundle:Program:view_cg_program_no_menu.html.twig")
     */
    public function viewCGVProgramAction($token)
    {
        $result = $this->securityCheck($token);
        if (is_null($result)) return $this->redirect($this->container->getParameter('frontend.base.uri'));

        return array_merge($result, array(
            'token' => $token
        ));
    }
}
