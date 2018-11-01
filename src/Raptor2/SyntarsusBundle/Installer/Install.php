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

if (!$app->getStore()->getManager()->getConnection()->getSchemaManager()->tablesExist(array('security_category', 'security_estructure', 'security_privilege', 'security_rol', 'security_trace', 'security_user'))) {
    /**
     * Generate all tables of Syntarsus
     */
    $import = $app->getStore()->getImporter()->createIfNotExist(true);
    $result = $import->import(__DIR__ . '/data/security.php');


    if ($result)
        $message = "The Syntarsus Module was installed correctly";
    else {
        $error = $import->getErrors();
        $message = "An error ocurred, the Syntarsus Module cannot be installed correctly:
Details:  $error           
";
    }
} else {
    Raptor\Bundle\Publisher\Publisher::run('\Raptor2\SyntarsusBundle\SyntarsusBundle');
    $message = "The Syntarsus Module was installed correctly, but cannot install the database tables:
 Details:  We found a similar installed schema        
";
    return $message;
}
?>
