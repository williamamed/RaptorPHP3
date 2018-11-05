<?php
/**
 * Generated with RAPTOR NEMESIS
 * You can add a route prefix to this Controller
 * puting a @Route("annotation") to this class.
 */

namespace Raptor2\SyntarsusBundle\Controller;

use Raptor\Bundle\Controller\Controller;
use Raptor\Bundle\Annotations\Route;
/**
 * @Route
 */
class DefaultController extends Controller{
    
    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/syntarsus/login",rule="securitymanager")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request,$response,$route) {
        if($request->get('redirect'))
            $this->redirect(urldecode($request->get('redirect')),false);
        else
            return "No se especifico ninguna redireccion......";
    }
    
    /**
     * 
     *
     * @Route("/security/logout")
     * 
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function logoutAction($request,$response,$route) {
        $this->getApp()->getSecurity()->getManager()->logout();
        if($request->get('redirect'))
            $this->redirect(urldecode($request->get('redirect')),false);
    }
    
    
}

?>
