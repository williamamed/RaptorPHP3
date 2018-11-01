<?php
namespace examples\exampleBundle;
use Raptor\Bundle\Annotations\Route;
/**
 * Description of examplePkg
 * 
 * @Route("/examples")
 */
class exampleBundle extends \Raptor\Bundle\Bundle {

    public function registerBundleAspect(\Go\Core\AspectContainer $appAspectContainer) {
        $appAspectContainer->registerAspect(new \examples\exampleBundle\Aspect\AccessAspect());
        
        
    }

    public function entrance(\Raptor\Raptor $app) {
        
    }

    public function registerRouteRule(\Raptor\Bundle\Route\RuleContainer $ruleContainer) {
        //$ruleContainer->add('/examples/new/example', new Rule\MyRule());
        
        
    }

}

?>
