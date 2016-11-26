<?php

namespace Apr\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Apr\UserBundle\Entity\User;
use Apr\UserBundle\Entity\Contact;

class Registration
{

    /**
     * @Assert\Type(type="Apr\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @Assert\Type(type="Apr\UserBundle\Entity\Contact")
     */
    protected $contact;


    public function __construct()
    {

    }

    public function setRegistration($user, $contact)
    {
        $this->user = $user;
        $this->contact = $contact;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function getContact()
    {
        return $this->contact;
    }

}

?>
