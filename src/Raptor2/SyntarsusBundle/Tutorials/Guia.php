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
namespace Raptor2\SyntarsusBundle\Tutorials;
/**
 * Description of Guia
 *
 * 
 */
class Guia implements \Raptor\Bundle\Route\Rule{
    /**
     *
     * @var \Raptor2\InteractiveBundle\Manager\InteractiveManager
     */
    private $interactive;
    

    public function call(\Raptor\Raptor $app) {
        $this->interactive=$app->getInyector()->get('InteractiveManager');
        $this->userManager();
        $this->rolManager();
        $this->privilegesManager();
        $this->estructureManager();
        $this->categoryManager();
        $this->auditoriesManager();
        return false;
    }
    
    private function userManager() {
        $this->interactive->add(array(
            'name'=>'syn.user.welcome',
            'seconds'=>35,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'next'=>'syn.user.estructura',
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.estructura',
            'pointer'=>".x-grid-body table tbody tr:first td:last",
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.selectestructure',
            'pointer'=>".x-toolbar span:has(span.icon-add)",
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.add',
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.asignrol',
            'seconds'=>40,
            'pointer'=>".x-toolbar span:has(span.icon-user)",
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.user.active',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.active',
            'pointer'=>".x-grid-body .x-grid-view table tbody tr:first td:last",
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.user.edit',
            'position'=>'left top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.user.edit',
            'waitSeconds'=>30,
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'left top'
        ),'SyntarsusBundle');
    }
    
    private function rolManager() {
        $this->interactive->add(array(
            'name'=>'syn.rol.rol',
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.rol.add',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.rol.add',
            'pointer'=>'.x-toolbar span:has(span.icon-add)',
            'seconds'=>40,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'next'=>'syn.rol.privileges',
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.rol.privileges',
            'pointer'=>'.x-toolbar span:has(span.icon-privilege)',
            'seconds'=>40,
            'waitSeconds'=>20,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.rol.privilegesdescription',
            'pointer'=>'arrow:right .x-tree-view tr td div .icon-privilege',
            'seconds'=>60,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
    }
    
    private function privilegesManager() {
        $this->interactive->add(array(
            'name'=>'syn.priv.description1',
            'pointer'=>'.x-tree-view tr:first',
            'seconds'=>45,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.priv.description2',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.priv.description2',
            'pointer'=>'.x-toolbar span:has(span.icon-vendor)',
            'seconds'=>60,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.priv.description3',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.priv.description3',
            'pointer'=>'.x-toolbar span:has(span.icon-actions)',
            'seconds'=>60,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'next'=>'syn.priv.begin',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.priv.begin',
            'seconds'=>35,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
        
        $this->interactive->add(array(
            'name'=>'syn.priv.actions',
            'seconds'=>35,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
    }
   
    private function estructureManager() {
        $this->interactive->add(array(
            'name'=>'syn.estructure.description',
            'seconds'=>60,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
    }
    
    private function categoryManager() {
        $this->interactive->add(array(
            'name'=>'syn.category.description',
            'seconds'=>30,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
    }

    private function auditoriesManager() {
        $this->interactive->add(array(
            'name'=>'syn.auditories.description',
            'pointer'=>'.x-toolbar span:has(span.icon-grafica)',
            'seconds'=>55,
            'style'=>array(
                'background'=>'darkblue'
            ),
            'author'=>'amed',
            'position'=>'right top'
        ),'SyntarsusBundle');
    }
}

?>
