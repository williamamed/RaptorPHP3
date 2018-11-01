<?php

/**
 * Generated with RAPTOR NEMESIS
 * You can add a route prefix to this Controller
 * puting a  to this class.
 */

namespace Raptor2\SyntarsusBundle\Controller;

use Raptor\Bundle\Controller\Controller;
use Raptor\Util\ItemList;
use Raptor\Bundle\Annotations\Route;
/**
 * @Route("/syntarsus")
 */
class PrivilegeController extends Controller {

    const INDEX = 0;
    const ACTION = 1;
    const DIR = 2;

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/privilege")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/privilege/index.html.twig');
    }

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/action")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexActionAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/action/index.html.twig');
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {

        $node = $request->post('node') == 'root' ? 0 : $request->post('node');
        $list = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')
                        ->findBy(array('belongs' => $node)));

        $list->toArray(function(&$item) {
            if ($item['idRol'])
                $item['idRol'] = '';
            if ($item['type'] == PrivilegeController::INDEX) {
                $item['iconCls'] = 'icon-privilege';
                $item['expandable'] = false;
                $item['indexrender'] = true;
            }
            if ($item['type'] == 2) {
                $item['expandable'] = true;
            }
        });
        return $this->JSON($list);
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/listaction")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listActionAction($request, $response, $route) {

        $node = $request->post('belongs');
        $list = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')
                        ->findBy(array('belongs' => $node, 'type' => PrivilegeController::ACTION)));

        $description = $this->app->getConfigurationLoader()->getRoutesDescriptions();
        $list->toArray(function(&$item)use($description) {
            if ($item['idRol'])
                $item['idRol'] = '';
            $item['iconCls'] = 'icon-action';
            if (isset($description[$item['route']]))
                $item['description'] = '<i style="color:gray">' . $description[$item['route']][0] . '</i>';
        });
        return $this->JSON($list);
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/listroutes")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listRoutesAction($request, $response, $route) {

        $routes = new ItemList($this->app->getConfigurationLoader()->getRoutes());
        $route = $request->post('query');
        $result = $routes->filter(function($value) use ($route) {
            return (strpos($value[0], $route) === 0);
        });
        $return = new ItemList();
        $result->each(function($key, &$value) use ($route, &$return) {
            $return->add(array('route' => $value[0]));
        });
        return $this->JSON($return);
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/addDir")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertDirAction($request) {
        $this->hasCsrfProtection();

        $privilege = $this->collector('SyntarsusBundle:SecurityPrivilege');
        $privilege->setName($request->post('name'));

        $privilege->setType(PrivilegeController::DIR);
        $privilege->setBelongs($request->post('belongs') == 'root' ? 0 : $request->post('belongs'));
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('insert_con_msg_priv'));
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/addIndex")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertIndexAction($request) {
        $this->hasCsrfProtection();

        $privilege = $this->collector('SyntarsusBundle:SecurityPrivilege');
        $privilege->setType(PrivilegeController::INDEX);
        $privilege->setBelongs($request->post('belongs') == 'root' ? 0 : $request->post('belongs'));
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();

        $routes = new ItemList($this->app->getConfigurationLoader()->getRoutes());
        $route = $privilege->getRoute();
        if (strlen($route) > 0 and $route[strlen($route) - 1] != '/') {
            $route.='/';
        }
        $result = $routes->filter(function($value) use ($route) {
            return (strpos($value[0], $route) === 0);
        });

        $store = $this->getStore();
        $result->each(function($key, &$value) use ($route, &$store, $privilege) {
            $sub = str_replace($route, "", $value[0]);
            if ($sub) {
                if ($sub[0] == '/')
                    $sub = substr($sub, 1);
                $sub = str_replace(':', ':', $sub);
                $sub = str_replace('/', '_', $sub);
                $p = new \Raptor2\SyntarsusBundle\Model\Entity\SecurityPrivilege();
                $p->setBelongs($privilege->getId());
                $p->setName($sub);
                $p->setRoute($value[0]);
                $p->setType(PrivilegeController::ACTION);
                $store->getManager()->persist($p);
            }
        });
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('insert_ind_msg_priv'));
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/insertaction")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertActionAction($request) {
        $this->hasCsrfProtection();

        $privilege = $this->collector('SyntarsusBundle:SecurityPrivilege');
        $privilege->setType(PrivilegeController::ACTION);
        $privilegeParent = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($request->post('belongs'));
        $privilege->setRoute($privilegeParent->getRoute() . '/' . $request->post('name'));
        $privilege->setBelongs($request->post('belongs') == 'root' ? 0 : $request->post('belongs'));
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('insert_act_msg_priv'));
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/editaction")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function editActionAction($request) {
        $this->hasCsrfProtection();
        $privilege = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($request->post('id'));
        $this->collector($privilege);
        $sub = str_replace(':', ':', $request->post('name'));
        $sub = str_replace('/', '_', $sub);
        $route = str_replace('_', '/', $sub);
        $privilegeParent = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($privilege->getBelongs());
        $privilege->setRoute($privilegeParent->getRoute() . '/' . $route);
        $privilege->setName($sub);
        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('edit_msg_action'));
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/deleteaction",description="Esta accion permite al usuario la eliminacion de acciones")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteActionAction($request) {
        $this->hasCsrfProtection();
        $privilege = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($request->post('id'));
        try {
            $this->getStore()->getManager()->remove($privilege);
            $this->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            return $this->show($this->lang('fail_delete'), true, Controller::ERROR);
        }


        return $this->show($this->lang('delete_act_msg_priv'));
    }

    /**
     * This funtion belongs to the privilege view
     * 
     * @Route("/privilege/edit",description="Esta accion permite al usuario la edicion de privilegios")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function editAction($request) {
        $this->hasCsrfProtection();

        $privilege = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($request->post('id'));
        $before = $privilege->getRoute();
        $this->collector($privilege);

        $childs = $this->getStoreManager()->getRepository('SyntarsusBundle:SecurityPrivilege')->findBy(array('type' => PrivilegeController::ACTION, 'belongs' => $request->post('id')));
        $route = $privilege->getRoute();
        foreach ($childs as $action) {
            $action->setRoute(str_replace($before, $route, $action->getRoute()));
            $this->getStore()->getManager()->persist($action);
        }


        $this->getStore()->getManager()->persist($privilege);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('edit_msg_priv'));
    }

    /**
     * This funtion belongs to the privilege view
     * [TODO] Delete recursive all privilege
     * 
     * @Route("/privilege/delete",description="Esta accion permite al usuario la eliminacion de privilegios")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteAction($request) {

        $this->hasCsrfProtection();

        $privilege = $this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityPrivilege')
                ->find($request->post('id'));
        $dependency = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')
                        ->findBy(array('belongs' => $request->post('id'))));
        if ($dependency->size() > 0 and $privilege->getType() == PrivilegeController::DIR)
            return $this->show($this->lang('fail_delete_childs'), true, Controller::ERROR);
        if ($dependency->size() > 0 and $privilege->getType() == PrivilegeController::INDEX) {
            $me = $this;
            $dependency->each(function($key, $value) use (&$me) {
                if ($value->getType() == PrivilegeController::ACTION)
                    $me->getStore()->getManager()->remove($value);
            });
        }
        try {
            $this->getStore()->getManager()->remove($privilege);
            $this->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            return $this->show($this->lang('fail_delete'), true, Controller::ERROR);
        }

        return $this->show($this->lang('delete_msg_priv'));
    }

}

?>
