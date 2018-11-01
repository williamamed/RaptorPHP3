<?php
/**
 * Generated by Raptor
 * You can add a route prefix to this bundle
 * puting a @Route("annotation") to this class.
 * In this class you need to register your Bundle Aspects and your Rules for routes executions
 */
namespace Raptor2\ServiceBundle;
use Raptor\Bundle\Bundle;
use Go\Core\AspectContainer;
use Raptor\Raptor;
use Raptor\Bundle\Route\RuleContainer;
use Raptor\Bundle\Annotations\Route;
/**
 * @Route 
 */
class ServiceBundle extends Bundle{

    public function registerBundleAspect(AspectContainer $appAspectContainer) {

    }

    public function entrance(Raptor $app) {

    }

    public function registerRouteRule(RuleContainer $ruleContainer) {
        $ruleContainer->add("/raptor/",new Rule\Plugin());
        
    }

}

?>