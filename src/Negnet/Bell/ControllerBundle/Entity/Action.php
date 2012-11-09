<?php

namespace Negnet\Bell\ControllerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Negnet\Bell\ControllerBundle\Entity\Action
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Action
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
     * @var string $time
     *
     * @ORM\Column(name="time", type="string", length=255)
     */
    private $time;

    /**
     * @var integer $alert
     *
     * @ORM\Column(name="alert", type="smallint")
     */
    private $alert;


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
     * Set time
     *
     * @param string $time
     * @return Action
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set alert
     *
     * @param integer $alert
     * @return Action
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
}
