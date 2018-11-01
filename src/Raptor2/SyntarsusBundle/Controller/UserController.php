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
class UserController extends Controller {

    /**
     * Add your definition route and the name route[optional]
     *
     * @Route("/user")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function indexAction($request, $response, $route) {
        return $this->render('@SyntarsusBundle/user/index.html.twig');
    }

    /**
     * @Route("/user/list")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listAction($request, $response, $route) {

        $node = $request->post('idEstructure');

        $users = new ItemList($this->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityUser')
                        ->getPageList($node, $request->get('start'), $request->get('limit')));

        $users->toArray(function(&$item) {
            $roles = $item['idRol'];
            $item['password'] = '';
            $item['idEstructure'] = $item['idEstructure']->getName();
            if ($roles) {
                $roleList = array();
                foreach ($roles as $role) {
                    $roleList[] = array('idRol' => $role->getId(), 'rolename' => $role->getName());
                }
                $item['idRol'] = $roleList;
//              $item['idRol'] = $roles->getId();
//              $item['rolname'] = $roles->getName();
            }
        });

        $result = new ItemList();
        $result->set("result", $users->size());
        $result->set("success", true);
        $result->set("rows", $users->getArray());
        return $this->JSON($result);
    }

    /**
     * @Route("/user/listestructure")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listEstructureAction($request, $response, $route) {

        $node = $request->post('node') === 'root' ? 0 : $request->post('node');
        $list = $this->service()->getPrivate('SyntarsusBundle')->listUserStructureByDemand($node);
        return $this->JSON($list);
    }

    /**
     * @Route("/user/insert")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function insertAction($request) {

        $this->hasCsrfProtection();

        $user = $this->collector('SyntarsusBundle:SecurityUser');

        $find = $this->getStoreManager()->getRepository('SyntarsusBundle:SecurityUser')->findOneBy(array('username' => $request->post('username')));
        if ($find)
            return $this->show($this->lang('usernotadded'), true, Controller::ERROR);

        if ($request->file('icon')->get('size') > 0) {
            $rand = rand(0, 500000);
            $web = \Raptor\Core\Location::get(\Raptor\Core\Location::WEBBUNDLES);
            if (!file_exists($web . '/security_photos'))
                mkdir($web . '/security_photos');
            $img = $request->file('icon');
            if ($img->get('type') == "image/gif" || $img->get('type') == "image/jpeg" || $img->get('type') == "image/x-png" || $img->get('type') == "image/png") {
                $user->setIcon($rand . '_' . $request->file('icon')->get('name'));
                $this->moveUploadFileTo('icon', $web . '/security_photos/' . $rand . '_' . $request->file('icon')->get('name'));
            }
        }
        $pass = $request->post('password');
        $estructure = $this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->find($request->post('idEstructure'));
        $user->setIdEstructure($estructure);
        $user->setPassword(\Raptor\Security\SecureHash::hash($pass));
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();

        return $this->show($this->lang('insertmsg'));
    }

    /**
     * @Route("/user/changestate")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function changeStateAction($request) {
        $this->hasCsrfProtection();

        $user = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityUser')->find($request->post('id'));
        $user->setState($request->post('state') == 'true' ? true : false);
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('statemsg'));
    }

    /**
     * @Route("/user/listrol")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function listRolAction() {
        $user = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityUser')->findOneBy(array('username'=>$this->getSecurityUser()->get('username')));
        $roles=$user->getIdRol();
        $listRol=array();
        $list = new ItemList();
        foreach ($roles as $rolId) {
            $list->add($rolId);
            $listR=$this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityRol')->getAllChilds($rolId->getId());
            foreach ($listR as $value) {
                $listRol[$value->getId()]=$value;
            }
        }
        
        foreach ($listRol as $value) {
            $list->add($value);
        }
        $list->toArray();
        return $this->JSON($list);
    }

    /**
     * @Route("/user/asignrol")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function asignRolAction($request) {
        $this->hasCsrfProtection();

        $user = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityUser')->find($request->post('iduser'));
        $user->clearIdRol();
        $roles = explode(',', $request->post('id'));
        foreach ($roles as $rolId) {
            $rol = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityRol')->find($rolId);
            if ($rol) {
                $user->addSecurityRol($rol);
            }
        }
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('rolmsg'));
    }

    /**
     * @Route("/user/edit")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function editAction($request) {
        $this->hasCsrfProtection();

        $user = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityUser')->find($request->post('id'));
        $lastPass = $user->getPassword();
        $this->collector($user);
        $pass = $request->post('password');
        if ($request->post('password') != '0')
            $user->setPassword(\Raptor\Security\SecureHash::hash($pass));
        else
            $user->setPassword($lastPass);
        if ($request->file('icon')->get('size') > 0) {
            $rand = rand(0, 500000);
            $web = \Raptor\Core\Location::get(\Raptor\Core\Location::WEBBUNDLES);
            if (!file_exists($web . '/security_photos'))
                mkdir($web . '/security_photos');
            $img = $request->file('icon');
            if ($img->get('type') == "image/gif" || $img->get('type') == "image/jpeg" || $img->get('type') == "image/x-png" || $img->get('type') == "image/png") {
                if ($user->getIcon() != NULL and file_exists($web . '/security_photos/' . $user->getIcon()))
                    @unlink($web . '/security_photos/' . $user->getIcon());
                $user->setIcon($rand . '_' . $request->file('icon')->get('name'));
                $this->moveUploadFileTo('icon', $web . '/security_photos/' . $rand . '_' . $request->file('icon')->get('name'));
            }
        }
        $this->getStore()->getManager()->persist($user);
        $this->getStore()->getManager()->flush();

        return $this->show($this->lang('editmsg'));
    }

    /**
     * @Route("/user/delete")
     * 
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param \Slim\Route $route
     */
    public function deleteAction($request) {

        $this->hasCsrfProtection();

        $user = $this->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityUser')->find($request->post('id'));
		if($user->getUsername()==$this->getSecurityUser()->get('username')){
			return $this->show('ACCESS DENIED',true,Controller::ERROR);
		}
        $web = \Raptor\Core\Location::get(\Raptor\Core\Location::WEBBUNDLES);
        if (!file_exists($web . '/security_photos'))
            mkdir($web . '/security_photos');
        if ($user->getIcon() != NULL)
            unlink($web . '/security_photos/' . $user->getIcon());
        $this->getStore()->getManager()->remove($user);
        $this->getStore()->getManager()->flush();
        return $this->show($this->lang('deletemsg'));
    }

}

?>
