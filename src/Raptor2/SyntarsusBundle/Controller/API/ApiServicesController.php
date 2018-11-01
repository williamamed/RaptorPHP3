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
namespace Raptor2\SyntarsusBundle\Controller\API;
use Raptor\Bundle\Annotations\Api;
/**
 * This class is only to describe the Services functioning<br>
 * To use this methods please call $this->service()->getPrivate('SyntarsusBundle')->[the method name]
 * 
 */
abstract class ApiServicesController extends \Raptor\Bundle\Controller\Controller{
    
    /**
     * <h4>changeUserPassword($username, $old, $new, $msg)</h4><br>
     * 
     * Internal Services to update the user password.<br>
     * The new password must have lenght mayor than 7, especial characters and Uppercase letters otherwhise will
     * be market has invalid
     *  
     * <b>This function expect four parameters:</b><br>
     * 
     * <b>username</b> (the username to change the password)<br>
     * <b>old</b> (the current username password to change)<br>
     * <b>new</b> (the new username password to set)<br>
     * <b>&msg</b> (this is a reference var to complete by this method with a message if something goes wrong)<br>
     * 
     * <b>Return</b> Boolean True if the password was changed correctly, false otherwhise
     * 
     * <b>This function can be call it in any controller like this:</b>
     * $this->service()->getPrivate('SyntarsusBundle')->changeUserPassword($username, $old, $new, $msg);
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()->getPrivate('SyntarsusBundle')->changeUserPassword('miranda','Miranda123','M123TypingNewPass45TY', $msg);
     * //Checking if any error in the operation
     * if(!$result)
     *  return $msg;
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     */
    abstract public function changeUserPassword($username, $old, $new, &$msg = '');
    
    /**
     * <h4>getUserMenu()</h4><br>
     * 
     * This method return the user menu corresponding to the authenticated user.
     * You must note that the user menu is builded by the privilege manager in the same way that is
     * rendered there, this method return only the privileges tree that is authorized
     * to see by this user.
     * 
     * <b>Return</b> Array An array tree that represent the user menu, is returned with the privileges attributes.
     * 
     * <b>The Array Item structure:</b>
     * <b>-className</b> <i>This storage the css class that was set to this privilege</i>
     * <b>-name</b> <i>This storage the name of the privilege</i>
     * <b>-type</b> <i>The type determine if this privilege is a grouping item or a concrete privilege</i>
     * <b>-route</b> <i>if the type of this privilege is not a grouping this contain the access route to this privilege</i>
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()->getPrivate('SyntarsusBundle')->getUserMenu();
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     */
    abstract public function getUserMenu();
    
    /**
     * <h4>listAllUserStructure()</h4><br>
     * 
     * Return all the structure of the authenticated user, meaning that the returned tree is the asigned to the user
     * 
     * <b>Return</b> Array An array tree that represent the user structure
     * 
     * <b>The Array Item structure:</b>
     * <b>-description</b> <i>A description of this structure</i>
     * <b>-name</b> <i>This storage the name of the structure</i>
     * <b>-category</b> <i>This is the category name of this structure</i>
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()->getPrivate('SyntarsusBundle')->listAllUserStructure();
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     */
    abstract public function listAllUserStructure();
    
     /**
     * <h4>getStructureAllChilds($id)</h4><br>
     * 
     * Return all the structure childs recursively
     * 
     * <b>Return</b> Array An array with all structure childs
     * 
     * <b>The Array Item structure:</b>
     * 
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()->getPrivate('SyntarsusBundle')->getStructureAllChilds(5);
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     */
    abstract public function getStructureAllChilds($id);
    
    /**
     * <h4>listUserStructureByDemand($id=0)</h4><br>
     * 
     * Return the structure by demand of the authenticated user, meaning that is returned only the requested node.
     * If the requested node is the 0 then this method return the base structure for this user 
     * 
     * <b>Return</b> Array An array of the structure childs
     * 
     * <b>The Array Item structure:</b>
     * <b>-description</b> <i>A description of this structure</i>
     * <b>-name</b> <i>This storage the name of the structure</i>
     * <b>-category</b> <i>This is the category name of this structure</i>
     * 
     * <b>Example</b>
     * 
     * //This return the base structure for the authenticated user
     * $result=$this->service()->getPrivate('SyntarsusBundle')->listUserStructureByDemand();
     * 
     * //OR return the childs node of an structure
     * $result=$this->service()->getPrivate('SyntarsusBundle')->listUserStructureByDemand(5);
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     */
    abstract public function listUserStructureByDemand($id=0);
    
    /**
     * <h4>registerUser($userFullName, $username, $email, $password, $rolname, $structurename, &$msg)</h4><br>
     * 
     * Register a new User in the system with a Rol and a structure,
     * If an error ocurred a message is placed in parameter $msg
     * [Be aware that after the register you need to activate the user calling the changeStateUser or in the User Manager]<br>
     *
     * <b>This function expect seven parameters:</b><br>
     * 
     * <b>$userFullName</b> (the user full name to add)<br>
     * <b>$username</b> (the username to add)<br>
     * <b>$email</b> (the user email)<br>
     * <b>$password</b> (the new username password to set)<br>
     * <b>array $rolnames</b> (the rol names to this user [the rolname must exist])<br>
     * <b>$structurename</b> (the structure that will belongs this user [the structure must exist])<br>
     * <b>&msg</b> (this is a reference var to complete by this method with a message if something goes wrong)<br>
     * 
     * <b>Return</b> Boolean True if the user was added correctly, false otherwhise
     * 
     * <b>This function can be call it in any controller like this:</b>
     * $this->service()
     * ->getPrivate('SyntarsusBundle')
     * ->registerUser($userFullName, $username, $email, $password, $rolnames, $structurename, $msg);
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()
     * ->getPrivate('SyntarsusBundle')
     * ->registerUser('Miranda Aguilera','miranda','miranda@gmail.com','M123TypingNewPass45TY','admin','Raptor2', &$msg);
     * 
     * //Checking if any error in the operation
     * if(!$result)
     *  return $msg;
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     * 
     */
    abstract public function registerUser($userFullName,$username,$email,$password,array $rolnames,$structurename,&$msg);
    
    /**
     * <h4>changeStateUser($username, $state, &$msg)</h4><br>
     * 
     * This change the state of the user, TRUE to active state and FALSE to inactive state.
     * If an error ocurred a message is placed in parameter $msg
     *  
     * <b>This function expect 3 parameters:</b><br>
     * 
     * <b>username</b> (the username to change the state)<br>
     * <b>state</b> (the state to be set)<br>
     * <b>&msg</b> (this is a reference var to complete by this method with a message if something goes wrong)<br>
     * 
     * <b>Return</b> Boolean True if the state was changed correctly, false otherwhise
     * 
     * <b>This function can be call it in any controller like this:</b>
     * $this->service()->getPrivate('SyntarsusBundle')->changeStateUser($username, $state, $msg);
     * 
     * <b>Example</b>
     * 
     * $result=$this->service()->getPrivate('SyntarsusBundle')->changeStateUser('miranda', true, $msg);
     * //Checking if any error in the operation
     * if(!$result)
     *  return $msg;
     * 
     * @Api(name="Security Module",category="Syntarsus",version="2.0.1")
     * 
     */
    abstract public function changeStateUser($username,$state,&$msg);
    
}

?>
