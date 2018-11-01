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
 * Description of Panel
 *
 * 
 */
class SamlLoginResponse implements \Raptor\Bundle\Route\Rule{
    
    public function call(\Raptor\Raptor $app) {
        
        
        $username=$app->request()->get('username');
        $pass=$app->request()->get('password');
        $securitymanager=$app->getSecurity()->getManager();
        $resp=new \Raptor\Util\ItemList();
        $app->getLanguage()->setCurrentBundle('\Raptor2\SyntarsusBundle\SyntarsusBundle');
        $securitymanager->setUsername($username);
        $securitymanager->setPassword($pass);
        $securitymanager->setRedirect(false);
        
        if($securitymanager->indentification()){
            $resp->set('login',$securitymanager->authentication());
            $resp->set('attr',  $app->getSecurity()->getUser());
        }else{
            $resp->set('login',false);
        }
        
        $app->contentType(\Raptor\Raptor::JSON);
        $app->response()->write($resp->toJson(), true);
        session_destroy();
        session_write_close();
        return true;
    }    
}

?>
