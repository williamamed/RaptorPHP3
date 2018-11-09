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
class CollectorManager {

    private $entity;
    private $app;
    private $class;
    private $matcher;
    private $method;
    private $isExecuted;

    function __construct($class, $method = 'POST') {

        $this->class = $class;
        $this->matcher = array();
        $this->method = $method;
        $this->isExecuted = false;

        $this->app = \Raptor\Raptor::getInstance();
    }

    /**
     * Realiza una operacion de persistencia sobre la entidad generada por el colector
     * $em->persist($entidad), si se especifica el parametro flush en true se ejecuta la transaccion
     * @param boolean $flush
     */
    public function save($flush = false) {
        $this->execute();
        $this->app->getStore()->getManager()->persist($this->entity);
        if ($flush)
            $this->app->getStore()->getManager()->flush();
        return $this;
    }

    /**
     * Retorna la entidad generada
     * @return mixed
     */
    public function getEntity() {
        $this->execute();
        return $this->entity;
    }

    public function match($match) {
        $this->matcher = $match;
        $this->execute();
        return $this;
    }

    private function execute() {
        if ($this->isExecuted)
            return false;
        $classTo = $this->class;

        $params = array();
        if ($this->method == 'POST')
            $params = $this->app->request()->post();
        if ($this->method == 'GET')
            $params = $this->app->request()->get();
        if ($this->method == 'PUT')
            $params = $this->app->request()->put();
        
        if (!is_object($classTo)) {
            $values = array();
            preg_match('/(.+)\((.+)\)/', $classTo, $values);
            if (count($values) > 0) {
                $filter = array();
                $items=new \Raptor\Util\ItemList($params);
                $filter[$values[2]] = $items->get($values[2],NULL);
                $classTo = $this->app->getStore()->getManager()->getRepository($values[1])->findOneBy($filter);
                if(!$classTo){
                    $classTo=$alias->get(0);
                    $classTo = new $classTo();
                }
                
            } else {
                $alias = new \Raptor\Util\ItemList(explode(':', $classTo));
                if ($alias->size() > 1) {
                    $classTo = $alias->get(0);
                    $namespace = $this->app->getStore()->getManager()->getConfiguration()->getEntityNamespace($classTo);
                    $namespace.='\\' . $alias->get(1);
                    $classTo = $namespace;
                }
                $classTo = new $classTo();
            }
        }

        $this->entity = Collector::run($classTo, new \Raptor\Util\ItemList($params), $this->matcher);
        $this->isExecuted = true;
        return true;
    }

}

?>
