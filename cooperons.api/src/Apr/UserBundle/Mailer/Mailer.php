<?php

namespace Apr\UserBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\Mailer as BaseMailer;

class Mailer extends BaseMailer
{
    protected $container;
    
    public function __construct($container, array $parameters)
    {
        parent::__construct($container->get('mailer'), $container->get('router'), $container->get('templating'), $parameters);
        $this->container = $container;
    }

    public function sendResettingEmailMessage(UserInterface $user, $url = null)
    {
        $template = $this->parameters['resetting.template'];
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url
        ));
        $to = $user->getEmail();
        $from = ($this->container->getParameter('senderMail') && $this->container->getParameter('senderName'))?array($this->container->getParameter('senderMail') => $this->container->getParameter('senderName')):$this->parameters['from_email']['resetting'];
        
        $this->sendEmailMessage($rendered, $from, $to);
    }

    /**
     * @param string $params
     */
    public function sendMails($params)
    {
        // Fondative
        if (count($params) === 0) {
            return;
        }
        $env = $this->container->get('kernel')->getEnvironment();
        $testCcMail = $this->container->getParameter('testCcMail');
        $fromArr = array($this->container->getParameter('senderMail') => $this->container->getParameter('senderName'));

        foreach($params as $param){
            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($param['subject'])
                ->setTo($param['to']);
            if (isset($param['cc'])) {
                $message->setCc($param['cc']);
            }
            if($env != 'dev') {
                $message->setBcc($testCcMail);
            }
            if(isset($param['body']) && !is_array($param['body'])){
                $message->setBody($param['body']);
            }elseif(isset($param['body']) && is_array($param['body'])){
                $message->setBody(
                    $this->templating->render(
                            $param['body']['template'], $param['body']['parameter']
                    ));
            }

            if(isset($param['from'])) {
                $message->setFrom($param['from']);
            } else {
                $message->setFrom($fromArr);
            }

            if(isset($param['replyTo'])) {
                $message->setFrom($param['replyTo']);
            }

            if (isset($param['attach'])) {
                $message->attach(\Swift_Attachment::fromPath($param['attach']));
            }
            $this->mailer->send($message);
        }
        
    }
}
