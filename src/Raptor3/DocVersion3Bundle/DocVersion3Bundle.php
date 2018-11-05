<?php
/**
 * Generado por RAPTOR PHP 3
 * Puedes adicionar un prefijo de ruta para este componente
 * estableciendo la anotacion \@Route en la clase.
 * En esta clase necesitas registrar todos los Aspectos y Reglas para las ejecuciones de rutas
 */
namespace Raptor3\DocVersion3Bundle;
use Raptor\Bundle\Bundle;
use Go\Core\AspectContainer;
use Raptor\Raptor;
use Raptor\Bundle\Route\RuleContainer;
use Raptor\Bundle\Annotations\Route;

/**
 * @Route("")
 */
class DocVersion3Bundle extends Bundle{

    public function registerBundleAspect(AspectContainer $appAspectContainer) {

    }

    public function entrance(Raptor $app) {

    }

    public function registerRouteRule(RuleContainer $ruleContainer) {
        $ruleContainer->addGuia(new Tutorials\Guia());
        $ruleContainer->addRule(new Rule\Plugin());
    }

}

?>