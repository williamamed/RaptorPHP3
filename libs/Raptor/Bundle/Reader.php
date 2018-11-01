<?php

/**
 * Raptor - Integration PHP 5 framework
 *
 * @author      William Amed <watamayo90@gmail.com>, Otto Haus <ottohaus@gmail.com>
 * @copyright   2014 
 * @link        http://dinobyte.net
 * @version     2.0.1
 * @package     Raptor
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Raptor\Bundle;

/**
 * 
 * Esta clase lee la definicion de rutas de los bundles
 * [USO INTERNO DEL SISTEMA]
 * 
 */
class Reader {

    private $bundles;
    private $definitions;
    private $location;
    private $specif;
    private $description;
    private $api;
    private $rules;

    function __construct() {
        $this->bundles = array();
        $this->definitions = array();
        $this->location = array();
        $this->description = array();
        $this->api = array();
        $this->rules = array();
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @param type $bundles
     */
    public function setBundles($bundles) {
        $this->bundles = $bundles;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @return type
     */
    public function getDefinitions() {
        return $this->definitions;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @return type
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @return type
     */
    public function getSpecifications() {
        return $this->specif;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     * @return type
     */
    public function getDescriptions() {
        return $this->description;
    }

    /**
     * [USO INTERNO DEL SISTEMA] 
     * @return type
     */
    public function getApi() {
        return $this->api;
    }

    /**
     * [USO INTERNO DEL SISTEMA] 
     * @return type
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * [USO INTERNO DEL SISTEMA]
     */
    public function load() {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace("Raptor\Bundle\Annotations", __DIR__ . "/../../");
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('RouteName');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('api');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Description');

        foreach ($this->bundles as $bundle) {

            $class = new \Wingu\OctopusCore\Reflection\ReflectionClass($bundle);

            $prefix = '';
            $this->location[$class->getShortName()] = \Raptor\Util\ClassLocation::getLocation($bundle);
            $this->specif[$class->getShortName()] = array('location' => \Raptor\Util\ClassLocation::getLocation($bundle), 'namespace' => $class->getNamespaceName(), 'name' => $class->getName());

            $controllerDir = \Raptor\Util\ClassLocation::getLocation($bundle) . '/Controller';
            $listfiles = \Raptor\Util\Files::find($controllerDir, "*Controller.php");

            $reflClassBundle = new \ReflectionClass($bundle);
            try {
                $classAnnotationsBundle = $reader->getClassAnnotations($reflClassBundle);
            } catch (\Exception $exc) {
                $template = new \Raptor\Exception\Listener\RaptorExceptions();
                echo \Raptor\Raptor::getInstance()->response()->body($template->errorTemplate($exc));
                die;
            }

            foreach ($classAnnotationsBundle AS $annot) {

                if ($annot instanceof Annotations\Route) {
                    $prefix = $annot->path;
                    if ($annot->rule) {
                        $rules = explode(',', str_replace(' ', '', $annot->rule));
                        foreach ($rules as $rule) {
                            $this->rules[] = array($prefix, $rule);
                        }
                    }
                }
            }

            foreach ($listfiles as $nombre_fichero) {
                $prefixController = $prefix;
                $namespaceSrc = explode('src', $nombre_fichero);
                $real = array();
                if (count($namespaceSrc) > 1)
                    $real = $namespaceSrc[1];
                else {
                    $namespaceSrc = explode('libs', $nombre_fichero);
                    $real = $namespaceSrc[1];
                }
                $namespaceClass = str_replace('.php', '', $real);
                $namespace = str_replace('/', '\\', $namespaceClass);

                $controller = new \Wingu\OctopusCore\Reflection\ReflectionClass($namespace);

                $reflClassController = new \ReflectionClass($namespace);
                try {
                    $classAnnotationsController = $reader->getClassAnnotations($reflClassController);
                } catch (\Exception $exc) {
                    $template = new \Raptor\Exception\Listener\RaptorExceptions();
                    echo \Raptor\Raptor::getInstance()->response()->body($template->errorTemplate($exc));
                    die;
                }

                foreach ($classAnnotationsController AS $annot) {

                    if ($annot instanceof \Raptor\Bundle\Annotations\Route) {
                        $prefixController = $prefixController . $annot->path;
                        if ($annot->rule) {
                            $rules = explode(',', str_replace(' ', '', $annot->rule));
                            foreach ($rules as $rule) {
                                $this->rules[] = array($prefixController, $rule);
                            }
                            //$this->rules[] = array($prefixController, $annot->rule);
                        }
                    }
                }


                foreach ($controller->getMethods() as $method) {
                    $api = new \stdClass();
                    $api->hasApi = false;
                    $api->version = '0.0.0';
                    $api->text = '';


                    try {
                        $methods = $reader->getMethodAnnotation(new \ReflectionMethod($namespace, $method->getName()), 'Raptor\Bundle\Annotations\Route');
                    } catch (\Exception $exc) {
                        $template = new \Raptor\Exception\Listener\RaptorExceptions();
                        echo \Raptor\Raptor::getInstance()->response()->body($template->errorTemplate($exc));
                        die;
                    }

                    try {
                        $apis = $reader->getMethodAnnotation(new \ReflectionMethod($namespace, $method->getName()), 'Raptor\Bundle\Annotations\Api');
                    } catch (\Exception $exc) {
                        $template = new \Raptor\Exception\Listener\RaptorExceptions();
                        echo \Raptor\Raptor::getInstance()->response()->body($template->errorTemplate($exc));
                        die;
                    }
                    $api->route = '';
                    if ($apis) {
                        $api->text = $method->getReflectionDocComment()->getFullDescription();
                        $api->name = $apis->name;
                        $api->category = $apis->category;
                        $api->class = $method->getDeclaringClass()->getName();
                        $api->bundle = $class->getName();
                        $api->method = $method->getName();
                        $api->hasApi = true;

                        $api->version = $apis->version;
                    }

                    if ($methods) {
                        if ($methods->name)
                            $collectionName = $methods->name;
                        else
                            $collectionName = str_replace('/', '_', $prefixController . $methods->path);
                        $methodName = 'ANY';
                        if ($methods->method) {
                            $methodName = $methods->method;
                        }
                        if ($methods->rule) {
                            $rules = explode(',', str_replace(' ', '', $methods->rule));
                            foreach ($rules as $rule) {
                                $this->rules[] = array($prefixController . $methods->path, $rule);
                            }
                            //$this->rules[] = array($prefixController . $methods->path, $methods->rule);
                        }
                        $descrip = '';
                        if ($methods->description) {
                            $descrip = $methods->description;
                            $this->description[$prefixController . $methods->path] = array($descrip, $method->getReflectionDocComment()->getFullDescription());
                        }
                        $api->route = $prefixController . $methods->path;
                        $this->definitions[$collectionName] = array($prefixController . $methods->path, $method->getDeclaringClass()->getName(), $method->getName(), $class->getName(), 'method' => $methodName, 'csrf' => $methods->csrf ? true : false);
                    }
                    if ($api->hasApi) {
                        if (!isset($this->api[$api->category]))
                            $this->api[$api->category] = array();
                        $this->api[$api->category][] = $api;
                    }
                }
            }





            //////////////////////////
            /*             * if (!$class->getReflectionDocComment()->isEmpty() and $class->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Route')) {
              $doc = $class->getReflectionDocComment();
              $obj = $doc->getAnnotationsCollection()->getAnnotation('Route');
              $prefix = $obj[0]->getDescription();
              }

              $controllerDir = \Raptor\Util\ClassLocation::getLocation($bundle) . '/Controller';
              $listfiles = \Raptor\Util\Files::find($controllerDir, "*Controller.php");

              foreach ($listfiles as $nombre_fichero) {
              $prefixController = $prefix;
              $namespaceSrc = explode('src', $nombre_fichero);
              $real = array();
              if (count($namespaceSrc) > 1)
              $real = $namespaceSrc[1];
              else {
              $namespaceSrc = explode('libs', $nombre_fichero);
              $real = $namespaceSrc[1];
              }
              $namespaceClass = str_replace('.php', '', $real);
              $namespace = str_replace('/', '\\', $namespaceClass);

              $controller = new \Wingu\OctopusCore\Reflection\ReflectionClass($namespace);
              if (!$controller->getReflectionDocComment()->isEmpty() and $controller->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Route')) {
              $doc = $controller->getReflectionDocComment();
              $obj = $doc->getAnnotationsCollection()->getAnnotation('Route');
              $prefixController = $prefixController . $obj[0]->getDescription();
              }

              foreach ($controller->getMethods() as $method) {

              $api = new \stdClass();
              $api->hasApi = false;
              $api->version = '0.0.0';
              $api->text = '';
              $doc = $method->getReflectionDocComment();
              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('api')) {
              $api->text = $method->getReflectionDocComment()->getFullDescription();
              $collectionDescrip = $doc->getAnnotationsCollection()->getAnnotation('api');
              $api->category = $collectionDescrip[0]->getDescription();
              $api->class = $method->getDeclaringClass()->getName();
              $api->bundle = $class->getName();
              $api->method = $method->getName();
              $api->hasApi = true;
              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Route')) {
              $doc = $method->getReflectionDocComment();
              $collectionRouteObj = $doc->getAnnotationsCollection()->getAnnotation('Route');
              $collectionRoute = $collectionRouteObj[0];
              $api->route = $prefixController . $collectionRoute->getDescription();
              } else {
              $api->route = false;
              }

              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('version')) {
              $doc = $method->getReflectionDocComment();
              $collectionRouteObj = $doc->getAnnotationsCollection()->getAnnotation('version');
              $collectionRoute = $collectionRouteObj[0];
              $api->version = $collectionRoute->getDescription();
              }
              }
              if ($api->hasApi) {
              if (!isset($this->api[$api->category]))
              $this->api[$api->category] = array();
              $this->api[$api->category][] = $api;
              }

              if (!$method->getReflectionDocComment()->isEmpty() and $method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Route')) {

              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('RouteName')) {
              $collectionNameObj = $doc->getAnnotationsCollection()->getAnnotation('RouteName');
              $collectionName = $collectionNameObj[0]->getDescription();
              } else {
              $routeArray = $doc->getAnnotationsCollection()->getAnnotation('Route');
              $Route = $routeArray[0];
              $collectionName = str_replace('/', '_', $prefixController . $Route->getDescription());
              }


              $collectionRouteObj = $doc->getAnnotationsCollection()->getAnnotation('Route');

              $collectionRoute = $collectionRouteObj[0];
              $descrip = "";

              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Description')) {
              $collectionDescrip = $doc->getAnnotationsCollection()->getAnnotation('Description');
              $descrip = $collectionDescrip[0]->getDescription();
              }
              $methodName = 'ANY';
              if ($method->getReflectionDocComment()->getAnnotationsCollection()->hasAnnotationTag('Method')) {
              $collectionMethod = $doc->getAnnotationsCollection()->getAnnotation('Method');
              $methodName = $collectionMethod[0]->getDescription();
              }

              $this->definitions[$collectionName] = $api;
              $this->definitions[$collectionName] = array($prefixController . $collectionRoute->getDescription(), $method->getDeclaringClass()->getName(), $method->getName(), $class->getName(), 'method' => $methodName);
              $this->description[$prefixController . $collectionRoute->getDescription()] = array($descrip, $method->getReflectionDocComment()->getFullDescription());
              }
              }
              } */
        }
    }

}

?>
