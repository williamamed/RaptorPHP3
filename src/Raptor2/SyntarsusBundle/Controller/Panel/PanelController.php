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
namespace Raptor2\SyntarsusBundle\Controller\Panel;
use Raptor\Bundle\Annotations\Route;
/**
 * Description of Panel
 *
 * 
 */
class PanelController extends \Raptor\Bundle\Controller\Controller{
    
    /**
     * @Route("/raptor/syntarsus/saml")
     * @param \Slim\Http\Request $request
     */
    public function samlIndexAction($request) {
       $opt=$this->app->getConfigurationLoader()->getOptions();
       $active=$opt['options']['syntarsus']['auth'];
       
       if($active=='saml')
           $active=true;
       else
           $active=false;
       
       $config_directory=glob(\Raptor\Core\Location::get(\Raptor\Core\Location::WEB).'/SSO/config/*');
       $config=array();
       foreach ($config_directory as $value) {
           $route_array=  explode('/', $value);
           $route_array=$route_array[count($route_array)-1];
           $config[]=  $route_array;
       }
       
       $metadata_directory=glob(\Raptor\Core\Location::get(\Raptor\Core\Location::WEB).'/SSO/metadata/*');
       $metadata=array();
       foreach ($metadata_directory as $value) {
           $route_array=  explode('/', $value);
           $route_array=$route_array[count($route_array)-1];
           $metadata[]=  $route_array;
       }
        return $this->render('@SyntarsusBundle/Plugin/saml/index.html.twig',array(
            'samlconfig'=>$active,
            'config'=>$config,
            'metadata'=>$metadata
            
        ));
    }
    
     /**
     * @Route("/raptor/syntarsus/activate")
     * @param \Slim\Http\Request $request
     */
     public function changeStateAction($request) {
        $state = '';
        $base= $request->getUrl().$request->getScriptName().'/..';
        if ($request->get('state') == 'activate') {
            
            $this->app->getConfigurationLoader()->setConfOption(array(
                'syntarsus'=>array('auth'=>'saml')
                ));
            $state = true;
        }
        if ($request->get('state') == 'deactivate') {
            $this->app->getConfigurationLoader()->setConfOption(array(
                'syntarsus'=>array('auth'=>'native')
                ));
            $state = false;
        }
        $this->app->getConfigurationLoader()->writeOptions();
        $this->app->getConfigurationLoader()->forceLoad();

        return $this->render('@SyntarsusBundle/Plugin/saml/edit.html.twig', array(
            'state' => $state,
            'base'=>$base
                ));
    }
    
    /**
     * @Route("/raptor/syntarsus/api")
     * @param \Slim\Http\Request $request
     */
    public function apiAction($request) {
       $api=  $this->app->getApi();

        if(is_array($api) and isset($api['Syntarsus'])){
            $api=$api['Syntarsus'];
        }else{
            $api=array();
        }
        
        return $this->render('@SyntarsusBundle/Plugin/api/index.html.twig',array(
            'api'=>$api
        ));
    }
    
    
}

?>
