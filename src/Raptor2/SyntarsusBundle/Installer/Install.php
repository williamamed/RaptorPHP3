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
$app = \Raptor\Raptor::getInstance();
$message = '';
/**
 * Publish the Resources files of the Bundle
 */
Raptor\Bundle\Publisher\Publisher::run('\Raptor2\SyntarsusBundle\SyntarsusBundle');
$app->getConfigurationLoader()->setConfOption(array('syntarsus' => array('auth' => 'native', 'samlbase' => dirname($app->request()->getUrl() . $_SERVER['SCRIPT_NAME']))));
$app->getConfigurationLoader()->writeOptions();

if (!$app->getStore()->getImporter()->tablesExist(array('security_category', 'security_estructure', 'security_privilege', 'security_rol', 'security_trace', 'security_user'))) {
    /**
     * Generate all tables of Syntarsus
     */
    $import = $app->getStore()
            ->getImporter()
            ->createIfNotExist(true);
    $result = $import->import(__DIR__ . '/data/security.php');
    

    if ($result)
        $message = "El modulo de seguridad Syntarsus fue instalado correctamente!!";
    else {
        $error = $import->getErrors();
        $message = "Un error ocurrió y el módulo de seguridad Syntarsus no pudo ser instalado
Detalles:  $error           
";
    }
} else {
    Raptor\Bundle\Publisher\Publisher::run('\Raptor2\SyntarsusBundle\SyntarsusBundle');
    $message = "El modulo de seguridad Syntarsus fue instalado correctamente!!, pero no pudimos instalar las tablas relacionadas al componente:
 Razón:  Las Tablas ya se encuentran creadas   
";
    return $message;
}
?>
