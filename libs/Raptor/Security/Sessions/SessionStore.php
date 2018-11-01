<?php

/**
 * Crea una interfaz de acceso y modificacion de los datos de la session
 * a traves de un ItemList
 * @author Dinobyte
 */
namespace Raptor\Security\Sessions;
use Raptor\Util\ItemList;

class SessionStore extends ItemList {

    function __construct($items=array()) {
        parent::__construct((count($items) == 0) ? \Raptor\Raptor::getInstance()->getSecurity()->getUser() : $items);
    }
    /**
     * Setea un elemento en la sesion del usuario
     * 
     * @param string $key
     * @param mixed $item
     */
    public function set($key, $item) {
        parent::set($key, $item);

        //$session = \Raptor\Raptor::getInstance()->getSession();
        //$session->put($this->getArray());
         
    }
    /**
     * Elimina un elemento de la session del usuario
     * @param integer $pos
     */
    public function remove($pos) {
         parent::remove($pos);
         //$session = \Raptor\Raptor::getInstance()->getSession();
         //$session->put($this->getArray());
    }
    /**
     * Ejecuta una operacion de escritura de los datos modificados en la sesion
     */
    public function flush() {
         
         $session = \Raptor\Raptor::getInstance()->getSession();
         $session->put($this->getArray());
    }
    
    /**
     * Retorna la Sesion para este SessionStore
     * @return \Raptor\Security\Sessions\NativeSession
     */
    public function getSession() {
        return \Raptor\Raptor::getInstance()->getSession();
    }
}

?>
