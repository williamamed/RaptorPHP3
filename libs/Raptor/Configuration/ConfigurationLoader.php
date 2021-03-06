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

namespace Raptor\Configuration;

use Raptor\Core\Location;

/**
 * 
 * El cargador de configuracion maneja toda la configuracion global de Raptor,
 * definicion de rutas, bundles registrados, locaciones absolutas de los bundles
 * y definiciones api etc...
 * 
 */
class ConfigurationLoader {

    protected $options;

    /**
     *
     * @var Raptor\Cache\Cache
     */
    private $cache;

    /**
     *
     * @var \Raptor\Bundle\Reader
     */
    private $reader;
    private $monitor;

    /**
     *
     * @var \Raptor\Cache\Cache
     */
    private $cacheautoinstaller;

    /**
     *
     * @var array 
     */
    private $autoinstaller;

    function __construct() {
        
        $this->options = array();
        $this->monitor = new Monitor();
        $this->cache = new \Raptor\Cache\Cache('system');
        $this->cacheautoinstaller = new \Raptor\Cache\Cache('autoinstall');
        $this->autoinstaller = array();
        $this->reader = new \Raptor\Bundle\Reader();
        $this->read();
        
    }

    private function read() {
        if (!$this->cacheautoinstaller->isDirty()) {
            $this->autoinstaller = $this->cacheautoinstaller->getData();
        }
        
        if ($this->cache->isDirty()) {
            $app = Location::get(Location::APP);
            $this->monitor->execute();
            /**
             * INICIO Revision anterior Raptor 2
             */
            //$this->options['bundles'] = json_decode(file_get_contents($app . '/conf/components.json'), true);
            //$this->checkForGhosts();
            /**
             * FIN Revision anterior
             */
            $this->readManifestFiles();
            $this->options['options'] = json_decode(file_get_contents($app . '/conf/options.json'), true);
            
            //$this->options['options'] = \Raptor\Yaml\Yaml::parse($app . '/conf/options.yml');
            //$this->options['bundles'] = \Raptor\Yaml\Yaml::parse($app . '/conf/bundles.yml');
            
            /**
             * Add the system routes and the bundles
             * check fot enviroment
             * if(development)
             */
            //$this->options['bundles'] = array_merge(\Raptor\Yaml\Yaml::parse(__DIR__ . '/../Component/bundles.yml'), $this->options['bundles']);
            //$this->options['bundles'][]="\\Raptor\\Component\\systemBundle\\systemBundle";
            /**
             * Must call this before the Reader
             */
            $cache = \Raptor\Core\Location::get(\Raptor\Core\Location::CACHE);
         
//           \Raptor\Raptor::getInstance()->getAppAspectKernel()->resetContainer();
            $aop = Location::get(Location::CACHE) . '/AOP';
            \Raptor\Util\Files::delete($aop);
            \Raptor\Util\Files::delete($cache . '/7u136');
            /**
             * Aspect Kernel antes del lector de metadatos
             */
            $appr=\Raptor\Raptor::getInstance();
            
            $appr->getAppAspectKernel()->init(array(
                'debug' => $appr->config('debug'),
                'appDir' => Location::get(Location::SRC),
                'cacheDir' => Location::get(Location::CACHE) . '/AOP'
            ));
            $container = \Raptor\Raptor::getInstance()->getAppAspectKernel()->getContainer();
            
            foreach ($this->options['bundles'] as $bundle) {
                $cmp_str = $bundle;
                $cmp = new $cmp_str();
                call_user_func_array(array($cmp, 'init'), array());
                
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $refClass = new \ReflectionObject($cmp);

                $container->addResource($trace[1]['file']);
                $container->addResource($refClass->getFileName());
            }

            $this->reader->setBundles($this->options['bundles']);
            $this->reader->load();
            $this->options['routes'] = $this->reader->getDefinitions();
            $this->options['location'] = $this->reader->getLocation();
            //$this->options['specifications'] = $this->reader->getSpecifications();
            $this->options['description'] = $this->reader->getDescriptions();
            
            $this->options['rules'] = $this->reader->getRules();
            $this->cache->setData($this->options);
            $this->cache->save();
            
            /**
             * Save the API to access in the main Raptor class
             */
            $api = new \Raptor\Cache\Cache('api');
            $api->setData($this->reader->getApi());
            $api->save();
            /**
             * Save the Auto Install Cache to know the trace of installed bundled
             */
            $this->cacheautoinstaller->setData($this->autoinstaller);
            $this->cacheautoinstaller->save();
            
        } else {
            
            $this->options = $this->cache->getData();
            $app=\Raptor\Raptor::getInstance();
            $app->getAppAspectKernel()->init(array(
                'debug' => $app->config('debug'),
                'appDir' => Location::get(Location::SRC),
                'cacheDir' => Location::get(Location::CACHE) . '/AOP'
            ));
            $container = \Raptor\Raptor::getInstance()->getAppAspectKernel()->getContainer();
            foreach ($this->options['bundles'] as $bundle) {
                $cmp_str = $bundle;
                $cmp = new $cmp_str();
                call_user_func_array(array($cmp, 'init'), array());
                
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $refClass = new \ReflectionObject($cmp);

                $container->addResource($trace[1]['file']);
                $container->addResource($refClass->getFileName());
            }
            
        }
    }

