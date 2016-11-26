<?php

namespace Apr\UserBundle\Security;

use FOS\UserBundle\Security\UserProvider AS FOSUserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\User;

class ApiKeyUserProvider extends FOSUserProvider
{
    /**
     * @var bool Stateless Authentication?
     */
    private $stateless = false;
    /**
     * @var ProgramManager 
     */
    protected $programManager;
    /**
     * @var $container
     */
    protected $container;
    
    public function __construct(\FOS\UserBundle\Model\UserManagerInterface $userManager, \Apr\ProgramBundle\Manager\ProgramManager $programManager, $container) {
        parent::__construct($userManager);
        $this->programManager = $programManager;
        $this->container = $container;
    }
    
    
    public function getUsernameForApiKey($apiKey)
    {
        $this->stateless = true;
        $program = $this->programManager->loadProgramByApiKey($apiKey);
        if(!$program or $program->isEasy()) return array('error' => 4011);
        if($program->isExpired() or !($program->getStatus() == 'preprod' or $program->getStatus() == 'prod')) return array('error' => 4015);

        $member = $program->getCollaborator()->getMember();
        $user = $member->getUser();

        if ($user) {
            $request = $this->container->get('request');
            $uri = $request->getUri();
            $method = $request->getMethod();
            $parameters = '';
            if($method == 'POST' || $method == 'PUT'){
                $parameters = $request->getContent();
            }
            $this->programManager->addActionAPIToJournal($program, $uri, $method, $parameters);
            return array('username' => $user->getUsername(), 'APIKeyProgramId' => $program->getId());
        }

        // Cas impossible ...
        return array('error' => 4011);
    }
    
    public function loadUserByUsername($username)
    {
        return new User(
            $username,
            null,
            array('ROLE_USER')
        );
    }

    public function refreshUser(SecurityUserInterface $user)
    {
        if ($this->stateless) {
            throw new UnsupportedUserException();
        }
        return parent::refreshUser($user);
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}
?>
