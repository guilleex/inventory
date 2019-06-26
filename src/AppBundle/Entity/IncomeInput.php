<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IncomeInputRepository")
 * @ORM\Table(name="income_input")
 */
class IncomeInput {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Worker", inversedBy="incomeInputs")
     * @ORM\JoinColumn(nullable=TRUE)
     */
    protected $worker;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $value;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * Get value
     * @return number
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     * @param number $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getDate() {
      return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date) {
      $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}