    private function decodeFunctions($text) {

        $rigth = strstr($text, '"hash(');
        if ($rigth !== FALSE) {
            $left = strstr($rigth, ')"', true);
            $change = $left . ')"';
            $hash = str_replace('"hash(', '', $left);
            // ... Detect and change the text, pass after to the recursive function

            $passed = str_replace($change, "{mode: hash,password: $hash}", $text);
            return $this->decodeFunctions($passed);
        }
        return $text;
    }

    /**
     * [USO DEL SISTEMA]
     * 
     * ei. "hash($TH$63.YHHSKKSK*&SJHJS&%JHD.sIIMNs)" = {mode: hash, password: $TH$63.YHHSKKSK*&SJHJS&%JHD.sIIMNs}
     * @param string $definition
     * @return \stdClass
     */
    static public function getHash($definition) {

        $rigth = strstr($definition, 'hash(');

        $std = new \stdClass();
        $std->valid = false;
        $std->password = $definition;
        if ($rigth !== FALSE) {
            $left = strstr($rigth, ')', true);

            $hash = str_replace('hash(', '', $left);
            // ... Detect and change the text, pass after to the recursive function
            $std->mode = 'hash';
            $std->password = $hash;
            $std->valid = true;
        }

        return $std;
    }

    /**
     * Retorna toda la configuracion en un array
     * 
     * ['options'=>
     *      ['database'=>...,'raptor'=>...],
     *  'bundles'=>
     *      ['\exmples\ejemploBundle']
     *  'routes'=>[
     *      ['route_name'=>['/path','Bundle\ClassController','MethodToCall']],
     *  'location'=>
     *      ['bundleName'=>'src/exampleBundle'],
     *  'specifications'=>
     *      ['bundleName'=>
     *          ['location'=>'src/exampleBundle',
     *          'namespace'=>'example\exampleBundle',
     *          'name'=>'example\exampleBundle\exampleBundle']
     *  ]
     * @return Array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * 
     * Setea las opciones de configuracion para el archivo option.yml
     * necesitas ejecutar flush luego de esta accion
     * @param Array $array
     */
    public function setConfOption($array) {
        if (is_array($array)) {
            $this->options['options'] = array_replace_recursive($this->options['options'], $array);
        }
    }

    /**
     * 
     * Retorna la configuracion de options.yml en un array
     * 
     * @return Array
     */
    public function getConfOption() {
        $count=  func_num_args();
        if($count==0)
            return $this->options['options'];
        $args= func_get_args();
        $value=$this->options['options'];
        $counter=0;
        foreach ($args as $arg) {
            if(is_string($arg) && isset($value[$arg])){
                $value=$value[$arg];
                $counter++;
            }
        }
        if($counter==$count)
            return $value;
        else
            return false;
    }

    /**
     * Retorna todas las rutas registradas
     * ['route_name'=>['/path','Bundle\ClassController','MethodToCall']]
     * 
     * @return Array
     */
    public function getRoutes() {
        return $this->options['routes'];
    }

    /**
     * Retorna todas las descripciones de rutas
     * ['/path'=>['This route is for doing something']]
     * 
     * @return Array
     */
    public function getRoutesDescriptions() {
        return $this->options['description'];
    }

    /**
     * Retorna todas las locaciones de los bundles
     * ['bundleName'=>'src/exampleBundle']
     * 
     * @return Array
     */
    public function getBundlesLocation() {
        return $this->options['location'];
    }

    /**
     * Retornas los bundles registrados
     * ['\example\exampleBundle\exampleBundle']
     * 
     * @return Array
     */
    public function getBundles() {
        return $this->options['bundles'];
    }

