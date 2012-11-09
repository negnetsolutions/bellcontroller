<?php

namespace Negnet\Bell\ControllerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Negnet\Bell\ControllerBundle\Entity\Alert
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Alert
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
     * @var string $beepcode
     *
     * @ORM\Column(name="beepcode", type="string", length=255)
     */
    private $beepcode;


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
     * @return Alert
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
     * Set beepcode
     *
     * @param string $beepcode
     * @return Alert
     */
    public function setBeepcode($beepcode)
    {
        $this->beepcode = $beepcode;
    
        return $this;
    }

    /**
     * Get beepcode
     *
     * @return string 
     */
    public function getBeepcode()
    {
        return $this->beepcode;
    }
}
