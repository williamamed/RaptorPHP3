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

namespace Raptor2\SyntarsusBundle\Manager;

/**
 * SecurityManager handle the Identification-Autentication, 
 * Authorization and Auditories Process
 *
 * 
 */
class SecurityManager extends \Raptor\Security\AbstractSecurityManager implements \Raptor\Bundle\Route\Rule {

    /**
     *
     * @var \Raptor\Raptor
     */
    private $app;

    /**
     * This is the user indentificated in the indentification process
     * @var \Raptor2\SyntarsusBundle\Model\Entity\SecurityUser
     */
    private $user;

    /**
     * the username gived by the user in the form login
     * @var string
     */
    private $username;

    /**
     * the password gived by the user in the form login
     * @var string
     */
    private $password;

    /**
     * Redirect when user authenticate
     * @var boolean
     */
    private $redirect;

    /**
     * This is the error message after any event
     * @var string
     */
    private $errorMessage;
    private $eacl = true;

    /**
     * This is the entry of Security Manager Routing, calling the 4 main process
     * @param \Raptor\Raptor $app
     * @return boolean
     */
    private $template = "@SyntarsusBundle/Login/index.html.twig";

    public function call(\Raptor\Raptor $app) {
        $this->redirect = true;
        $this->app = $app;
        $opt = $this->app->getConfigurationLoader()->getOptions();
        if (isset($opt['options']['syntarsus']) and isset($opt['options']['syntarsus']['auth']) and $opt['options']['syntarsus']['auth'] === 'saml')
            return $this->samlHandler();

        if (!$app->getSecurity()->isAuthenticated()) {
            $app->getLanguage()->setCurrentBundle('\Raptor2\SyntarsusBundle\SyntarsusBundle');
            $this->handleAuthenticationRequest();

            $app->response()->write($app->render($this->template, array(
                        'error' => $this->errorMessage,
                        'username' => $this->app->request()->post('username')
            )));
            $app->contentType('text/html; charset=UTF-8');
            $app->response()->setStatus(401);
            return true;
        } else {
            if ($app->request()->getPathInfo() == '/syntarsus/login') {
                if($app->request()->get('redirect'))
                    $app->redirect(urldecode($app->request()->get('redirect')), 302);
                else{
                    echo 'Redireccion invalida .....';
                    exit();
                }
            } else {
                $evac = new \Raptor2\SyntarsusBundle\Listener\EvacListener();

                $this->app->getStore()->getManager()->getEventManager()->addEventSubscriber($evac);
                return $this->authorization();
            }

            
        }
    }

    public function setRedirect($redirect) {
        $this->redirect = $redirect;
    }

    /**
     * 
     * @param \Raptor\Raptor $app
     */
    public function handleAuthenticationRequest() {
        /**
         * Check first its has a remember me cookie
         * and login with hem
         */
        if ($this->handleSyntarsusCookie()) {
            $this->authentication();
        }
        if ($this->app->request()->isFormData() and ! $this->app->request()->isXhr()) {
            if ($this->app->request()->post('username') and $this->app->request()->post('password')) {
                $this->username = $this->app->request()->post('username');
                $this->password = $this->app->request()->post('password');
                $remember = $this->app->request()->post('remember');

                /**
                 * if not has a remember cookie continue with the login routine process
                 */
                if ($this->indentification()) {
                    $this->setRememberMe($this->username, $this->password, $remember);
                    $this->authentication();
                } else {
                    $this->errorMessage = $this->app->getLanguage()->getBundleLanguage('wrong_user_pass');
                }
            } else {
                $this->errorMessage = $this->app->getLanguage()->getBundleLanguage('wrong_user_pass');
            }
        }
    }

    /**
     * Invoke the indentification process
     * 
     * 
     * @param \Raptor\Raptor $app
     */
    public function indentification() {
        $indenficated = $this->app->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityUser')
                ->findOneBy(array(
            'username' => $this->username,
            'state' => true
        ));
        $this->user = $indenficated;
        return ($indenficated) ? true : false;
    }