    /**
     * Retorna las especificaciones de los bundles
     * 
     *      ['bundleName'
     *          =>['location'=>'src/exampleBundle',
     *              'namespace'=>'example\exampleBundle',
     *              'name'=>'example\exampleBundle\exampleBundle']
     *       ]
     * 
     * @return Array
     */
    public function getBundlesSpecifications() {
        return $this->options['specifications'];
    }

    /**
     * 
     * Fuerza al cargador a re-leer la configuracion
     * 
     */
    public function forceLoad() {
       
       
            $this->cache->setDirty();
            $this->cache->save();
       
        //$this->read();
    }

    /**
     * Escirbe en el archivo de configuracion
     */
    public function writeOptions() {

        $app = Location::get(Location::APP);

        $real = $this->getConfOption();
            
        //$ymlParam = \Raptor\Yaml\Yaml::dump($real);
        file_put_contents($app . '/conf/options.json', json_encode($real, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }

    /**
     * Añade un bundle al archivo de registro de bundles, esta funcionalidad sera removida
     * el archivo de bundles ha desaparecido a partir de la version 3.0.1
     * 
     * @deprecated desde version 3.0.1
     * @param array|string $bundles
     */
    public function registerBundle($bundles) {
        throw new \Exception("EL archivo de registro de bundles fue removido para esta version, el proceso se realiza de forma interna y automatica");
        $app = Location::get(Location::APP);
        $real_bundles = json_decode(file_get_contents($app . '/conf/components.json'), true);
        
        if (is_array($bundles)) {
            $real_bundles = array_merge($real_bundles, $bundles);
        } else {
            $real_bundles[] = $bundles;
        }
        
        file_put_contents($app . '/conf/components.json', json_encode($real_bundles, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        
    }

    /**
     * 
     * Remueve un bundle del archivo de registro de bundles, esta funcionalidad sera removida
     * el archivo de bundles ha desaparecido a partir de la version 3.0.1
     * 
     * @deprecated desde version 3.0.1
     * @param string $bundle
     */
    public function unRegisterBundle($bundle) {
        throw new \Exception("EL archivo de registro de bundles fue removido para esta version, el proceso se realiza de forma interna y automatica");
        $app = Location::get(Location::APP);
        $real_bundles = json_decode(file_get_contents($app . '/conf/components.json'), true);
        //$real_bundles = \Raptor\Yaml\Yaml::parse($app . '/conf/bundles.yml');
        $copy = $bundle;
        if ($copy[0] != '\\')
            $copy = '\\' . $copy;

        foreach ($real_bundles as $key => $value) {
            if ($value == $copy) {
                unset($real_bundles[$key]);
            }
        }
        file_put_contents($app . '/conf/components.json', json_encode($real_bundles, JSON_PRETTY_PRINT));
        //$ymlParam = \Raptor\Yaml\Yaml::dump($real_bundles);
        //file_put_contents($app . '/conf/bundles.yml', $ymlParam);
    }

    /**
     * Devuelve la instancia de la cache system
     * @return \Raptor\Cache\Cache
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * 
     * Devuelve un array con los mensajes correspondientes a los ultimos bundles que realizaron
     * operaciones de autoinstalacion
     * @return array
     */
    public function getAutoInstallMessage() {
        $msg = $this->autoinstaller;
        $this->autoinstaller = array();
        $this->cacheautoinstaller->setData(array());
        $this->cacheautoinstaller->save();
        return $msg;
    }

    private function checkForGhosts() {
        $app = \Raptor\Core\Location::get(\Raptor\Core\Location::APP);
        $src = \Raptor\Core\Location::get(\Raptor\Core\Location::SRC);
        $installed = $this->options['bundles'];
        //$installed = $this->options['bundles'] = \Raptor\Yaml\Yaml::parse($app . '/conf/bundles.yml');
        $detected = $this->monitor->getDetection();
        
        /**
         * Ghost detection
         */
        foreach ($installed as $bundle) {
            $detect = false;
            foreach ($detected as $value) {
                if ($bundle === $value) {
                    $detect = true;
                    break;
                }
            }
            if ($detect == false) {
                $this->autoinstaller[] = "<h3>Un componente fantasma <b>($bundle)</b> fue detectado y removido !!</h3>";
                $this->unRegisterBundle($bundle);
            }
        }
        /**
         * AutoBundle-Installer
         */
        foreach ($detected as $bundle) {
            $detect = false;
            foreach ($installed as $value) {
                if ($bundle === $value) {
                    $detect = true;
                    break;
                }
            }
            if ($detect == false) {
                $bundleRoute = $src . '' . str_replace('\\', '/', $bundle) . '';
                $div = explode('/', $bundleRoute);
                unset($div[count($div) - 1]);
                $bundleRoute = join('/', $div);
                //$ruta = $bundleRoute . '/Manifiest/install.json';
                $resultManifest=\Raptor\Util\Files::find($bundleRoute,'install.json');
                
                if ($resultManifest && file_exists($resultManifest[0])) {
                    $ruta=$resultManifest[0];
                    $meta = json_decode(utf8_encode(file_get_contents($ruta)), true);

                    if (!isset($meta['installed']) or (isset($meta['installed']) and $meta['installed'] == 0)) {

                        $meta['installed'] = 1;
                        if (isset($meta['installScript']) and file_exists($bundleRoute . $meta['installScript'])) {
                            $this->callbackInstall($bundleRoute . $meta['installScript']);
                        }
                        file_put_contents($ruta, json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
                        $this->registerBundle($bundle);
                        $this->autoinstaller[] = "<h3>Un nuevo componente <b>$bundle</b> fue detectado e instalado !!</h3>";
                    }
                }
            }
        }
    }

    /**
     * This callback prevent that the include installer script
     * inject malicius code in the functioning
     * @param type $param
     */
    private function callbackInstall($file) {
        include $file;
    }
    
    private function readManifestFiles(){
        $appLocation = \Raptor\Core\Location::get(\Raptor\Core\Location::APP);
        $src = \Raptor\Core\Location::get(\Raptor\Core\Location::SRC);
        
        $resultManifest=\Raptor\Util\Files::find($src,'install.json');
        $resultManifest[]=__DIR__.'/../Component/systemBundle/Manifest/install.json';
        $this->options['bundles']=array();
        $this->options['specifications']=array();
        //$this->options['bundles'][]="\\Raptor\\Component\\systemBundle\\systemBundle";
        // Busca componentes
        foreach ($resultManifest as $manifest) {
            $meta = json_decode(utf8_encode(file_get_contents($manifest)), true);
            if(isset($meta['version']) and isset($meta['namespace'])){
                $path=  explode('.', $meta['namespace']);
                if(file_exists($src.'/'.  join('/', $path).'.php') || file_exists(__DIR__.'/../../'.  join('/', $path).'.php')){
                    
                    $name="\\".  join("\\", $path);
                    $class = new \Wingu\OctopusCore\Reflection\ReflectionClass($name);
                    $require=array();
                    if(isset($meta['require'])){
                        $require=$meta['require'];
                    }
                    
                    $this->options['specifications'][$class->getShortName()] = array(
                        'location' => \Raptor\Util\ClassLocation::getLocation($name), 
                        'namespace' => $class->getNamespaceName(),
                        'name' => $class->getName(),
                        'version' => $meta['version'],
                        'packagename' => str_replace('Bundle','', $class->getShortName()),
                        'require'=> $require,
                        'nameinbundles'=> $name
                    );
                    
                    $this->options['bundles'][$name]=$name;
                }
            }
        }
        // Verificacion de requisitos componentes
        $notReady=array();
        foreach ($this->options['specifications'] as $name=>$bundle) {
            foreach ($bundle['require'] as $depend => $valueVersion) {
                if(isset($this->options['specifications'][$depend.'Bundle'])){
                    $version=  explode('@', $valueVersion);
                    if(count($version)==2){
                        if(version_compare($this->options['specifications'][$depend.'Bundle']['version'], $version[1], $version[0])!=1){
                            $notReady[]=array('name'=>$name,'nameinbundles'=>$bundle['nameinbundles']);
                        }
                    }else{
                        if(version_compare($this->options['specifications'][$depend.'Bundle']['version'], $version[0])!=1){
                            $notReady[]=array('name'=>$name,'nameinbundles'=>$bundle['nameinbundles']);
                        }    
                    }
                }else{
                    $notReady[]=array('name'=>$name,'nameinbundles'=>$bundle['nameinbundles']);
                }
            }
        }
        // Elimina los componentes que no cumplen el require
        
        foreach ($notReady as $bundle) {
            unset($this->options['specifications'][$bundle['name']]);
            unset($this->options['bundles'][$bundle['nameinbundles']]);
        }
        
    }

}

?>
