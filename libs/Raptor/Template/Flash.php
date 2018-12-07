<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Raptor\Template;

/**
 * Description of Flash
 *
 * @author william.tamayo
 */
class Flash extends \Slim\Middleware\Flash{
    
    /**
     * Save
     */
    public function save()
    {
        $this->app->getSession()->set($this->settings['key'],$this->messages['next']);
    }
    
    /**
     * Load messages from previous request if available
     */
    public function loadMessages()
    {

        if (isset($_SESSION[$this->settings['key']])) {
            $this->messages['prev'] = $this->app->getSession()->get($this->settings['key']);
        }
    }
}
