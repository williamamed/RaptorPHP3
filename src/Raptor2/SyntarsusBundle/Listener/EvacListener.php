<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Raptor2\SyntarsusBundle\Listener;
/**
 * Description of EaclListener
 *
 * @author Amed
 */
class EvacListener extends \Raptor\Listeners\EventListener{
    
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $e) {
    
    }

    public function onClear(\Doctrine\ORM\Event\OnClearEventArgs $e) {
        
    }

    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $e) {
        
    }

    public function postFlush(\Doctrine\ORM\Event\PostFlushEventArgs $e) {
        
    }

    public function postLoad(\Doctrine\Common\EventArgs $e) {
        $this->EVAC($e);
    }

    public function postPersist(\Doctrine\Common\EventArgs $e) {
        
    }

    public function postRemove(\Doctrine\Common\EventArgs $e) {
        
    }

    public function postUpdate(\Doctrine\Common\EventArgs $e) {
        
    }

    public function preFlush(\Doctrine\ORM\Event\PreFlushEventArgs $e) {
        
    }

    public function prePersist(\Doctrine\Common\EventArgs $e) {
        $this->EVAC($e);
    }

    public function preRemove(\Doctrine\Common\EventArgs $e) {
        $this->EVAC($e);
    }

    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $e) {
        $this->EVAC($e);
    }
    
    private function EVAC(\Doctrine\Common\EventArgs $e) {
        $app=  \Raptor\Raptor::getInstance();
        if(!$app->getSecurity()->isAuthenticated())
            return;
        $entity = $e->getEntity();
        $em = $e->getEntityManager();
        
        $refl=new \Wingu\OctopusCore\Reflection\ReflectionClass($entity);
        
        $meta=$em->getClassMetadata($refl->getName());
        $assoc=$meta->getAssociationMappings();
        foreach ($assoc as $key => $value) {
            if($value['targetEntity']=='Raptor2\SyntarsusBundle\Model\Entity\SecurityEstructure'){
//                $prop=$refl->getProperty($key);
//                $prop->setAccessible(true);
//                \Raptor2\SyntarsusBundle\SyntarsusBundle::EvacFlow($prop->getValue($entity)->getId());
                $method="get".ucfirst($key);
                \Raptor2\SyntarsusBundle\SyntarsusBundle::EvacFlow($entity->$method()->getId());
            }
        }
    }
}

?>