    /**
     * Invoke the authentication process
     * 
     * YOU NEED TO LOCK THE ACCESS FOR N ATTEMPS
     * OF LOGIN BY A CERTAIN TIME
     * 
     * TODO register the atemmpt failed of login when
     * the user pass the N Attempts
     * 
     * @return boolean
     */
    public function authentication() {
        $user = $this->user;
        $pass = $this->password;
        if ($user->getAttempts() !== NULL) {
            if ($user->getAttempts() < \Raptor2\SyntarsusBundle\SyntarsusBundle::ATTEMPTS) {
                $user->setAttempts($user->getAttempts() + 1);
            } else {
                $current = new \DateTime();
                if ($user->getLastattempt()->getTimestamp() + \Raptor2\SyntarsusBundle\SyntarsusBundle::LOCKTIME < $current->getTimestamp()) {
                    $user->setAttempts(1);
                } else {
                    $this->errorMessage = $this->app->getLanguage()->getBundleLanguage('lock_user');
                    $name = $user->getUsername();
                    $attempt = \Raptor2\SyntarsusBundle\SyntarsusBundle::ATTEMPTS;
                    $min = \Raptor2\SyntarsusBundle\SyntarsusBundle::LOCKTIME / 60;
                    $this->app->getLog()->alert(
                            "El usuario $name fue bloqueado $min minutos, posible suplantacion de identidad despues de $attempt intentos de login."
                    );
                    return false;
                }
            }
        } else {
            $user->setAttempts(1);
        }
        $user->setLastattempt(new \DateTime());

        $valid = \Raptor\Security\SecureHash::verify($pass, $user->getPassword());

        if ($valid) {
            $user->setAttempts(0);
            $this->app->getStore()->getManager()->persist($user);
            $this->app->getStore()->getManager()->flush();
            $this->app->getSecurity()->setAuthenticated(true);
            $roles = $user->getIdRol();
            $rolesLength = sizeof($roles);
            $roleList = ($rolesLength > 0) ? $roles[0]->getName() : '';
            for ($i = 1; $i < $rolesLength; $i++) {
                $roleList.=',' . $roles[$i]->getName();
            }
            $eidchildren = $this->app->getStore()->getManager()->getRepository('SyntarsusBundle:SecurityEstructure')->getAllChilds($user->getIdEstructure()->getId());
            foreach ($eidchildren as $key => $einstance) {
                $eidchildren[$key] = $einstance->getId();
            }
            $this->app->getSecurity()->setUser(array(
                'username' => $user->getUsername(),
                'rol' => $roleList,
                'eid' => $user->getIdEstructure()->getId(),
                'eidauth' => $eidchildren
            ));
            $this->app->getSession()->regenerateId();
            if ($this->redirect) {
                $this->app->redirect($_SERVER['REQUEST_URI']);
            } else
                return $valid;
        }
        $this->errorMessage = $this->app->getLanguage()->getBundleLanguage('wrong_user_pass');
        $this->app->getStore()->getManager()->persist($user);
        $this->app->getStore()->getManager()->flush();
        return $valid;
    }

    /**
     * Invoke the authorization process
     * @return boolean
     */
    public function authorization() {
        $session = $this->app->getSecurity()->getUser();
        $matchedRoutes = $this->app->router()->getMatchedRoutes($this->app->request()->getMethod(), $this->app->request()->getResourceUri());
        $routes = array();
        foreach ($matchedRoutes as $route) {
            $routes[] = $route->getPattern();
        }
        $privileges = $this->app->getStore()
                ->getManager()
                ->getRepository('SyntarsusBundle:SecurityUser')
                ->checkRoute($session['username'], $routes);
        $current = $this->app->request()->getResourceUri();
        foreach ($privileges as $priv) {
            $aprobedOwn = new \Raptor\Util\ItemList($this->app->getStore()
                            ->getManager()
                            ->getRepository('SyntarsusBundle:SecurityUser')
                            ->getPrivilegesUserPerRoute($session['username'], $priv->getId()));
            $aprobedOther = new \Raptor\Util\ItemList($this->app->getStore()
                            ->getManager()
                            ->getRepository('SyntarsusBundle:SecurityUser')
                            ->getPrivilegesUserAll($session['username'], $priv->getId()));
            $this->app->setViewPlugin('core_library_inside', $this->app->render('@SyntarsusBundle/SecurityManager/plugin/core.js.twig', array(
                        'privileges' => $aprobedOwn,
                        'privilegesOther' => $aprobedOther
            )));
            $params = $this->app->request()->params();
            $log_a = "";

            $log_a = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            /*             * foreach ($params as $key=>$value) {
              $log_a .= "[" . $key . "] => " . $value . "\n";
              } */
            $this->app->getLog()->info(
                    "Accediendo a la ruta $current con parametros:\n$log_a"
            );
            return FALSE;
        }

        $this->app->getLog()->alert(
                "Bloqueando el acceso a la ruta $current por privilegios insuficientes."
        );
        if ($this->app->request()->isAjax()) {
            $listMsg = new \Raptor\Util\ItemList();
            $listMsg->set('msg', 'ACCESO DENEGADO');
            $listMsg->set('cod', \Raptor\Bundle\Controller\Controller::ERROR);
            $listMsg->set('success', true);
            $this->app->response()->write($listMsg->toJson());
            $this->app->contentType(\Raptor\Raptor::JSON);
        } else
            $this->app->response()->write($this->app->render('@SyntarsusBundle/SecurityManager/denied.html.twig'));
        return true;
    }

