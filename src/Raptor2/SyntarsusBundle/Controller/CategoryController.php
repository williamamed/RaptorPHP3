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
class CategoryController extends Controller {

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/category")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/Category/index.html.twig');
    }

    /**
     * @Route("/category/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {

        $users = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityCategory')
                        ->findAll());

        
        return $this->JSON($users);
    }

    
    /**
     * @Route("/category/insert")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertAction($request) {

        $this->hasCsrfProtection();

        $cat = $this->collector('SyntarsusBundle:SecurityCategory');
        
        $this->getStore()->getManager()->persist($cat);
        $this->getStore()->getManager()->flush();

        return $this->JSON(new ItemList(array($cat)));
    }


    
    /**
     * @Route("/category/edit/:id")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function editAction($request,$response,$route) {
        $this->hasCsrfProtection();
        
        $cat=$this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityCategory')->find($route->getParam('id'));
        $this->collector($cat);
        
        $this->getStore()->getManager()->persist($cat);
        $this->getStore()->getManager()->flush();
        
        return $this->JSON(new ItemList(array($cat)));
    }
    
    /**
     * @Route("/category/delete/:id")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteAction($request,$response,$route) {
        
        $this->hasCsrfProtection();
        
        
        $cat=$this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityCategory')->find($route->getParam('id'));
        
        try {
            $this->getStore()->getManager()->remove($cat);
            $this->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            return $this->show($this->lang('fail_delete'), true,  Controller::ERROR);
        }

        
        
        return $this->JSON(new ItemList(array($cat)));
    }

}

?>
