<?php

namespace examples\exampleBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * examples\exampleBundle\Model\Entity\SecurityRol
 *
 * @Table(name="public.security_rol")
 * @Entity(repositoryClass="examples\exampleBundle\Model\Repository\SecurityRolRepository")
 */
class SecurityRol
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="SEQUENCE")
     * @SequenceGenerator(sequenceName="public.security_rol_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer $belongs
     *
     * @Column(name="belongs", type="integer", nullable=false)
     */
    private $belongs;


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
     * @return SecurityRol
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
     * Set belongs
     *
     * @param integer $belongs
     * @return SecurityRol
     */
    public function setBelongs($belongs)
    {
        $this->belongs = $belongs;
        return $this;
    }

    /**
     * Get belongs
     *
     * @return integer 
     */
    public function getBelongs()
    {
        return $this->belongs;
    }
}