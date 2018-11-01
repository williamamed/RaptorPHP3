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
namespace Raptor2\SyntarsusBundle\Manager\Log;

/**
 * Log Writer
 */
class LogWriter
{
    /**
     * @var resource
     */
    protected $resource;
    /**
     *
     * @var \Raptor\Raptor
     */
    private $app;


    public function __construct($app)
    {
        $this->app=$app;
    }

    /**
     * Write message
     * @param  mixed     $message
     * @param  int       $level
     * @return int|bool
     */
    public function write($message, $level = null)
    {
        try {

            $trace = new \Raptor2\SyntarsusBundle\Model\Entity\SecurityTrace();
            $trace->setIp($this->app->request()->getIp());
            $trace->setADate(new \DateTime());
            $trace->setLog($message);
            $trace->setState(($level == null) ? 10 : $level);
            if ($this->app->getSecurity()->isAuthenticated()) {
                $user = $this->app->getSecurity()->getUser();
                $trace->setUsername($user['username']);
            } else {
                $trace->setUsername('public');
            }
            $this->app->getStore()->getManager()->persist($trace);
            $this->app->getStore()->getManager()->flush();
        } catch (\Exception $exc) {
            //This prevent recursive database exceptions
        }
    }
}
