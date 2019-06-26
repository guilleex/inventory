<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IncomeRepository")
 * @ORM\Table(name="income")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Income table for this month already exist",
 *     errorPath="home"
 * )
 */
class Income
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     */
    private $slug;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Worker",
     *     mappedBy="income",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     * )
     * @Assert\Valid()
     */
    private $workers;

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
        $this->workers = new ArrayCollection();
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
     * @return Income
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get value
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     * @param integer $value
     * @return Income
     */
    public function setValue($value)
    {
        $this->value = $value;
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
//        $this->createdAt = $createdAt;
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
//        $this->updatedAt = $updatedAt;
//    }

    public function addWorker(Worker $worker)
    {
      if ($this->workers->contains($worker)) {
        return;
      }

      $this->workers[] = $worker;
      // needed to update the owning side of the relationship!
      $worker->setIncome($this);
    }

    public function removeWorker(Worker $worker)
    {
      if (!$this->workers->contains($worker)) {
        return;
      }

      $this->workers->removeElement($worker);
      // needed to update the owning side of the relationship!
      $worker->setIncome(null);
    }

    /**
     * @return ArrayCollection|Worker[]
     */
    public function getWorkers()
    {
        return $this->workers;
    }

}