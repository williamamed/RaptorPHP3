<?php
namespace examples\exampleBundle\Rule;

class CMS implements \Raptor\Bundle\Route\Rule {
    
    private $text;
    
    function __construct($text) {
        $this->text=$text;
    }

    
    public function call(\Raptor\Raptor $app) {
        
        $app->response()->write($this->text);
        /**
         * Return false to continue the flow of routing
         */
        return true;
    }

}

?>
