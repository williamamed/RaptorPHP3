<?php

namespace Raptor\Component\systemBundle\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation\After;
use Go\Lang\Annotation\Around;
use Go\Lang\Annotation\Before;
use Go\Lang\Annotation\Pointcut;

class InyectAspect implements \Go\Aop\Aspect {
    /**
     * @param MethodInvocation $invocation
     * @Before("execution(public **->*(*)) || execution(protected **->*(*)) || execution(public **::*(*)) || execution(protected **::*(*))")

      protected function beforeExecutionAnyMethod(\Go\Aop\Intercept\MethodInvocation $invocation) {
      $arg = $invocation->getArguments();

      foreach ($invocation->getMethod()->getParameters() as $param) {
      // param name
      if($param->getClass() && $param->isDefaultValueAvailable() && $param->getDefaultValue()==NULL){

      $arg[$param->getPosition()]=\Raptor\Raptor::getInstance()->getInyector()->getByName($param->getClass()->name);
      $invocation->setArguments($arg);

      }
      //echo $param->name,'---',(($param->getClass())?$param->getClass()->name:''),'<br>';
      // param type hint (or null, if not specified).


      }
      // echo 'Calling Before Interceptor for ', $invocation, "<br>";
      } */
    
    /**
     * 
     *
     * @param MethodInvocation $invocation Invocation
     *
     * @Before("@execution(Raptor\Bundle\Annotations\Inyect)")
     */
    public function aroundCacheable(MethodInvocation $invocation) {
        $arg = $invocation->getArguments();
        $toInyect = explode(',', str_replace(' ', '', $invocation->getMethod()->getAnnotation('Raptor\Bundle\Annotations\Inyect')->params));
        
        foreach ($invocation->getMethod()->getParameters() as $param) {
            // param name
            foreach ($toInyect as $inyect) {
                if ($inyect == $param->getName() && $param->getClass() ) {

                    $arg[$param->getPosition()] = \Raptor\Raptor::getInstance()->getInyector()->getByName($param->getClass()->name);
                    $invocation->setArguments($arg);
                }
            }

            //echo $param->name,'---',(($param->getClass())?$param->getClass()->name:''),'<br>';
            // param type hint (or null, if not specified).
        }

       // echo 'Calling Before Interceptor for ', $invocation, "<br>";
    }
    
    /**
     * 
     *
     * @Around("@access(Raptor\Bundle\Annotations\Inyect)")
     */
    public function propertyCacheable(FieldAccess $fieldAccess) {
        $wingu=new \Wingu\OctopusCore\Reflection\ReflectionProperty($fieldAccess->getField()->class, $fieldAccess->getField()->name);
        $fieldAccess->proceed();
        if(count($wingu->getReflectionDocComment()->getAnnotationsCollection()->getAnnotation('var'))>0){
            $datatype=$wingu->getReflectionDocComment()->getAnnotationsCollection()->getAnnotation('var')[0]->getDescription();
            $split=explode('\\', $datatype);
            unset($split[0]);
            
            return \Raptor\Raptor::getInstance()->getInyector()->getByName(join('\\',$split ));
        }
        return NULL;        
    }

}

?>
