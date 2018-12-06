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

namespace Raptor;
/**
 * Raptor es la clase principal del framework del lado del
 * servidor. Extiende de la clase principal Slim implementando
 * la lógica general del sistema.
 * 
 * @package Raptor
 * @since   3.0.1
 */
class Raptor extends \Slim\Slim {
    
    const JSON = 'application/json';
    const APPXML = 'application/xml';
    const TEXTXML = 'text/xml';
    const CVS = 'text/csv';
    const PDF = 'application/pdf';
    const ZIP = 'application/octet-stream';
    const BMP = 'image/x-ms-bmp';
    const CSS = 'text/css';
    const GIF = 'image/gif';
    const HTM = 'text/html';
    const HTML = 'text/html';
    const SHTML = 'text/html';
    const ICO = 'image/vnd.microsoft.icon';
    const JPE = 'image/jpeg';
    const JPEG = 'image/jpeg';
    const JPG = 'image/jpeg';
    const JS = 'text/javascript';
    const PNG = 'image/png';
    const SVG = 'image/svg+xml';
    const SVGZ = 'image/svg+xml';
    const SWF = 'application/x-shockwave-flash';
    const SWFL = 'application/x-shockwave-flash';
    const TXT = 'text/plain';
    const XHT = 'application/xhtml+xml';
    const XHTML = 'application/xhtml+xml';
    const EXCEL = 'application/vnd.ms-excel';


    /**
     * Store the Global Application Config
     * @var Configuration\ConfigurationLoader
     */
    private $configuration;

    /**
     *
     * @var Security\Sessions\NativeSession
     */
    private $session;

    /**
     * 
     * @var Security\Security
     */
    private $security;

    /**
     *
     * @var Persister\Store
     */
    private $store;

    /**
     *
     * @var \App\AppAspectKernel
     */
    private $appAspectKernel;

    /**
     *
     * @var Language\Language
     */
    private $language;
    /**
     *
     * @var array
     */
    private $plugins;

    /**
     * 
     * @var Core\Inyector\Container
     */
    private $inyector;
    
    /**
     *
     * @var \Raptor\Bundle\Bundle
     */
    private $bundle;
    
    private $commands;
            
    function __construct(array $userSettings = array()) {
        $userSettings['view'] = '\Raptor\Template\View';
        
        parent::__construct($userSettings);
        $this->container->singleton('router', function ($c) {
                    return new Core\Router();
        });
        // Default request
        $this->container->singleton('request', function ($c) {
            return new http\Request($c['environment']);
        });
        $this->bundle=null;
        $this->commands=array();
    }
    
    /**
     * Setea el bundle actual en la ejecución del sistema de rutas
     * @param Bundle\Bundle $bundle Instancia del bundle actual en ejecución
     */
    public function setCurrentBundle($bundle) {
        $this->bundle=$bundle;
    }
    /**
     * 
     * Devuelve el bundle actual en la ejecución del sistema de rutas.
     * Tenga presente que este valor solo estará presente cuando se ejecuten las rutas correspondientes
     * a un bundle en específico, de lo contrario el valor sera null.
     * 
     * @return Bundle\Bundle
     */
    public function getCurrentBundle() {
        return $this->bundle;
    }

    /**
     *
     * @var Util\Timer
     */
    private $timer;

