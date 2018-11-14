<?php

/**
 * Description of Collector
 * All Right Reserved
 * @author DinoByte
 */

namespace Raptor\Bundle;

use \ReflectionClass;
use \ReflectionProperty;

/**
 * Pobla los atributos de la clase o objeto especificado con los parametros provenientes en el request actual
 */
class Collector {

    /**
     *  Pobla los atributos de la clase o objeto especificado con los parametros provenientes en el request actual
     * @param string|Object $name
     * @param \Raptor\Util\ItemList $request
     * @param array $matcher
     * @return mixed
     * @throws \Exception
     */
    static public function run($name, \Raptor\Util\ItemList $request, $matcher = array()) {

        $class = $name;
        $reflectionClass = new ReflectionClass($class);
        $props = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $app = \Raptor\Raptor::getInstance();
        $metadata = NULL;
        try {

            $metadata = $app->getStore()->getManager()->getMetadataFactory()->getMetadataFor($reflectionClass->getName());
        } catch (\Exception $exc) {
            
        }

        foreach ($props as $prop) {

            if ($request->keyExist($prop->getName())) {


                if ($metadata && $metadata->associationMappings && isset($metadata->associationMappings[$prop->getName()])) {
                    if (isset($metadata->associationMappings[$prop->getName()]['joinColumns'])) {
                        $prop->setAccessible(true);
                        $prop->setValue($class, $app->getStore()->getManager()->getRepository($metadata->associationMappings[$prop->getName()]['targetEntity'])->find($request->get($prop->getName())));
                    }
                } else {
                    $prop->setAccessible(true);
                    $prop->setValue($class, Collector::validator($request->get($prop->getName())));
                }
            }
        }

        foreach ($matcher as $key => $match) {
            $comodin = explode(':', $match);
            if (count($comodin) > 1)
                $match = $comodin[1];

            if ($reflectionClass->hasProperty($key) and $request->keyExist($match)) {


                if ($metadata && $metadata->associationMappings && isset($metadata->associationMappings[$key])) {
                    if (isset($metadata->associationMappings[$key]['joinColumns'])) {

                        if (count($comodin) > 1) {
                            $look = array();
                            $look[$comodin[0]] = $request->get($comodin[1]);
                            $prop=$reflectionClass->getProperty($key);
                            $prop->setAccessible(true);
                            $prop->setValue($class, $app->getStore()
                                            ->getManager()
                                            ->getRepository($metadata->associationMappings[$key]['targetEntity'])
                                            ->findOneBy($look));
                        } else
                            $reflectionClass->getProperty($key)->setValue($class, $app->getStore()
                                            ->getManager()
                                            ->getRepository($metadata->associationMappings[$key]['targetEntity'])
                                            ->find($request->get($match)));
                    }
                }else {
                    $prop=$reflectionClass->getProperty($key);
                    $prop->setAccessible(true);
                    $prop->setValue($class, $request->get($match));
                }
            } else {
                throw new \Exception("Error en el colector de datos: Atributo $key no existe en la clase o el match $match especificado no esta presente en el request actual");
            }
        }



        return $class;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @param type $value
     * @return boolean
     */
    static public function validator($value) {
        if ($value === 'true')
            return true;
        if ($value === 'false')
            return false;
        return $value;
    }

    static private function fillAssociations($class, &$reflex, $request) {
        try {
            $app = \Raptor\Raptor::getInstance();
            $metadata = $app->getStore()->getManager()->getMetadataFactory()->getMetadataFor($class);
            foreach ($metadata->associationMappings as $key => $association) {
                if (isset($association['joinTable'])) {
                    
                }
                if (isset($association['joinColumns']) && $request->keyExist($key)) {
                    $reflex->getProperty($key)->setAccessible(true);
                    $reflex->getProperty($key)->setValue($class, $request->get($key));
                }
            }
        } catch (\Exception $exc) {
            
        }
    }

}

?>
