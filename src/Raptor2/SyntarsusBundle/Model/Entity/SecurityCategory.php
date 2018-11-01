<?php

namespace Raptor2\SyntarsusBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Raptor2\SyntarsusBundle\Model\Entity\SecurityCategory
 *
 * @Table(name="security_category")
 * @Entity(repositoryClass="Raptor2\SyntarsusBundle\Model\Repository\SecurityCategoryRepository")
 */
class SecurityCategory
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
     * @return SecurityCategory
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
}