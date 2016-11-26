<?php

namespace Apr\ProgramBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\MailInvitation;

class MailsManager extends BaseManager {
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function loadByCode($program, $code = 'default'){
        return $this->getRepository()->findOneBy(array('program' => $program, 'codeMail' => $code));
    }
    public function getRepository()
    {
        return $this->em->getRepository('AprProgramBundle:MailInvitation');
    }
    
    public function saveMail($program, $params){
        $mail = null;
        if(isset($params['code'])) {
            $mail = $this->loadByCode($program, $params['code']);
        }

        if(!$mail) $mail = new MailInvitation($program);

        if(isset($params['code'])) $mail->setCodeMail($params['code']);
        if(isset($params['subject'])) $mail->setSubject($params['subject']);
        if(isset($params['content'])) $mail->setContent($params['content']);
        if(isset($params['header'])) $mail->setHeader($params['header']);
        if(isset($params['footer'])) $mail->setFooter($params['footer']);
        if(isset($params['signature'])) $mail->setSignature($params['signature']);

        $this->persistAndFlush($mail);
    }

    public function createDefaultMail($program){
        $subject = "Invitation ".$program->getLabel();
        $header = "Bonjour %FIRSTNAME% %LASTNAME%,
                    <br /><br />
                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.
                    <br /><br /> ";
        $footer = "Vous pouvez en savoir plus sur notre offre:
                    <br /><br />
                    %LANDING_URL%
                    <br /><br />
                    A bientôt,
                    <br /><br />";

        $signature = $program->getCollaborator()->getMember()->getFirstName()." ".$program->getCollaborator()->getMember()->getLastName().
                    "<br />".$program->getCollaborator()->getCorporate()->getRaisonSocial();

        if($program->isEasy()) {
            $params = array('subject' => $subject, 'header' => $header, 'content' => '',  'footer' => $footer, 'signature' => $signature);
        } else {
            $params = array('subject' => $subject, 'content' => $header . $footer . $signature);
        }

        $this->saveMail($program, $params);
    }

    public function deleteMail($mail){
        $mailDefault = $this->loadByCode($mail->getProgram(), 'default');

        if(count($mail->getAllParticipatesTo())) {
            foreach ($mail->getAllParticipatesTo() as $participatesTo) {
                $participatesTo->setMailInvitation($mailDefault);
                $this->persist($participatesTo);
            }
            $this->flush();
        }
        $this->removeAndFlush($mail);
    }
}
?>
