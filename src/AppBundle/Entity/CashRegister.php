<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="cash_register")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CashRegisterRepository")
 * )
 */
class CashRegister {

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
     * @Assert\NotBlank(message="Name should not be blank.")
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date")
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
     * @return InValue
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

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}