<?php

namespace AppBundle\Entity;

use AppBundle\Repository\InValueRepository;
use AppBundle\Repository\OutValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MachineRepository")
 * @ORM\Table(name="machines")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="This name is already in use",
 *     errorPath="name"
 * )
 */
class Machine
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
     * @Assert\NotBlank(message="Machine name should not be blank.")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Put in a machine ratio")
     */
    private $ratio;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Put in a machine position")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="MachineType", inversedBy="machines", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Machine type should not be blank.")
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $visible = true;

    /**
     * @ORM\OneToMany(
     *     targetEntity="InValue",
     *     mappedBy="machine",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @Assert\Valid()
     */
    private $inValues;

    /**
     * @ORM\OneToMany(
     *     targetEntity="OutValue",
     *     mappedBy="machine",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     * )
     */
    private $outValues;

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

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     */
    private $slug;

    private $date;

    private $date_old;

    /**
     * @var integer
     *
     */
    private $inFindValue;

    /**
     * @var integer
     *
     */
    private $inFindValueOld;

    /**
     * @var integer
     *
     */
    private $outFindValue;

    /**
     * @var integer
     *
     */
    private $outFindValueOld;


    public function __construct()
    {
        $this->inValues = new ArrayCollection();
        $this->outValues = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    /**
     * Get ratio
     * @return integer
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * Set ratio
     * @param integer $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * Get position
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set Visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function addInValue(InValue $inValue)
    {
        if ($this->inValues->contains($inValue)) {
            return;
        }

        $this->inValues[] = $inValue;
        // needed to update the owning side of the relationship!
        $inValue->setMachine($this);
    }

    public function removeInValue(InValue $inValue)
    {
        if (!$this->inValues->contains($inValue)) {
            return;
        }

        $this->inValues->removeElement($inValue);
        // needed to update the owning side of the relationship!
        $inValue->setMachine(null);
    }

    /**
     * @return ArrayCollection|InValue[]
     */
    public function getInValues()
    {
        return $this->inValues;
    }

    public function addOutValue(OutValue $outValue)
    {
        if ($this->outValues->contains($outValue)) {
            return;
        }

        $this->outValues[] = $outValue;
        // needed to update the owning side of the relationship!
        $outValue->setMachine($this);
    }

    public function removeOutValue(OutValue $outValue)
    {
        if (!$this->outValues->contains($outValue)) {
            return;
        }

        $this->outValues->removeElement($outValue);
        // needed to update the owning side of the relationship!
        $outValue->setMachine(null);
    }

    /**
     * @return ArrayCollection|OutValue[]
     */
    public function getOutValues()
    {
        return $this->outValues;
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getInFindValue()
    {
        return $this->inFindValue;
    }

    /**
     * @param int $inFindValue
     */
    public function setInFindValue($inFindValue)
    {
        $this->inFindValue = $inFindValue;
    }

    /**
     * @return int
     */
    public function getInFindValueOld()
    {
        return $this->inFindValueOld;
    }

    /**
     * @param int $inFindValueOld
     */
    public function setInFindValueOld($inFindValueOld)
    {
        $this->inFindValueOld = $inFindValueOld;
    }

    /**
     * @return int
     */
    public function getOutFindValue()
    {
        return $this->outFindValue;
    }

    /**
     * @param int $outFindValue
     */
    public function setOutFindValue($outFindValue)
    {
        $this->outFindValue = $outFindValue;
    }

    /**
     * @return int
     */
    public function getOutFindValueOld()
    {
        return $this->outFindValueOld;
    }

    /**
     * @param int $outFindValueOld
     */
    public function setOutFindValueOld($outFindValueOld)
    {
        $this->outFindValueOld = $outFindValueOld;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }


    /**
     * @return mixed
     */
    public function getDateOld()
    {
        return $this->date_old;
    }

    /**
     * @param mixed $date_old
     */
    public function setDateOld($date_old)
    {
        $this->date_old = $date_old;
    }

    public function getInValue()
    {
        $in_values = $this->getInValues()->matching(InValueRepository::createDateCriteria($this->date));

        return $in_values->first();
    }

    public function getOutValue()
    {
        $out_values = $this->getOutValues()->matching(OutValueRepository::createDateCriteria($this->date));

        return $out_values->first();
    }

    public function getInValueOld()
    {
        $in_values_old = $this->getInValues()->matching(InValueRepository::createDateCriteria($this->date_old));

        return $in_values_old->first();
    }

    public function getOutValueOld()
    {
        $out_values_old = $this->getOutValues()->matching(OutValueRepository::createDateCriteria($this->date_old));

        return $out_values_old->first();
    }
}