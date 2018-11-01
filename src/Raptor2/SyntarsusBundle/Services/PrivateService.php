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

namespace Raptor2\SyntarsusBundle\Services;

use Raptor\Util\ItemList;
use Raptor\Security\SecureHash;

/**
 * Description of PrivateService
 *
 * 
 */
class PrivateService extends \Raptor2\SyntarsusBundle\Controller\API\ApiServicesController {

    public function changeStateUser($username, $state, &$msg) {
        $users = $this->app->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityUser')
                        ->findOneBy(array('username' => $username));

        if (!$users) {
            $msg = "The username $username does not exist";
            return false;
        }

        $user = $users;
        $user->setState($state);
        $this->app->getStore()->getManager()->persist($user);
        $this->app->getStore()->getManager()->flush();
        return true;
    }

    public function changeUserPassword($username, $old, $new, &$msg = '') {

        $user = $this->app->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityUser')
                ->findOneBy(array('username' => $username));
        if ($user) {
            $theUser = $user;
            $valid = SecureHash::verify($old, $theUser->getPassword());
            if ($valid) {
                if ($this->validatePassword($new)) {
                    $theUser->setPassword(SecureHash::hash($new));
                    $this->app->getStore()->getManager()->persist($theUser);
                    $this->app->getStore()->getManager()->flush();
                    return TRUE;
                } else {
                    $msg = $this->lang('passstrong', '\Raptor2\SyntarsusBundle\SyntarsusBundle');
                    return FALSE;
                }
            } else {
                $msg = $this->lang('beforepass', '\Raptor2\SyntarsusBundle\SyntarsusBundle');
                return FALSE;
            }
        } else {
            $msg = $this->lang('userexist', '\Raptor2\SyntarsusBundle\SyntarsusBundle');
            return FALSE;
        }
    }

    public function getUserMenu() {
        if (!$this->app->getSecurity()->isAuthenticated())
            return array();
        if (!$this->getSecurityUser()->get('rol'))
            return array();
        $roles = explode(',', $this->getSecurityUser()->get('rol'));
        if (!$roles)
            return array();
        $privileges = $this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityUser')
                ->findPrivilegesByRol($this->getSecurityUser()->get('username'), $roles);

        $ownPrivilege = new ItemList($privileges);
        $ownPrivilege->toArray(function(&$item) {
            if ($item['idRol'])
                $item['idRol'] = '';
        });

        $listPrivilege = new \Raptor\Util\ItemList();

        foreach ($ownPrivilege as $value) {
            if ($value['belongs'] == 0) {
                $childs = $this->findChildsMenu($value, $ownPrivilege->getArray());
                if (count($childs) > 0) {
                    $value['children'] = $childs;
                }
                if ($value['type'] == \Raptor2\SyntarsusBundle\Controller\PrivilegeController::DIR || $value['type'] == \Raptor2\SyntarsusBundle\Controller\PrivilegeController::INDEX)
                    $listPrivilege->add($value);
            }
        }
        return $listPrivilege->toArray();
    }

    public function listAllUserStructure() {
        if (!$this->app->getSecurity()->isAuthenticated())
            return array();

        $result = $this->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityUser')
                ->findOneBy(array('username' => $this->getSecurityUser()->get('username')));

        if ($result) {
            $userEntity = $result;
            $estructure = $userEntity->getIdEstructure();
            $resulting = new ItemList();
            if ($estructure) {
                $allEstructure = new ItemList($this->app->getStore()
                                ->getManager()
                                ->getRepository('SyntarsusBundle:SecurityEstructure')
                                ->findAll());
                $allEstructure->toArray();
                $parentEstruct = new ItemList();
                $parentEstruct->add($estructure);
                $parentEstruct->toArray(function(&$item) {
                    if ($item['idCategory']) {
                        $item['category'] = $item['idCategory']->getName();
                        $item['idCategory'] = $item['idCategory']->getId();
                    }
                });
                $finalarray = $parentEstruct->getArray();
                $this->estructureChild($finalarray[0], $allEstructure->getArray(), $resulting);

                if ($resulting->size() > 0)
                    $finalarray[0]['children'] = $resulting->getArray();
                return $finalarray;
            }
        }
        return array();
    }

