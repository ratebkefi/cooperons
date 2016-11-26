<?php

namespace Apr\CorporateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\CorporateBundle\Repository\CollegeRepository")
 * @ORM\Table(name="colleges")
 */

class College
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Apr\ProgramBundle\Entity\Member $member
     * @ORM\ManyToOne(targetEntity="\Apr\ProgramBundle\Entity\Member", cascade = {"persist"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable = true)
     */
    private $member = null;

    /**
     * @var Corporate $corporate
     * @ORM\ManyToOne(targetEntity="Corporate", cascade = {"persist"})
     * @ORM\JoinColumn(name="corporate_id", referencedColumnName="id", nullable = true)
     */
    private $corporate = null ;

    /**
     * @var CollegeInvitation $invitation
     * @ORM\OneToOne(targetEntity="CollegeInvitation", mappedBy="college", cascade = {"all"})
     */
    private $invitation = null;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="last_confirm_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $lastConfirmDate;

    /**
     * @ORM\Column(name="leave_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $leaveDate;

    /**
     * @var string
     */
    private $status;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var boolean
     */
    private $isDelegate;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var integer
     */
    private $monthConfirm;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var string
     */
    private $labelMonthConfirm;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var boolean
     */
    private $isUnlocked;

    public function __construct($corporate = null, $member = null, $invitation = null) {
        $this->createdDate = Tools::DateTime('now');
        $this->member = $member;
        $this->corporate = $corporate;
        $this->invitation = $invitation;
        $member->SetCollege($this);
    }

    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getCorporate()
    {
        return $this->corporate;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function setInvitation($invitation) {
        // Mise à jour des 2 cotés de la relation nécessaire pour Doctrine ...
        if(!is_null($invitation)) {
            $invitation->setCollege($this);
        } elseif($this->invitation) {
            $this->invitation->setCollege(null);
        }
        $this->invitation = $invitation;
    }

    public function clearInvitation() {
        if($this->invitation) {
            // Mise à jour des 2 cotés de la relation nécessaire pour Doctrine ...
            $this->invitation->setCollege(null);
            $this->invitation = null;
        }
    }

    public function confirm() {
        $welcome = false;
        if($this->corporate && $this->corporate->isAccordSigned()) {
            if($this->getStatus() == 'wait_for_delegate') {
                $welcome = true;
                if(is_null($this->corporate->getDelegate())) {
                    $this->corporate->setDelegate($this);
                }
                $this->getCorporate()->addCountColleges(1);
            }
            $this->lastConfirmDate = Tools::DateTime('now');
        }
        return $welcome;
    }

    public function getLastConfirmDate()
    {
        return $this->lastConfirmDate;
    }

    public function getMonthConfirm($forGift = false)
    {
        if(is_null($this->lastConfirmDate)) {
            // C'est à dire en attente de confirmation par Delegate ... Cas ne devrait pas se produire ... mais par sécurité ...
            return null;
        } else {
            $now = Tools::DateTime('now');
            $yearNow = $now->format('Y');
            $monthNow = $now->format('m');
            $monthConfirm = ceil($monthNow/3)*3;

            if($forGift) {
                // On se positionne à la dernière échéance trimestrielle lors de la distribution de chèques cadeau ...
                $yearNow += ($monthConfirm == 3)?-1:0;
                $monthConfirm += ($monthConfirm == 3)?9:-3;
                $monthNow = $monthConfirm;
            }

            $yearLastConfirm = $this->lastConfirmDate->format('Y');
            $monthLastConfirm = $this->lastConfirmDate->format('m');

            if($monthNow % 3 == 0) {
                // Durant le mois de l'échéance trimestrielle (Mars / Juin / Sept / Déc) ...
                if(($monthNow == $monthLastConfirm) && ($yearNow == $yearLastConfirm)) {
                    // Si le membre a confirmé son appartenance ce mois-ci: < 0
                    return -1;
                } else {
                    // Sinon: 0
                    return 0;
                }
            } else {
                return $monthConfirm;
            }
        }
    }

    public function getLabelMonthConfirm($forGift = false)
    {
        return Tools::displayMonth($this->getMonthConfirm($forGift));
    }

    public function leave($newDelegate = null) {
        if(!in_array($this->getStatus(), array('wait_for_corporate', 'wait_for_delegate'))) $this->getCorporate()->addCountColleges(-1);

        $this->clearInvitation();

        $this->leaveDate = Tools::DateTime('now');
        $this->getMember()->setCollege(null);

        if($this->corporate && $this->corporate->getDelegate() == $this) {
            $this->corporate->setDelegate($newDelegate);
        }
    }

    public function getLeaveDate()
    {
        return $this->leaveDate;
    }

    public function getStatus($forGift = false){
        if(!is_null($this->leaveDate)) {
            return 'cancel';
        }
        elseif(!$this->corporate) {
            return 'wait_for_corporate';
        }
        elseif(is_null($this->lastConfirmDate)) {
            return 'wait_for_delegate';
        }
        else {
            $monthConfirm = $this->getMonthConfirm($forGift);
            if($monthConfirm >= 0) {
                return 'wait_for_reconfirm';
            } else {
                return 'confirmed';
            }
        }
    }

    public function isUnlocked($forGift = false){
        $status = $this->getStatus($forGift);

        $isUnlocked = ($status == 'confirmed');

        // Quand ce n'est pas pour le calcul des chèques cadeaux à distribuer effectivement ce mois-ci par l'admin,
        // on étend aussi aux membres devant confirmer plus tard (pour connaître le montant théorique maximum ...)
        if(!$forGift) $isUnlocked = ($isUnlocked or ($status == 'wait_for_reconfirm'));

        return $isUnlocked;
    }

    public function isDelegate() {
        if($this->corporate && $this->corporate->getDelegate()) {
            return $this == $this->corporate->getDelegate();
        } else {
            return false;
        }
    }

    /**
     * @author Fondative <devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->isDelegate = $this->isDelegate();
        $this->monthConfirm = $this->getMonthConfirm();
        $this->labelMonthConfirm = $this->getLabelMonthConfirm();
        $this->isUnlocked = $this->isUnlocked();
        $this->status = $this->getStatus();

    }

}