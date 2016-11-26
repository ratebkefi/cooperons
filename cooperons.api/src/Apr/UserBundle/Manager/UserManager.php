<?php

namespace Apr\UserBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager as BaseManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Apr\CoreBundle\Tools\Tools;

class UserManager extends BaseManager
{
    protected $container;

    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, EntityManager $om, $class, $container)
    {
        $this->container = $container;
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class);
    }

    public function addUser($user, $contact)
    {
        $departementsManager = $this->container->get('apr_user.departements_manager');

        $userDb = $this->findUserByEmail($user->getEmail());
        if ($userDb) throw new ApiException(400201);
        
        $tokenGenerator = new TokenGenerator();
        $tokenUser = $tokenGenerator->generateToken();
        $user->setConfirmationToken($tokenUser);
        $user->addRole('ROLE_MEMBER');
        $user->setUserName($user->getEmail());
        $dateNow = Tools::DateTime('now');
        $user->setCreatedDate($dateNow);
        $this->updateUser($user);

        $departement = $departementsManager->loadDepartement((int)substr($contact->getPostalCode(), 0, 2));
        $contact->setNumDepartement($departement);
        $contact->setUser($user);
        $user->setContact($contact);

        $this->persistAndFlush($user);

        return $user;
    }

    /**
     * Find users for grid
     *
     * @param string $params search parameters for grid
     * @param string $type user type  C
     * @param string $status status   waitingCustomer
     *
     * @return array Returns users list
     */
    public function search($search = NULL, $type = NULL, $status = NULL)
    {

        $usersEntities = $this->getRepository()->search($search);
        return $this->getDataGrid($usersEntities, NULL, $type, $status);
    }


    /**
     * Find users for API service
     * @author Fondative <devteam@fondative.com>
     *
     * @param string $params search parameters for grid
     * @param string $type user type  C
     * @param string $status status   waitingCustomer
     *
     * @return array Returns users list
     */
    public function searchAPI($search = NULL)
    {

        $allUsers = $this->getRepository()->search($search);
        $users = array();

        foreach ($allUsers as $user) {
            if (!$user->hasRole('ROLE_ADMIN') && !$user->hasRole('ROLE_SUPER_ADMIN')) {
                $users[] = $user;
            }
        }

        return $users;

    }


    /**
     * Format data for grid
     *
     * @param Array $usersEntities
     * @param int $idUser
     * @param string $type user type  C|AE
     * @param string $status status   waitingCustomer
     * @return array formatted data
     */
    public function getDataGrid($usersEntities, $idUser = NULL, $type = NULL, $status = NULL)
    {

        $users = array();

        foreach ($usersEntities as $userEntity) {
            if ($userEntity->hasRole('ROLE_ADMIN') || $userEntity->hasRole('ROLE_SUPER_ADMIN')) continue;
            //Init return array params 
            $user = array();
            $user["id"] = $userEntity->getId();
            $user["firstName"] = $userEntity->getFirstName();
            $user["lastName"] = $userEntity->getLastName();
            $user["email"] = $userEntity->getUsername();
            $user["status"] = $userEntity->isEnabled();
            $user["phone"] = $userEntity->getContact()->getPhone();
            $user["status"] = $userEntity->getStatus();
            $user["emailStatus"] = $userEntity->getMailStatus();
            $user["type"] = 'Member';
            $user["nbrMembre"] = 0;
            $users[] = $user;
        }

        return $users;
    }

    public function loadUserByEmail($email)
    {
        return $this->getRepository()->findOneBy(array('email' => $email));
    }

    public function loadUserById($id)
    {
        return $this->getRepository()->findOneBy(array('id' => $id));
    }

    public function isActiveUser($user)
    {
        return ($user->getEnabled()) ? true : false;
    }

    public function getRepository()
    {
        return $this->objectManager->getRepository('AprUserBundle:User');
    }

    /***/
    public function persistAndFlush($entity)
    {
        $this->persist($entity);
        $this->flush();
    }

    public function removeAndFlush($entity)
    {
        $this->objectManager->remove($entity);
        $this->objectManager->flush();
    }

    public function persist($entity)
    {
        $this->objectManager->persist($entity);
    }

    public function flush()
    {
        $this->objectManager->flush();
    }

    public function updateUserContact($userDb, $user, $contact)
    {
        $departementsManager = $this->container->get('apr_user.departements_manager');

        //User
        if ($user->getPlainPassword()) {
            $userDb->setPlainPassword($user->getPlainPassword());
        }
        if (!is_null($user->getEmail()) && $userDb->getEmail() != $user->getEmail()) {
            $userDb->setMailStatus('standby');
            $userDb->setEmail($user->getEmail());
            $userDb->setUsername($user->getEmail());
        }
        if (!is_null($user->getFirstName())) {
            $userDb->setFirstname($user->getFirstName());
        }
        if (!is_null($user->getLastName())) {
            $userDb->setLastname($user->getLastName());
        }
        //Contact
        $contactDb = $userDb->getContact();

        if (!is_null($contact->getPhone())) {
            $contactDb->setPhone($contact->getPhone());
        }
        if (!is_null($contact->getAddress())) {
            $contactDb->setAddress($contact->getAddress());
        }

        /**
         * @author Fondative <devteam@fondative.com>
         *
         * @ Updated
         */
        if (!is_null($contact->getSecondAddress())) {
            $contactDb->setSecondAddress($contact->getSecondAddress());
        } else {
            $contactDb->setSecondAddress(null);
        }
        if (!is_null($contact->getCity())) {
            $contactDb->setCity($contact->getCity());
        }
        if (!is_null($contact->getPostalCode())) {
            $contactDb->setPostalCode($contact->getPostalCode());
        }
        if (!is_null($contact->getPostalCode())) {
            $departement = $departementsManager->loadDepartement((int)substr($contact->getPostalCode(), 0, 2));
            $contactDb->setNumDepartement($departement);
        }
        $userDb->setcontact($contactDb);

        $this->updateUser($userDb);

        return $userDb;
    }

    public function loadUserByUserNameAndPwd($username, $password)
    {
        return $this->getRepository()->findOneBy(array('username' => $username, 'password' => $password));
    }

    public function validatePassword($password)
    {
        if (strlen($password) < 6) {
            return false;
        }
        return true;

    }
}

?>
