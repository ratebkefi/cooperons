<?php

namespace Apr\AffairBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity(repositoryClass="Apr\AffairBundle\Repository\RecruitmentRepository")
 * @ORM\Table(name="recruitments")
 * @UniqueEntity(
 *     fields={"recruiterCorpContract", "recruitmentSettings", "isExpired"}, ignoreNull = false
 * )
 * @UniqueEntity(
 *     fields={"recruiteeCorpContract"}
 * )
 */

class Recruitment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Apr\ContractBundle\Entity\Contract
     *
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\Contract", cascade = {"persist"})
     * @ORM\JoinColumn(name="recruiter_corp_contract_id", referencedColumnName="id")
     *
     */
    private $recruiterCorpContract;

    /**
     * @var RecruitmentSettings
     *
     * @ORM\ManyToOne(targetEntity="RecruitmentSettings", cascade = {"persist"})
     * @ORM\JoinColumn(name="recruitment_settings_id", referencedColumnName="id")
     *
     */
    private $recruitmentSettings;

    /**
     * @var \Apr\ContractBundle\Entity\Contract
     *
     * Chaque contrat Corp ne peut avoir qu'un recrutement associé en tant que recruiteeCorpContract...
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\Contract", inversedBy="", cascade = {"persist"})
     * @ORM\JoinColumn(name="recruitee_corp_contract_id", referencedColumnName="id")
     *
     */
    private $recruiteeCorpContract;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="expiry_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $expiryDate;

    /**
     * @ORM\Column(name="is_expired", type="boolean")
     * @var boolean
     */
    private $isExpired = false;

    /**
     * @ORM\Column(name="cumulated_billings", type="float")
     */
    private $cumulatedBillings = 0;

    /**
     * @ORM\Column(name="cumulated_rebate", type="float")
     */
    private $cumulatedRebate = 0;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var \Apr\ContractBundle\Entity\Contract
     */
    private $recruitmentContract;

    public function __construct($recruiterCorpContract, $recruitmentSettings) {
        $this->createdDate = Tools::DateTime('now');
        $this->recruiterCorpContract = $recruiterCorpContract;
        $this->recruitmentSettings = $recruitmentSettings;
    }
    
    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getRecruiterCorpContract()
    {
        return $this->recruiterCorpContract;
    }

    public function getRecruitmentSettings()
    {
        return $this->recruitmentSettings;
    }

    public function getRecruitmentLegalDocument()
    {
        return $this->recruitmentSettings->getLegalDocument();
    }

    public function getRecruitmentContract()
    {
        return $this->getRecruitmentLegalDocument()->getContract();
    }

    public function setRecruiteeCorpContract($recruiteeCorpContract)
    {
        $this->recruiteeCorpContract = $recruiteeCorpContract;
    }

    public function getRecruiteeCorpContract()
    {
        return $this->recruiteeCorpContract;
    }

    public function setExpiryDate() {
        $nowDate = Tools::DateTime('now');
        $this->expiryDate = new \DateTime(date("Y-m-d", mktime(0, 0, 0,
            (int) $nowDate->format("m") + $this->recruitmentSettings->getDuration(),
            $nowDate->format("d"), $nowDate->format("Y"))));
    }

    public function getExpiryDate() {
        return $this->expiryDate;
    }

    public function expire() {
        $this->isExpired = true;
    }

    public function isExpired() {
        return $this->isExpired;
    }

    public function getCumulatedBillings() {
        return $this->cumulatedBillings;
    }

    public function getCumulatedRebate()
    {
        return $this->cumulatedRebate;
    }

    public function addBillings($amount)
    {
        $this->cumulatedBillings += $amount;
    }

    public function addRebate($amount)
    {
        $this->cumulatedRebate += $amount;
    }

    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->recruitmentContract = $this->getRecruitmentContract();
    }

}