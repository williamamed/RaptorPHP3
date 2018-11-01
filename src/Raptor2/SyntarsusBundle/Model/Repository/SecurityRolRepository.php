<?php

namespace Raptor2\SyntarsusBundle\Model\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SecurityRolRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SecurityRolRepository extends EntityRepository
{
    public function getAllChilds($id) {
        $entities=$this->findBy(array('belongs'=>$id));
        $result=$entities;
        foreach ($entities as $ent) {
            $result=  array_merge($result,$this->getAllChilds($ent->getId()));
        }
        return $result;
    }
    
}