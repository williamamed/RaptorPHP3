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
class AuditoriesController extends Controller {

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/auditories")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/Auditories/index.html.twig');
    }

    /**
     * @Route("/auditories/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {
        
        $listObj=  $this->getStoreManager()
                    ->getRepository('SyntarsusBundle:SecurityTrace')
                        ->getListTraceSearch($request->post('start'),$request->post('limit'),$request);
        $list=new \Raptor\Util\ItemList($listObj['result']);
        
        $list->toArray(function(&$value){
            $date=$value['aDate'];
            if($date instanceof \DateTime){
                $value['aDate']=$date->format('d-n-Y  h:i A');
            }
        });
        $count=$listObj['count'];
        $result=new \Raptor\Util\ItemList();
        $result->set("result",$count);
        $result->set("success",true);
        $result->set("rows",$list->getArray());
        return $this->JSON($result);
    }
    
    /**
     * @Route("/auditories/listestadistic")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function estadisticAction($request, $response, $route) {
        $levels = array(
            1 => 'EMERGENCY',
            2     => 'ALERT',
            3  => 'CRITICAL',
            4     => 'ERROR',
            5      => 'WARNING',
            6    => 'NOTICE',
            7      => 'INFO',
            8     => 'DEBUG'
        );
        $colors = array(
            1 => '#B30606',
            2     => '#D4A007',
            3  => '#B30606',
            4     => '#B30606',
            5      => '#D4A007',
            6    => '#12048F',
            7      => '#069407',
            8     => '#D4A007'
        );
        
        $lev=array();
        for ($index = 1; $index <= count($levels); $index++) {
            $lev[]=array(
                'name'=>$levels[$index],
                'color'=>$colors[$index],
                'cant'=>0,
                'percent'=>0
                );
        }
        $listObj=  $this->getStoreManager()
                    ->getRepository('SyntarsusBundle:SecurityTrace')
                        ->getEstadistic();
        
        $total=  $this->getStoreManager()
                    ->getRepository('SyntarsusBundle:SecurityTrace')
                        ->getEstadisticTotal();
        foreach ($listObj as $value) {
            $lev[$value['name'] - 1]['name'] = $levels[$value['name']];
            $lev[$value['name'] - 1]['color'] = $colors[$value['name']];
            $lev[$value['name'] - 1]['percent'] = ($value['cant'] / $total) * 100;
            $lev[$value['name'] - 1]['cant'] = $value['cant'];
        }

        return $this->JSON($lev);
    }
    
    /**
     * @Route("/auditories/delete")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteAction($request, $response, $route) {
        $listdelete=  json_decode($request->post('ids'));
        foreach ($listdelete as $value) {
            $element= $this->getStore()->getManager()->find('SyntarsusBundle:SecurityTrace', $value);
            $this->getStore()->getManager()->remove($element);
        }
        $this->getStoreManager()->flush();
        return $this->show($this->lang('globdeletemsg'));
    }

}

?>
