<?php
namespace Raptor\Component\systemBundle;
use Raptor\Bundle\Annotations\Route;
/**
 * Description of examplePkg
 * 
 * @Route("/raptor")
 */
class systemBundle extends \Raptor\Bundle\Bundle {

    public function registerBundleAspect(\Go\Core\AspectContainer $appAspectContainer) {
        $appAspectContainer->registerAspect(new Aspect\InyectAspect());
    }

    public function entrance(\Raptor\Raptor $app) {
        
    }

    public function registerRouteRule(\Raptor\Bundle\Route\RuleContainer $ruleContainer) {
            $ruleContainer->add('[\/\w]*', new Rule\Panel(),10);
            $ruleContainer->add('[\/\w]*', new Rule\Client(),10);
            $ruleContainer->add('/raptor[\/\w]*', new Rule\Firewall(),10);
            $ruleContainer->add('/interactive/tutorial', new Tutorials\Guia());
            
            $ruleContainer->add('/command/raptor', new Rule\Command());
            
            $this->app->getInyector()->add($this->app->getSecurity());
            $this->app->getInyector()->add($this->app);
            $this->app->getInyector()->add($this->app->getStore());
            $this->app->getInyector()->add($this->app->request());
            $this->app->getInyector()->add($this->app->response());
            $this->app->getInyector()->add($this->app->router());
            
            $this->app->addCommand(new Command\MigrateCommand());
    }

}

?>
