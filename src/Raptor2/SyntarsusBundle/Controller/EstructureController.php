<?php

/**
 * Generated with RAPTOR NEMESIS
 * You can add a route prefix to this Controller
 * puting a @Route("annotation") to this class.
 */

namespace Raptor2\SyntarsusBundle\Controller;

use Raptor\Bundle\Controller\Controller;
use Raptor\Util\ItemList;
use Raptor\Bundle\Annotations\Route;
/**
 * @Route("/syntarsus")
 */
class EstructureController extends Controller {

    /**
     * 
     *
     * @Route("/estructure")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/estructure/index.html.twig');
    }

    /**
     * @Route("/estructure/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {
        
        
        $node=$request->get('node')=='root'?0:$request->post('node');
        if($node==0){
            $session=$this->getSecurityUser();
            $user=  $this->getStore()
                    ->getManager()
                    ->getRepository('SyntarsusBundle:SecurityUser')
                    ->findOneBy(array('username'=>$session->get('username')));
            $node=$user->getIdEstructure()->getBelongs();
        }
        $list=new ItemList($this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->findBy(array('belongs'=>$node)));
       
       $list->toArray(function(&$item){
           if($item['idCategory'] !==NULL) 
            $item['idCategory']=$item['idCategory']->getId();
        });
        return $this->JSON($list);
    }
    
    /**
     * @Route("/estructure/listcategory")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listCategoryAction($request) {
        $list=new ItemList($this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityCategory')
                ->findAll());
       
       
        return $this->JSON($list);
    }
    
    /**
     * @Route("/estructure/insert",name="_security_estructure_insert")
     */
    public function insertAction($request) {
        $this->hasCsrfProtection();
        
        $privilege=$this->collector('SyntarsusBundle:SecurityEstructure');
        $privilege->setBelongs($request->post('index')=='root'?0:$request->post('index'));
        if($request->post('idCategory'))
            $privilege->setIdCategory($this->getStore()
                    ->getManager ()
                    ->getRepository ('SyntarsusBundle:SecurityCategory')
                    ->find ($request->post('idCategory')));
        else
            $privilege->setIdCategory(NULL);
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('add_msg_estructure'));
    }
    
   /**
    * @Route("/estructure/edit",name="_security_estructure_edit")
    */
    public function editAction($request) {
        $this->hasCsrfProtection();
        
        $privilege=$this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->find($request->post('id'));
        $this->collector($privilege);
        if($request->post('idCategory'))
            $privilege->setIdCategory($this->getStore()
                    ->getManager ()
                    ->getRepository ('SyntarsusBundle:SecurityCategory')
                    ->find ($request->post('idCategory')));
        else
            $privilege->setIdCategory(NULL);
        
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('edit_msg_estructure'));
    }
    
   /**
    * @Route("/estructure/delete",name="_security_estructure_delete")
    * 
    */
    public function deleteAction($request) {
        
        $this->hasCsrfProtection();
        
        $privilege=$this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->find($request->post('id'));
        $dependency=new ItemList($this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->findBy(array('belongs'=>$request->post('id'))));
        
        if($dependency->size()>0 )
            return $this->show($this->lang('delete_msg_error_estructure'),true,  Controller::ERROR);
        if($dependency->size()>0){
            $me=$this;
            $dependency->each (function($key,$value) use (&$me) {
                    $me->getStore()->getManager()->remove($value);
            });
        }
        try {
            $this->getStore()->getManager()->remove($privilege);
            $this->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            return $this->show($this->lang('fail_delete'), true,  Controller::ERROR);
        }

        
        return $this->show($this->lang('delete_msg_estructure'));
    }
    
}

?>