    /**
     *
     * @var Bundle\Route\RuleContainer 
     */
    private $ruleContainer;
    /**
     * Manda a ejecutar el core de Raptor
     * 
     */
    public function run() {
        
        $this->timer = new Util\Timer();
        $this->timer->start();
        $this->plugins=array();

        $this->appAspectKernel = \App\AppAspectKernel::getInstance();
        if(!file_exists(Core\Location::get(Core\Location::CACHE)))
            @mkdir (Core\Location::get(Core\Location::CACHE));
        /**
         * Esta instruccion fue movida hacia el cargador de configuracion
         * Llamando al gestor de AOP cuando la configuracion este lista
         */
//        $this->appAspectKernel->init(array(
//            'debug' => $this->config('debug'),
//            'appDir' => Core\Location::get(Core\Location::SRC),
//            'cacheDir' => Core\Location::get(Core\Location::CACHE).'/AOP'
//        ));
        
        $this->ruleContainer = new Bundle\Route\RuleContainer();
        $this->configuration = new Configuration\ConfigurationLoader();
	/**
         * Verify if the framework was moved of location
         */
        $locations = \Raptor\Raptor::getInstance()->getConfigurationLoader()->getOptions();
        $bundles = $locations['location'];
        $counting=0;
        $onefile=NULL;
        foreach ($bundles as $value) {
            if (!file_exists($value)){
                $counting++;
                $onefile=$value;
            }else{
                break;
            }
        }
        if($counting == count($bundles) and $counting>0){
            
            $this->configuration->forceLoad();
            if (strpos(PHP_SAPI, 'cgi') === 0) {
                header(sprintf('Status: %s', \Slim\Http\Response::getMessageForCode(302)));
            } else {
                header(sprintf('HTTP/%s %s', $this->config('http.version'), \Slim\Http\Response::getMessageForCode(302)));
            }
            header('Location: '.$_SERVER['REQUEST_URI']);
            return;
        }
        
        if($this->configuration->getConfOption('raptor','secret')!==false)
            $this->config('cookies.secret_key',$this->configuration->getConfOption('raptor','secret'));
        Security\Security::directives();
        
        $this->add(new Core\Routing());
        $this->add(new \App\Main());
        $this->add(new Language\Language());
        $this->add(new Security\Security());
        $this->add(new Persister\Store());
        
        $this->add(new Exception\Listener\RaptorExceptions());
        $this->inyector = new Core\Inyector\Container();
        
        parent::run();
    }
    
    /**
     * Adiciona un comando a la consola
     * 
     * @param Symfony\Component\Console\Command\Command $command
     */
    public function addCommand($command) {
        $this->commands[]=$command;
    }
    
    /**
     * Retorna todos los comandos registrados
     * @return array
     */
    public function getCommands() {
        return $this->commands;
    }
    
    /**
     * Setea un mensaje flash para peticiones subsiguientes
     * @param  string   $key llave del mensaje
     * @param  mixed    $value mensaje a enviar
     */
    public function flash($key, $value)
    {
        parent::flash($key, $value);
        $this->environment['slim.flash']->save();
    }

    /**
     * Setea un mensaje flash para la petición actual
     * @param  string   $key llave del mensaje
     * @param  mixed    $value mensaje a enviar
     */
    public function flashNow($key, $value)
    {
        parent::flashNow($key, $value);
        $this->environment['slim.flash']->save();
    }

    /**
     * Mantiene los mensajes flash de peticiones anteriores para las peticiones subsiguientes
     */
    public function flashKeep()
    {
        parent::flashKeep();
        $this->environment['slim.flash']->save();
    }
    
    /**
     * Ejecuta una operación de salvado de los mensajes actuales para peticiones subsiguientes
     */
    public function flashSave()
    {
        $this->environment['slim.flash']->save();
    }
    /**
     * Elimina de la respuesta actual el script enviado por Raptor para las librerias por defecto del lado del cliente.
     */
    public function removeClientWebScript()
    {
        $this->removeHook('slim.after', 'raptor:script');
        $this->removeHook('slim.after', 'raptor:panel');
    }
    
    /**
     * Retorna la instancia del cargador de configuración del sistema
     * El cargador contiene las directivas de configuración
     * general del sistema almacenados en options.json
     * 
     * Tener en cuenta que la configuración es levantada desde cache, nunca
     * directamente desde el archivo de configuración.
     * 
     * @return Configuration\ConfigurationLoader
     */
    public function getConfigurationLoader() {
        return $this->configuration;
    }
    
    /**
     * Retorna la instancia del Inyector de Dependencias
     * 
     * Este contiene todas las instancias de objetos previamente
     * registrados y seran identificados por su nombre de clase.
     * 
     * @return Core\Inyector\Container
     */
    public function getInyector() {
        return $this->inyector;
    }

    /**
     * Retorna el contenedor de reglas para el sistema.
     * Devolverá todas reglas registradas en los bundles de
     * la aplicación.
     * 
     * @return Bundle\Route\RuleContainer
     */
    public function getRuleContainer() {
        return $this->ruleContainer;
    }
    /**
     * Establece la clase controladora del lenguaje de la aplicación
     * 
     * @param \Raptor\Language\Language $language La clase controladora del lenguaje
     */
    public function setLanguage(Language\Language $language) {
        $this->language = $language;
    }

    /**
     * Devuelve la instancia de la clase controladora del lenguaje
     * 
     * @return Language\Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Devuelve la instancia del núcleo de Aspectos de la aplicación.
     * Este núcleo es el contenedor de Aspectos registrados en todos los
     * bundles del sistema.
     * 
     * @return \App\AppAspectKernel
     */
    public function getAppAspectKernel() {
        return $this->appAspectKernel;
    }

