<?php

namespace AppBundle\Entity;

use AppBundle\Repository\JackpotRepository;
use AppBundle\Repository\MachineTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MachineTypeRepository")
 * @ORM\Table(name="machine_types")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="This type of machine already exist",
 *     errorPath="home"
 * )
 */
class MachineType
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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $haveJackpot;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Jackpot",
     *     mappedBy="machineType",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     * )
     * @Assert\Valid()
     */
    private $jackpots;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Machine",
     *     mappedBy="type",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     * )
     * @Assert\Valid()
     */
    private $machines;

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
     * @var
     */
    private $dateJP;

    /**
     * @var
     */
    private $periodJackpot;

    public function __construct()
    {
        $this->machines = new ArrayCollection();
        $this->jackpots = new ArrayCollection();
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
     * @return MachineType
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get haveJackpot
     *
     * @return boolean
     */
    public function getHaveJackpot()
    {
        return $this->haveJackpot;
    }

    /**
     * Set haveJackpot
     */
    public function setHaveJackpot($haveJackpot)
    {
        $this->haveJackpot = $haveJackpot;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function addMachine(Machine $machine)
    {
        if ($this->machines->contains($machine)) {
            return;
        }

        $this->machines[] = $machine;
        // needed to update the owning side of the relationship!
        $machine->setType($this);
    }

    public function removeMachine(Machine $machine)
    {
        if (!$this->machines->contains($machine)) {
            return;
        }

        $this->machines->removeElement($machine);
        // needed to update the owning side of the relationship!
        $machine->setType(null);
    }

    /**
     * @return ArrayCollection|Machine[]
     */
    public function getMachines()
    {
        return $this->machines;
    }

    public function addJackpot(Jackpot $jackpot)
    {
        if ($this->jackpots->contains($jackpot)) {
            return;
        }

        $this->jackpots[] = $jackpot;
        // needed to update the owning side of the relationship!
        $jackpot->setMachineType($this);
    }

    public function removeJackpot(Jackpot $jackpot)
    {
        if (!$this->jackpots->contains($jackpot)) {
            return;
        }

        $this->jackpots->removeElement($jackpot);
        // needed to update the owning side of the relationship!
        $jackpot->setMachineType(null);
    }

    /**
     * @return ArrayCollection|Jackpot[]
     */
    public function getJackpots()
    {
        return $this->jackpots;
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

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getDateJP()
    {
        return $this->dateJP;
    }

    /**
     * @param  $dateJP
     */
    public function setDateJP($dateJP)
    {
        $this->dateJP = $dateJP;
    }

    public function getJackpot()
    {
        $jp = $this->getJackpots()->matching(JackpotRepository::createDateCriteria($this->dateJP));

        return $jp->first();
    }

    /**
     * @return mixed
     */
    public function getPeriodJackpot()
    {
        return $this->periodJackpot;
    }

    /**
     * @param mixed $periodJackpot
     */
    public function setPeriodJackpot($periodJackpot)
    {
        $this->periodJackpot = $periodJackpot;
    }


}