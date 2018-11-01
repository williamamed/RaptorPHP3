<?php

namespace Raptor2\SyntarsusBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Raptor2\SyntarsusBundle\Model\Entity\SecurityPrivilege
 *
 * @Table(name="security_privilege")
 * @Entity(repositoryClass="Raptor2\SyntarsusBundle\Model\Repository\SecurityPrivilegeRepository")
 */
class SecurityPrivilege
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $route
     *
     * @Column(name="route", type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string $className
     *
     * @Column(name="class_name", type="string", length=255, nullable=true)
     */
    private $className;

    /**
     * @var integer $type
     *
     * @Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var integer $belongs
     *
     * @Column(name="belongs", type="integer", nullable=false)
     */
    private $belongs;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany(targetEntity="Raptor2\SyntarsusBundle\Model\Entity\SecurityRol", mappedBy="idPrivilege")
     */
    private $idRol;

    public function __construct()
    {
        $this->idRol = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set route
     *
     * @param string $route
     * @return SecurityPrivilege
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SecurityPrivilege
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
     * Set className
     *
     * @param string $className
     * @return SecurityPrivilege
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Get className
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return SecurityPrivilege
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set belongs
     *
     * @param integer $belongs
     * @return SecurityPrivilege
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

    /**
     * Add idRol
     *
     * @param Raptor2\SyntarsusBundle\Model\Entity\SecurityRol $idRol
     */
    public function addSecurityRol(\Raptor2\SyntarsusBundle\Model\Entity\SecurityRol $idRol)
    {
        $this->idRol[] = $idRol;
    }

    /**
     * Get idRol
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIdRol()
    {
        return $this->idRol;
    }
}