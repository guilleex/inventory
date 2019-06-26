<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkerRepository")
 * @ORM\Table(name="worker")
 */
class Worker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Put in a worker name")
     */
    private $name;

     /**
     * @ORM\ManyToOne(targetEntity="Income", inversedBy="workers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $income;

    /**
     * @ORM\OneToMany(
     *     targetEntity="IncomeInput",
     *     mappedBy="worker",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @Assert\Valid()
     */
    protected $incomeInputs;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->incomeInputs = new ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getIncome()
    {
        return $this->income;
    }

    public function setIncome($income)
    {
        $this->income = $income;
    }

    public function addIncomeInput(IncomeInput $incomeInput)
    {
        if ($this->incomeInputs->contains($incomeInput)) {
            return;
        }

        $this->incomeInputs[] = $incomeInput;
        // needed to update the owning side of the relationship!
        $incomeInput->setWorker($this);
    }

    public function removeIncomeInput(IncomeInput $incomeInput)
    {
        if (!$this->incomeInputs->contains($incomeInput)) {
            return;
        }

        $this->incomeInputs->removeElement($incomeInput);
        // needed to update the owning side of the relationship!
        $incomeInput->setWorker(null);
    }

    /**
     * @return ArrayCollection|IncomeInput[]
     */
    public function getIncomeInputs()
    {
        return $this->incomeInputs;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
      return $this->createdAt;
    }

//    /**
//     * @param mixed $createdAt
//     */
//    public function setCreatedAt($createdAt)
//    {
//      $this->createdAt = $createdAt;
//    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
      return $this->updatedAt;
    }

//    /**
//     * @param mixed $updatedAt
//     */
//    public function setUpdatedAt($updatedAt)
//    {
//      $this->updatedAt = $updatedAt;
//    }

    public function __toString()
    {
        return (string) $this->getId();
    }

}