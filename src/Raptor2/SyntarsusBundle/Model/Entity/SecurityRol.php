<?php

namespace Raptor2\SyntarsusBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Raptor2\SyntarsusBundle\Model\Entity\SecurityRol
 *
 * @Table(name="security_rol")
 * @Entity(repositoryClass="Raptor2\SyntarsusBundle\Model\Repository\SecurityRolRepository")
 */
class SecurityRol
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany(targetEntity="Raptor2\SyntarsusBundle\Model\Entity\SecurityPrivilege", inversedBy="idRol")
     * @JoinTable(name="security_rol_security_privilege",
     *   joinColumns={
     *     @JoinColumn(name="id_rol", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @JoinColumn(name="id_privilege", referencedColumnName="id")
     *   }
     * )
     */
    private $idPrivilege;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany(targetEntity="Raptor2\SyntarsusBundle\Model\Entity\SecurityUser", mappedBy="idRol")
     */
    private $idUser;

    public function __construct()
    {
        $this->idPrivilege = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idUser = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add idPrivilege
     *
     * @param Raptor2\SyntarsusBundle\Model\Entity\SecurityPrivilege $idPrivilege
     */
    public function addSecurityPrivilege(\Raptor2\SyntarsusBundle\Model\Entity\SecurityPrivilege $idPrivilege)
    {
        $this->idPrivilege[] = $idPrivilege;
    }

    /**
     * Get idPrivilege
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIdPrivilege()
    {
        return $this->idPrivilege;
    }

    /**
     * Add idUser
     *
     * @param Raptor2\SyntarsusBundle\Model\Entity\SecurityUser $idUser
     */
    public function addSecurityUser(\Raptor2\SyntarsusBundle\Model\Entity\SecurityUser $idUser)
    {
        $this->idUser[] = $idUser;
    }

    /**
     * Get idUser
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }
}