    /**
     * Retorna directamente la NativeSession utilizada por el sistema
     * 
     * @return Security\Sessions\NativeSession
     */
    public function getSession() {
        return $this->session;
    }
    
    /**
     * Establece el manejador de sesion para esta aplicación.
     * 
     * [ Esta funcion es usada por el Security para establecer el manejador en el momento
     * preciso con una verificación a sesiones remotas ]
     * 
     * @param Security\Sessions\NativeSession $session  El manejador de sesión
     */
    public function setSession($session) {
        $this->session=$session;
    }

    /**
     * Retorna la instancia del Store (Manejador de Persistencia, Doctrine ORM)
     * 
     * @return Persister\Store
     */
    public function getStore() {
        return $this->store;
    }
    
    /**
     * Establece la instancia del Store (Manejador de Persistencia, Doctrine ORM)
     * 
     * [ Esta función es usada por Raptor para establecer el manejador ]
     * 
     * @param \Raptor\Persister\Store $store Manejador de Persistencia, Doctrine ORM
     */
    public function setStore(Persister\Store $store) {
        $this->store = $store;
    }
    
    /**
     * Establece la instancia del Manejador de seguridad del sistema.
     * 
     * [ Esta función es usada por Raptor para establecer el manejador ]
     * 
     * @param \Raptor\Security\Security $security El manejador de seguridad para esta aplicacion
     */
    public function setSecurity(Security\Security $security) {
        $this->security = $security;
    }
    
    /**
     * Devuelve la instancia del Manejador de Seguridad
     * 
     * @return Security\Security
     */
    public function getSecurity() {
        return $this->security;
    }
    /**
     * Registra e inyecta codigo para un nombre de plugin determinado.
     * 
     * El primer parámetro establece para que punto de inyección se inyectará
     * el código pasado por el segundo parámetro.
     * 
     * Los hotpots declarados en Raptor por defecto son:
     * 
     * raptor_bundle:
     * nombre de plugin reservado para inyectar contenido html en el menú Componentes del panel de control.
     * 
     * raptor_tools:
     * nombre de plugin reservado para inyectar contenido html en el menú Herramientas del panel de control.
     * 
     * raptor_panel:
     * nombre de plugin reservado para inyectar contenido html en la barra principal del panel de control.
     * 
     * core_library_inside: 
     * nombre de plugin reservado para inyectar funciones javascript en la clase core enviada al cliente. 
     * Ejemplo getHola: function(){ … }, getHola2: function(){ … }
     * 
     * core_library_outside:
     * nombre de plugin reservado para inyectar funciones javascript en el espacio de variables de la biblioteca
     * core, este contenido es inyectadoo luego de la creación del objeto Raptor.
     * 
     * core_header: 
     * nombre de plugin reservado para inyectar contenido html en la sección header de la respuesta actual, 
     * es inyectado luego del script del core de Raptor.
     * 
     * PUEDES ADEMÁS CRETAR TUS PROPIOS PUNTOS DE INYECCIÓN Y LLAMARLOS EN LAS PLANTILLAS TWIG A TRAVÉS DE LA FUNCIÓN plugin()
     * 
     * @param string $key nombre del punto de inyección eje. raptor_bundle
     * @param string $value el contenido o codigo a inyectar, normalmente utilizado en conjunto con render()
     */
    public function setViewPlugin($key,$value) {
        
            if(!isset($this->plugins[$key]))
                $this->plugins[$key]=array();
            $this->plugins[$key][]=$value;
        
    }
    /**
     * Retorna un array de todo el contenido registrado para ese nombre de punto de inyección.
     * 
     * @param string|NULL $key nombre del punto de inyeccion
     * @return array|false
     */
    public function getViewPlugin($key='') {
        if($key==''){
            return $this->plugins;
        }else{
            if(isset($this->plugins[$key]))
                return $this->plugins[$key];
            else
                return false;
        }
    }
    /**
     * Retorna el Manejador de API de Raptor, esta debe devolver toda la documentación declarada en Raptor.
     * 
     * @return boolean
     */
    public function getApi() {
        $api = new \Raptor\Cache\Cache('api');
        if($api->isDirty()){
           return false; 
        }else{
           return $api->getData(); 
        }
    }

    /**
     * Devuelve la instancia de la clase utilitaria Timer, esta devuelve información sobre el
     * tiempo en ejecución de bloques de codigo etc.
     * 
     * @return Util\Timer
     */
    public function getTimer() {
        return $this->timer;
    }

