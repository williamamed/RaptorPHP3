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
class RolController extends Controller {

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/rol")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/rol/index.html.twig');
    }

    /**
     * @Route("/rol/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {
        $node = $request->post('node') == 'root' ? 0 : $request->post('node');
        
        $list = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')
                        ->findBy(array('belongs' => $node)));

        return $this->JSON($list);
    }

    /**
     * @Route("/rol/insert")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertAction() {
        $this->hasCsrfProtection();
        $user = $this->collector('SyntarsusBundle:SecurityRol');
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('insert_msg_rol'));
    }

   

    /**
     * @Route("/rol/edit")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function editAction($request) {
        $this->hasCsrfProtection();

        $user = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')->find($request->post('id'));
        $this->collector($user);
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('edit_msg_rol'));
    }

    /**
     * @Route("/rol/delete")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteAction($request) {

        $this->hasCsrfProtection();
        $childs = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')->findBy(array('belongs' => $request->post('id')));

        $user = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')->find($request->post('id'));

        try {
            $this->getStore()->getManager()->remove($user);
            if (count($childs) > 0) {
                foreach ($childs as $child) {
                    $child->setBelongs($user->getBelongs());
                    $this->getStore()->getManager()->persist($child);
                }
            }
            $this->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            return $this->show($this->lang('fail_delete'), true, Controller::ERROR);
        }


        return $this->show($this->lang('delete_msg_rol'));
    }

    /**
     * @Route("/rol/asignprivilege")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function asignPrivilegeAction($request) {
        $this->hasCsrfProtection();

        $rol = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')->find($request->post('id'));
        $rol->getIdPrivilege()->clear();
        $newIds = json_decode($request->post('privileges'));
        foreach ($newIds as $value) {

            $rol->addSecurityPrivilege($this->getStore()
                            ->getManager()
                            ->getRepository('SyntarsusBundle:SecurityPrivilege')->find($value));
        }
        $this->getStore()->getManager()->persist($rol);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('priv_msg_rol'));
    }

    /**
     * @Route("/rol/listprivileges")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listPrivilegesAction($request) {


        $rol = $this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityRol')->find($request->post('id'));
        $ownPrivilege = new ItemList($rol->getIdPrivilege()->toArray());
        $ownPrivilege->toArray(function(&$item) {
                    if ($item['idRol'])
                        $item['idRol'] = '';
                });
        $privileges = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityPrivilege')->findAll());
        $listPrivilege = new ItemList();
        $privileges->toArray();
        $me = $this;
        $description = $this->app->getConfigurationLoader()->getRoutesDescriptions();
        $own = $ownPrivilege->getArray();

        $privileges->each(function($key, $value, $list) use (&$listPrivilege, &$me, &$own, $description) {
                    if ($value['belongs'] == 0) {

                        $childs = $me->findChilds($value, $list->getArray(), $own, $description);
                        if ($value['type'] == 0)
                            $value['iconCls'] = 'icon-privilege';
                        if ($value['type'] == 1)
                            $value['iconCls'] = 'icon-actions';
                        if (isset($description[$value['route']]))
                            $value['name'] = $value['name'] . '&nbsp&nbsp&nbsp<i style="color:gray">' . $description[$value['route']][0] . '</i>';

                        if (count($childs) > 0) {
                            $value['children'] = $childs;
                            $value['expanded'] = true;
                        } else {
                            $value['expandable'] = false;
                            $value['leaf'] = true;
                        }
                        if ($this->isIn($own, $value))
                            $value['checked'] = true;
                        else
                            $value['checked'] = false;
                        $value['chi'] = count($childs);
                        $listPrivilege->add($value);
                    }
                });

        return $this->JSON($listPrivilege);
    }

    private function findChilds($parent, $all, $own, $description) {
        $childs = array();
        foreach ($all as $key => $value) {
            if ($parent['id'] === $value['belongs']) {
                $childs[] = $value;
                $my = $this->findChilds($value, $all, $own, $description);
                $countChilds = count($childs) - 1;
                if ($value['type'] == 0)
                    $childs[$countChilds]['iconCls'] = 'icon-privilege';
                if ($value['type'] == 1)
                    $childs[$countChilds]['iconCls'] = 'icon-actions';
                if (isset($description[$value['route']]))
                    $childs[$countChilds]['name'] = $value['name'] . '&nbsp&nbsp&nbsp<i style="color:gray">' . $description[$value['route']][0] . '</i>';

                if (count($my) > 0) {
                    $childs[$countChilds]['children'] = $my;
                    $childs[$countChilds]['expanded'] = true;
                } else {
                    $childs[$countChilds]['expandable'] = false;
                    $childs[$countChilds]['leaf'] = true;
                }
                if ($this->isIn($own, $value))
                    $childs[$countChilds]['checked'] = true;
                else
                    $childs[$countChilds]['checked'] = false;
            }
        }
        return $childs;
    }

    private function isIn($a, $b) {
        foreach ($a as $key => $value) {
            if ($value['id'] === $b['id'])
                return true;
        }
        return false;
    }

}

?>
