<?php

namespace BenTools\ApiFirstBundle\TestSuite\Model;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class City
 * @package BenTools\ApiFirstBundle\TestSuite\Model
 * @ORM\Entity()
 */
class City implements ResourceInterface {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(min="3")
     */
    private $name;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this - Provides Fluent Interface
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return $this - Provides Fluent Interface
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this - Provides Fluent Interface
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    

}