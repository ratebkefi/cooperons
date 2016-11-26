<?php

namespace Apr\AffairBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\PreSerialize;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="recruitment_settings")
 */

class RecruitmentSettings
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Premier seuil requis")
     * @Assert\Range(min = 1, minMessage = "Premier seuil invalide")
     * @ORM\Column(name="range_1", type="integer", nullable=true)
     */
    private $range1 = 1000;
    
    /**
     * @Assert\NotBlank(message = "Deuxième seuil requis")
     * @Assert\Range(min = 1, minMessage = "Deuxième seuil invalide")
     * @ORM\Column(name="range_2", type="integer", nullable=true)
     */
    private $range2 = 2000;

    /**
     * @Assert\NotBlank(message = "Premier taux requis")
     * @Assert\Range(min = 1, minMessage = "Premier taux  invalide")
     * @ORM\Column(name="rate_1", type="integer", nullable=true)
     */
    private $rate1 = 15;

    /**
     * @Assert\NotBlank(message = "Deuxième taux requis")
     * @ORM\Column(name="rate_2", type="integer", nullable=true)
     */
    private $rate2 = 10;

    /**
     * @Assert\NotBlank(message = "Dernier taux requis")
     * @ORM\Column(name="rate_beyond", type="integer", nullable=true)
     */
    private $rateBeyond = 5;

    /**
     * @Assert\NotBlank(message = "Durée requise")
     * @Assert\Range(min = 1, minMessage = "Durée  invalide")
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration = 12;


    /**
     * @var \Apr\ContractBundle\Entity\LegalDocument
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\LegalDocument", inversedBy="recruitmentSettings", cascade = {"persist"})
     * @ORM\JoinColumn(name="legal_document_id", referencedColumnName="id", nullable=true)
     *
     */
    private $legalDocument;

    /**
     * @var RecruitmentSettings
     *
     * @ORM\OneToOne(targetEntity="RecruitmentSettings", inversedBy="oldRecruitmentSettings", cascade = {"persist"})
     * @ORM\JoinColumn(name="new_recruitment_settings_id", referencedColumnName="id", nullable=true)
     */
    private $newRecruitmentSettings;

    /**
     * @var RecruitmentSettings
     *
     * @ORM\OneToOne(targetEntity="RecruitmentSettings", mappedBy="newRecruitmentSettings", cascade = {"persist"})
     */
    private $oldRecruitmentSettings;

    /**
     * @ORM\OneToMany(targetEntity="Recruitment", mappedBy="recruitmentSettings", cascade = {"persist"})
     */
    private $allRecruitments;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var float
     */
    private $offset1;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var float
     */
    private $offset2;

    public function __construct($legalDocument) {
        $this->createdDate = Tools::DateTime('now');
        $this->legalDocument = $legalDocument;
    }

    public function __clone() {
        $this->createdDate = Tools::DateTime('now');
    }

    public function getOldRecruitmentSettings() {
        return $this->oldRecruitmentSettings;
    }

    public function setNewRecruitmentSettings(RecruitmentSettings $recruitmentSettings) {
        $this->newRecruitmentSettings = $recruitmentSettings;
        $this->clearLegalDocument();
    }

    public function getNewRecruitmentSettings() {
        return $this->newRecruitmentSettings;
    }

    public function clearLegalDocument(){
        $this->legalDocument = null;
    }

    public function setLegalDocument($legalDocument){
        $this->legalDocument = $legalDocument;
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getLegalDocument() {
        if(!is_null($this->legalDocument)) {
            return $this->legalDocument;
        } elseif(!is_null($this->newRecruitmentSettings)) {
            return $this->newRecruitmentSettings->getLegalDocument();
        }
    }

    public function isRemovable() {
        return !count($this->getAllActiveRecruitments(true));
    }

    public function getRate1() {
        return $this->rate1;
    }

    public function getRate2() {
        return $this->rate2;
    }

    public function getRateBeyond() {
        return $this->rateBeyond;
    }

    public function setRate1($rate) {
        $this->rate1 = $rate;
    }

    public function setRate2($rate) {
        $this->rate2 = $rate;
    }

    public function setRateBeyond($rate) {
        $this->rateBeyond = $rate;
    }

    public function getRange1() {
        return $this->range1;
    }

    public function getOffset1() {
        return floor($this->range1*($this->rate1-$this->rate2)/100);
    }

    public function getOffset2() {
        return floor($this->range2*($this->rate2-$this->rateBeyond)/100+$this->getOffset1());
    }

    public function getRange2() {
        return $this->range2;
    }

    public function calcCumulatedRebate($cumulatedBillings) {
        if($cumulatedBillings < $this->range1) {
            return floor($cumulatedBillings * $this->rate1 / 100);
        } elseif($cumulatedBillings < $this->range2) {
            return floor((($cumulatedBillings - $this->range1) * $this->rate2 + $this->range1 * $this->rate1)/ 100);
        } else {
            return floor((($cumulatedBillings - $this->range2) * $this->rateBeyond + ($this->range2 - $this->range1) * $this->rate2 + $this->range1 * $this->rate1)/ 100);
        }
    }

    public function setRange1($range) {
        $this->range1 = $range;
    }

    public function setRange2($range) {
        $this->range2 = $range;
    }

    public function setDuration($duration) {
        $this->duration = $duration;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getAllActiveRecruitments($forRemoval = false) {
        $allRecruitments = (array) $this->allRecruitments->toArray();

        // Pour la suppression on regarde tous les recrutements - y compris expirés ... (on veut garder la mémoire)
        if(!$forRemoval) {
            foreach($allRecruitments as $key => $recruitment) {
                if($recruitment->isExpired()) unset($allRecruitments[$key]);
            }
        }

        if($this->getOldRecruitmentSettings()) {
            return new ArrayCollection(array_merge($allRecruitments, (array) $this->getOldRecruitmentSettings()->getAllActiveRecruitments($forRemoval)->toArray()));
        } else {
            return new ArrayCollection($allRecruitments);
        }
    }

    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->offset1 = $this->getOffset1();
        $this->offset2 = $this->getOffset2();
    }
}