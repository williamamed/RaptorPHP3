<?php

/**
 * Raptor - Integration PHP 5 framework
 *
 * @author      William Amed <watamayo90@gmail.com>
 * @copyright   2018 
 * @link        http://dinobyte.net
 * @version     3.0.1
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

namespace Raptor\Exception\Listener;

/**
 * Description of RaptorException
 *
 * 
 */
class RaptorExceptions extends \Slim\Middleware {

    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     * @param array $settings
     */
    public function __construct($settings = array()) {
        $this->settings = $settings;
    }

    /**
     * Call
     */
    public function call() {

        try {
            $this->next->call();
        } catch (\Exception $e) {
            $log = $this->app->getLog(); // Force Slim to append log to env if not already
            $env = $this->app->environment();
            $env['slim.log'] = $log;
            $env['slim.log']->error($e);
            $this->app->contentType('text/html');
            $this->app->response()->status(500);

            if (!$this->app->config('debug')) {
                $this->app->response()->body($this->renderGeneric($env, $e));
                return;
            }
            switch (get_class($e)) {
                case "Raptor\Exception\Csrf":
                    $this->app->response()->body($this->renderCsrf($env, $e));
                    break;
                default:
                    $this->app->response()->body($this->renderBody($env, $e));
                    break;
            }
        }
    }

    /**
     * Render response body
     * @param  array      $env
     * @param  \Exception $exception
     * @return string
     */
    protected function renderBody(&$env, $exception) {
        $title = 'Raptor Application Error';
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $html = sprintf('<div style="background-color: #262b30 ;color: white;padding: 25px;width:100%s"><h1>%s</h1></div>', "%", $title);
        $html .='<div style="background:#262b30;color:white;margin-top:0;padding:25px;overflow-x:scroll;width:100%">';
        $html .= '<p>La aplicación no pudo ejecutarse correctamente debido al siguiente error:</p>';
        $html .= '<h2>Detalles</h2>';
        $html .= sprintf('<div><strong>Tipo:</strong> %s</div>', get_class($exception));
        if ($code) {
            $html .= sprintf('<div><strong>Codigo:</strong> %s</div>', $code);
        }
        if ($message) {
            $html .= sprintf('<div><strong>Mensaje:</strong> %s</div>', $message);
        }
        if ($file) {
            $html .= sprintf('<div><strong>Archivo:</strong> %s</div>', $file);
        }
        if ($line) {
            $html .= sprintf('<div><strong>Linea:</strong> %s</div>', $line);
        }
        if ($trace) {
            $html .= '<h2>Trazas</h2>';
            $html .= sprintf('<pre>%s</pre>', $trace);
        }
        $html .='</div>';
        if ($this->app->request()->isXhr()) {
            $this->app->contentType(\Raptor\Raptor::JSON);
            $response = new \Raptor\Util\ItemList();
            $response->set('cod', 5);
            $response->set('msg', "<h2>Error</h2>" . "<h4>Mensaje: $message</h4>");
            $response->set('trace', "<div style='overflow:auto;height:200px;width:100%'>$html</div>");
            return $response->toJson();
        }
        return $this->app->render('@systemBundle/error/exception.html.twig', array('e' => $exception, 'class' => get_class($exception), 'title' => 'Error'));
        return sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body>%s</body></html>", $title, $html);
    }

    /**
     * Render response body
     * @param  array      $env
     * @param  \Exception $exception
     * @return string
     */
    protected function renderCsrf(&$env, $exception) {
        $title = 'Security Breach';

        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();

        $html = sprintf('<div style="background-color: #262b30 ;color: white;padding: 25px;width:100%s"><h1>%s</h1></div>', "%", $title);
        $html .='<div style="background:#262b30;color:white;margin-top:0;padding:25px;overflow-x:scroll;width:100%">';

        $html .= '<h2>Detalles</h2>';

        if ($message) {
            $html .= sprintf('<div><strong>Mensaje:</strong> %s</div>', $message);
        }
        if ($file) {
            $html .= sprintf('<div><strong>Archivo:</strong> %s</div>', $file);
        }
        if ($line) {
            $html .= sprintf('<div><strong>Linea:</strong> %s</div>', $line);
        }

        $html .='</div>';
        if ($this->app->request()->isXhr()) {
            $this->app->contentType(\Raptor\Raptor::JSON);
            $response = new \Raptor\Util\ItemList();
            $response->set('cod', 5);
            $response->set('msg', "<h2>Error</h2>" . "<h4>Mensaje: $message</h4>");
            $response->set('trace', "<div style='overflow:auto;height:200px;width:100%'>$html</div>");
            return $response->toJson();
        }
        return $this->app->render('@systemBundle/error/csrf.html.twig', array('e' => $exception, 'class' => get_class($exception), 'title' => 'Violación de seguridad'));
        return sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body>%s</body></html>", $title, $html);
    }

    /**
     * Render response body
     * @param  array      $env
     * @param  \Exception $exception
     * @return string
     */
    protected function renderGeneric(&$env, $exception) {
        $title = 'Ups algo salió mal !!';

        $html = sprintf('<div style="background-color: #402878 ;color: white;padding: 25px;width:100%s"><h1>%s</h1></div>', "%", $title);
        $html .='<div style="background:gray;color:white;margin-top:0;padding:25px;overflow-x:scroll;width:100%">';

        $html .= '<p>Por favor póngase en contacto con su administrador</p>';

        $html .='</div>';
        if ($this->app->request()->isXhr()) {
            $this->app->contentType(\Raptor\Raptor::JSON);
            $response = new \Raptor\Util\ItemList();
            $response->set('cod', 5);
            $response->set('msg', '<h2>Ups algo salió mal !!</h2>');

            return $response->toJson();
        }
        return sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body>%s</body></html>", $title, $html);
    }

    public function errorTemplate($exception) {

        $title = 'Error';
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();

        return $this->renderPlainTemplate(file_get_contents(__DIR__ . '/errorbasic.html'), array(
                    'class' => get_class($exception),
                    'code' => $code,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'trace' => $trace,
                    'title' => 'Error',
                    'url' => (dirname($_SERVER['SCRIPT_NAME']) == '/' or dirname($_SERVER['SCRIPT_NAME']) == '\\') ? '/' : dirname($_SERVER['SCRIPT_NAME']) . '/'
        ));
    }

    private function renderPlainTemplate($html, $array) {
        $text = $html;
        foreach ($array as $key => $value) {
            $text = str_replace("{{" . $key . "}}", $value, $text);
        }
        return $text;
    }

}

?>
