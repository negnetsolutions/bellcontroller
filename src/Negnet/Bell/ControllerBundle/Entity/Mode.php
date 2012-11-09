<?php

namespace Negnet\Bell\ControllerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Negnet\Bell\ControllerBundle\Entity\Mode
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Mode
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer $alert
     *
     * @ORM\Column(name="alert", type="smallint")
     */
    private $alert;

    /**
     * @var integer $cycles
     *
     * @ORM\Column(name="cycles", type="integer")
     */
    private $cycles;

    /**
     * @var float $delay
     *
     * @ORM\Column(name="delay", type="float")
     */
    private $delay;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Mode
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alert
     *
     * @param integer $alert
     * @return Mode
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;
    
        return $this;
    }

    /**
     * Get alert
     *
     * @return integer 
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set cycles
     *
     * @param integer $cycles
     * @return Mode
     */
    public function setCycles($cycles)
    {
        $this->cycles = $cycles;
    
        return $this;
    }

    /**
     * Get cycles
     *
     * @return integer 
     */
    public function getCycles()
    {
        return $this->cycles;
    }

    /**
     * Set delay
     *
     * @param float $delay
     * @return Mode
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    
        return $this;
    }

    /**
     * Get delay
     *
     * @return float 
     */
    public function getDelay()
    {
        return $this->delay;
    }
}
