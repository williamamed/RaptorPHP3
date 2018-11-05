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
namespace Raptor\Component\systemBundle\Rule;

/**
 * Description of Panel
 *
 * 
 */
class Command implements \Raptor\Bundle\Route\Rule {
    
    public function call(\Raptor\Raptor $app) {
        if(!$app->config('mock'))
            return true;
        $module=  $app->getConfigurationLoader()->getBundlesSpecifications()['systemBundle']['version'];
        $cli = new \Symfony\Component\Console\Application('Linea de comando Raptor PHP 3', $module);
        $cli->setCatchExceptions(true);
        
        $cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet(array(
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app->getStore()->getManager()->getConnection()),
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($app->getStore()->getManager())
        )));

        // Register All Doctrine Commands
        //\Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($cli);

        // Register your own command
        $own=  $app->getCommands();
        foreach ($own as $command) {
            $cli->add($command);
        }
        
        // Runs console application
        $cli->run();
        
        return true;
    }
   
}

?>