    public function listUserStructureByDemand($id = 0) {
        if (!$this->app->getSecurity()->isAuthenticated())
            return array();
        if ($id == 0) {

            $user = $this->getStore()
                    ->getManager()
                    ->getRepository('SyntarsusBundle:SecurityUser')
                    ->findOneBy(array('username' => $this->getSecurityUser()->get('username')));
            if (!$user)
                return array();
            $node = $user->getIdEstructure();
            $list = new ItemList();

            $list->add($node);
            return $list->toArray(function(&$item) {
                        if ($item['idCategory']) {
                            $item['category'] = $item['idCategory']->getName();
                            $item['idCategory'] = $item['idCategory']->getId();
                        }
                    });
        } else {
            $node = $id;
            $list = new ItemList($this->app->getStore()
                            ->getManager()
                            ->getRepository('SyntarsusBundle:SecurityEstructure')
                            ->findBy(array('belongs' => $node)));
            return $list->toArray(function(&$item) {
                        if ($item['idCategory']) {
                            $item['category'] = $item['idCategory']->getName();
                            $item['idCategory'] = $item['idCategory']->getId();
                        }
                    });
        }
    }

    public function registerUser($userFullName, $username, $email, $password, array $rolnames, $estructurename, &$msg) {
        if ($rolnames != null && sizeof($rolnames) > 0) {
            $roles = $this->app->getStore()
                    ->getManager()
                    ->getRepository('SyntarsusBundle:SecurityRol')
                    ->findBy(array('name' => $rolnames));
            if (!$roles || sizeof($roles) < sizeof($rolnames)) {
                $msg = "There are " . (sizeof($rolnames) - sizeof($roles)) . " rolnames that dosn't exist";
                return false;
            }
        }
        $users = new ItemList($this->app->getStore()
                        ->getManager()
                        ->getRepository('SyntarsusBundle:SecurityUser')
                        ->findBy(array('username' => $username)));

        $estructure = $this->app->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityEstructure')
                ->findOneBy(array('name' => $estructurename));


        if ($users->size() > 0) {
            $msg = "<div style='max-width:300px'>El usuario <b>$username</b> no se encuentra disponible, por favor utilice otro nombre de usuario</div>";
            return false;
        }
        if (!$estructure) {
            $msg = "The estructure $estructurename not exist";
            return false;
        }


        $user = new \Raptor2\SyntarsusBundle\Model\Entity\SecurityUser();
        $user->setFullname($userFullName);
        $user->setUsername($username);
        $user->setEmail($email);
        if ($roles != null && sizeof($roles) > 0) {
            foreach ($roles as $rolename) {
                $user->addSecurityRol($rolename);
            }
        }
        $user->setIdEstructure($estructure);
        if (!$this->validatePassword($password)) {
            $msg = $this->lang('passstrong', '\Raptor2\SyntarsusBundle\SyntarsusBundle');
            return false;
        }
        $user->setPassword(SecureHash::hash($password));

        $this->app->getStore()->getManager()->persist($user);
        $this->app->getStore()->getManager()->flush();
        return true;
    }

    private function validatePassword($pass) {
        $alfa = '!@#$%^&*()_+~:{}[];><?,.';
        $num = '1234567890';
        if (strlen($pass) < 7)
            return FALSE;
        $numbers = false;
        for ($index = 0; $index < strlen($num); $index++) {
            for ($index1 = 0; $index1 < strlen($pass); $index1++) {
                if ($num[$index] === $pass[$index1])
                    $numbers = TRUE;
            }
        }
        if ($numbers == TRUE)
            for ($index = 0; $index < strlen($alfa); $index++) {
                for ($index1 = 0; $index1 < strlen($pass); $index1++) {
                    if ($alfa[$index] === $pass[$index1])
                        return TRUE;
                }
            }
        return FALSE;
    }

    private function findChildsMenu($parent, $all) {
        $childs = array();
        foreach ($all as $value) {
            if ($parent['id'] === $value['belongs']) {
                if ($value['type'] == \Raptor2\SyntarsusBundle\Controller\PrivilegeController::DIR || $value['type'] == \Raptor2\SyntarsusBundle\Controller\PrivilegeController::INDEX) {

                    $childs[] = $value;
                    $my = $this->findChildsMenu($value, $all);
                    $countChilds = count($childs) - 1;

                    if (count($my) > 0) {
                        $childs[$countChilds]['children'] = $my;
                    }
                }
            }
        }
        return $childs;
    }

    private function estructureChild($estructure, $all, &$list) {
        foreach ($all as $value) {
            if ($value['belongs'] == $estructure['id']) {
                $my = new ItemList();
                $this->estructureChild($value, $all, $my);
                if ($my->size() > 0)
                    $value['children'] = $my->getArray();
                if ($value['idCategory']) {
                    $value['category'] = $value['idCategory']->getName();
                    $value['idCategory'] = $value['idCategory']->getId();
                }


                $list->add($value);
            }
        }
    }

    public function getStructureAllChilds($id) {
        return $this->getStoreManager()->getRepository('SyntarsusBundle:SecurityEstructure')->getAllChilds($id);
    }

}

?>