    /**
     * Retorna una instancia única de esta aplicación Raptor de acuerdo a su nombre
     * 
     * @param  string $name El nombre de la aplicación
     * @return \Raptor\Raptor|null
     */
     public static function getInstance($name = 'default') {
        return isset(static::$apps[$name]) ? static::$apps[$name] : null;
    }
    
    /**
     * Renderiza una plantilla twig. 
     * $this->render('@exampleBundle/index.twig');
     *
     * Call this method within a GET, POST, PUT, PATCH, DELETE, NOT FOUND, or ERROR
     * callable to render a template whose output is appended to the
     * current HTTP response body. How the template is rendered is
     * delegated to the current View.
     *
     * @param  string $template The name of the template passed into the view's render() method
     * @param  array  $data     Associative array of data made available to the view
     * @param  int    $status   The HTTP response status code to use (optional)
     */
    public function render($template, $data = array(), $status = null) {
        if (!is_null($status)) {
            $this->response->status($status);
        }
        $this->view->setTemplatesDirectory($this->config('templates.path'));
        $this->view->appendData($data);
        return $this->view->display($template);
    }
    
    /**
     * Generate diagnostic template markup
     *
     * This method accepts a title and body content to generate an HTML document layout.
     *
     * @param  string   $title  The title of the HTML template
     * @param  string   $body   The body content of the HTML template
     * @return string
     */
    protected static function generateTemplateMarkup($title, $body)
    {
        return sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body><div style='background-color: #402878 ;color: white;padding: 25px;'><h1>%s</h1></div><div style='background:gray;color:white;margin-top:0;padding:25px;'>%s</div></body></html>", $title, $title, $body);
    }

    /**
     * Default Not Found handler
     */
    protected function defaultNotFound()
    {
        if($this->request()->isXhr()){
            $this->contentType(\Raptor\Raptor::JSON);
            $response=new \Raptor\Util\ItemList();
            $response->set('cod',5);
            $response->set('msg',"<h2>404 Página no encontrada</h2>".'<h4>La página que buscas no pudo ser encontrada. Revisa la barra de direcciones y verifica si está bien escrita. Si esta acción falla, puedes visitar nuestra página principal en el link de abajo.</p><a href="' . $this->request->getRootUri() . '/">Visitar página principal</a></h4>');
            echo $response->toJson();
            return;
        }
        echo $this->render("@systemBundle/error/index.html.twig",array(
            'title'=>'404 Página no encontrada',
            'body'=>'La página que buscas no pudo ser encontrada. Revisa la barra de direcciones y verifica si está bien escrita. Si esta acción falla, puedes visitar nuestra página principal en el link de abajo.',
            'uri'=>$this->request->getRootUri()
        ));
        //echo static::generateTemplateMarkup('404 Page Not Found', '<p>The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.</p><a href="' . $this->request->getRootUri() . '/">Visit the Home Page</a>');
    }

    /**
     * Default Error handler
     */
    protected function defaultError($e)
    {
        $this->getLog()->error($e);
        if($this->request()->isXhr()){
            $this->contentType(\Raptor\Raptor::JSON);
            $response=new \Raptor\Util\ItemList();
            $response->set('cod',5);
            $response->set('msg',"<h2>Error</h2>".'<h4>Ocurrió un error en el sitio. El administrador de esta página ha sido notificado del problema, disculpe por el inconveniente temporal.</h4>');
            echo $response->toJson();
            return;
        }
        echo $this->render("@systemBundle/error/index.html.twig",array(
            'title'=>'Error',
            'body'=>'Ocurrió un error en el sitio. El administrador de esta página ha sido notificado del problema, disculpe por el inconveniente temporal.',
            'uri'=>$this->request->getRootUri()
        ));
        //echo self::generateTemplateMarkup('Error', '<p>A website error has occurred. The website administrator has been notified of the issue. Sorry for the temporary inconvenience.</p>');
    }
    
    /**
     * Datos a debuguear, serán encontrados en el directorio app en el archivo debug
     * @param mixed $data
     */
    static public function debug($data) {
        $trace = debug_backtrace();
        if(file_exists(Core\Location::get(Core\Location::APP).'/debug'))
            $current=  file_get_contents(Core\Location::get(Core\Location::APP).'/debug');
        else
            $current="\n";
        
        file_put_contents(Core\Location::get(Core\Location::APP).'/debug', "Debug --> Called in Class --> ".$trace[1]['class']." line: ".$trace[0]['line']." ---> ".var_export($data, true)."\n".$current);
     
    }
    
}