    /**
     * Register the auditory handler for trace the user actions
     */
    public function auditory() {
        $this->app->getLog()->setEnabled(true);
        $this->app->getLog()->setWriter(new Log\LogWriter($this->app));
        return $this;
    }

    /**
     * Set the rememberme cookie if the user check it
     * @param string $user
     * @param string $pass
     * @param string $remember
     */
    private function setRememberMe($user, $pass, $remember) {
        if ($remember) {
            $token = array('username' => $user, 'password' => $pass);

            $this->app->setCookie("SyntarsusLoginToken", json_encode($token), strtotime('+1 year'), NULL, NULL, NULL, false, true);
        }
    }

    /**
     * Handle the remember me cookie if exist in the user request
     * @param \Raptor2\SyntarsusBundle\Model\Entity\SecurityUser $user
     * @param string $passwordCookie
     * @return boolean
     */
    private function handleSyntarsusCookie() {

        $cookie = $this->app->getCookie("SyntarsusLoginToken", true, true);

        if ($cookie !== NULL) {
            $token = json_decode($cookie, true);
            $indenficated = $this->app->getStore()
                    ->getManager()
                    ->getRepository('SyntarsusBundle:SecurityUser')
                    ->findOneBy(array(
                'username' => $token['username'],
                'state' => true
            ));
            $this->user = $indenficated;
            $this->password = $token['password'];

            return ($indenficated) ? true : false;
        }


        return false;
    }

    /**
     * Handle the identification process by SAML
     * @return boolean
     */
    public function samlHandler() {

        if (!$this->app->getSecurity()->isAuthenticated()) {
            $options = $this->app->getConfigurationLoader()->getOptions();
            $secret = '';
            if (isset($options['raptor']) and isset($options['raptor']['secret']))
                $secret = $options['raptor']['secret'];
            $new = session_id();
            $this->app->getSession()->set('rpt_csrf', md5($secret . $new));
            $token = $this->app->getSecurity()->getToken();
            $id = session_id();
            $this->app->redirect($this->app->request()->getRootUri() . '/../SSO/index.php?tk=' . $token . '&ref=' . $id . '&samlroute=' . $this->app->request()->getResourceUri());
        }else {

            return $this->authorization();
        }
    }

    /**
     * Set the app for this security manager
     * @param \Raptor\Raptor $app
     */
    public function setApp($app) {
        $this->app = $app;
        return $this;
    }

    /**
     * Mark the user session has authenticated
     */
    public function login() {
        $this->app->getSecurity()->login();
        return $this;
    }

    /**
     * Mark the user session has non-authenticated
     */
    public function logout() {
        $this->app->getSecurity()->logout();
        $this->app->deleteCookie("SyntarsusLoginToken");
        return $this;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function Evac($id) {
        if (!$this->eacl)
            return true;
        if (!$this->app->getSecurity()->isAuthenticated())
            return true;
        $eids = $this->app->getSecurity()->getUser();
        //Evaluar si hacer la llamada directamente o trabajar con la sesion
        if (array_search($id, $eids['eidauth']) === FALSE && $eids['eid'] != $id) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function EvacFlow($id) {
        if (!$this->eacl)
            return $id;
        if (!$this->app->getSecurity()->isAuthenticated())
            return $id;
        if ($this->Evac($id))
            return $id;
        else
            throw new \Raptor2\SyntarsusBundle\Exception\EvacException('Una regla de control vertical de acceso a estructuras previene el acceso a esta área, esto significa que está usando un usuario sin permisos para acceder a una estructura superior o llanando una entidad fuera del contexto de la estructura del usuario.');
    }

    public function freeEvac() {
        $this->eacl = false;
        return $this;
    }

    public function activeEvac() {
        $this->eacl = true;
        return $this;
    }

    public function setLoginTemplate($template) {
        $this->template = $template;
        return $this;
    }

}

?>
