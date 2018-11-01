<?php
/**
 * Generado por RAPTOR PHP 3
 * Puedes adicionar un prefijo de ruta
 * estableciendo la anotacion \@Route en la clase.
 */

namespace Raptor3\DocVersion3Bundle\Controller;

use Raptor\Bundle\Controller\Controller;
use Raptor\Bundle\Annotations\Route;

/**
 * @Route("/apidoc")
 */
class DefaultController extends Controller{
    
    const VERSION='v3.0.1';

    /**
     * 
     *
     * @Route("")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request,$response,$route) {
       
        return $this->render('@DocVersion3Bundle/api.html.twig',array('version'=>  DefaultController::VERSION));
    }
    
    /**
     * Add your definition Route and the RouteName[optional]
     *
     * @Route("/proyect")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function proyectAction($request,$response,$route) {
        
        return $this->render('@DocVersion3Bundle/organization/index.html.twig',array('version'=>  DefaultController::VERSION));
    }
    
    /**
     * Add your definition Route and the RouteName[optional]
     *
     * @Route("/client")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function clientAction($request,$response,$route) {
        return $this->render('@DocVersion3Bundle/client/index.html.twig',array('version'=>  DefaultController::VERSION));
    }
    
    
    /**
     * Add your definition Route and the RouteName[optional]
     *
     * @Route("/abstraction")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function abstractionAction($request,$response,$route) {
        
        return $this->render('@DocVersion3Bundle/abs/index.html.twig',array('version'=>  DefaultController::VERSION));
    }
    
    /**
     * Add your definition Route and the RouteName[optional]
     *
     * @Route("/server")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function serverAction($request,$response,$route) {
        if($request->get('class')){
            $name=  str_replace('/','\\', $request->get('class'));
            if($name[0]=='\\')
                $name=$name;
            else
                $name='\\'.$name;
            try {
                 $class = new \Wingu\OctopusCore\Reflection\ReflectionClass($name);
            } catch (\Exception $exc) {
                return "No Class API FOUND";
            }

           
            $class_method=array();
            $class_prop=array();
            $class_const=array();
            
            foreach ($class->getProperties(\Wingu\OctopusCore\Reflection\ReflectionProperty::IS_PUBLIC) as $prop) {
                $prop_obj=array();
                $prop_obj['description']=$prop->getReflectionDocComment()->getFullDescription();
                $prop_obj['name']=$prop->getName();
                foreach ($prop->getReflectionDocComment()->getAnnotationsCollection()->getAnnotations() as $tag) {
                    if($tag->getTagName()=='var')
                        $prop_obj['type']=$tag->getDescription();
                }
                
                $class_prop[]=$prop_obj;
            }
            
            foreach ($class->getConstants() as $cons) {
                $cons_obj=array();
                $cons_obj['description']=$cons->getReflectionDocComment()->getFullDescription();
                $cons_obj['name']=$cons->getName();
                $cons_obj['value']=$cons->getValue();
                foreach ($cons->getReflectionDocComment()->getAnnotationsCollection()->getAnnotations() as $tag) {
                    if($tag->getTagName()=='var')
                        $cons_obj['type']=$tag->getDescription();
                }
                
                $class_const[]=$cons_obj;
            }
            
            
            foreach ($class->getMethods(\Wingu\OctopusCore\Reflection\ReflectionMethod::IS_PUBLIC) as $method) {
                $class_obj_method=array();
                $class_obj_method['description']=$method->getReflectionDocComment()->getFullDescription();
                $class_obj_method['tag']=array();
                if($method->isStatic())
                    $class_obj_method['static']=true;
                else
                    $class_obj_method['static']=false;
                
                foreach ($method->getReflectionDocComment()->getAnnotationsCollection()->getAnnotations() as $tag) {
                    $class_obj_method['tag'][]=array($tag->getTagName(),$tag->getDescription());
                }
                $class_obj_method['param']=array();
                foreach ($method->getParameters() as $param) {
                    $class_obj_method['param'][]='$'.$param->getName();
                }
                $class_obj_method['name']=$method->getName();
                
                
                if(substr_count($class_obj_method['name'],'_',0,2)==2);
                    
                else
                    $class_method[]=$class_obj_method;
            }
            
           
            return $this->render('@DocVersion3Bundle/server/class/gen.html.twig',array(
                'method'=>$class_method,
                'namespace'=>$class->getNamespaceName(),
                'classname'=>$class->getName(),
                'version'=>  DefaultController::VERSION,
                'prop'=>$class_prop,
                'cons'=>$class_const,
                'description'=>$class->getReflectionDocComment()->getFullDescription()
            ));
            
            
//            if(file_exists(__DIR__.'/../Views/server/class/'.$request->get('class').'.html.twig'))
//                return $this->render('@DocBundle/server/class/'.$request->get('class').'.html.twig');
//            else
//                return "No Class API FOUND";
        }
        return $this->render('@DocVersion3Bundle/server/index.html.twig',array('version'=>  DefaultController::VERSION));
    }
    
    
}

?>
