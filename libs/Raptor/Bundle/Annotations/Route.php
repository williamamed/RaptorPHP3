<?php
namespace Raptor\Bundle\Annotations;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @Annotation
 * @Target({"METHOD","CLASS"})
 * 
 * @author Amed
 */
final class Route implements \Doctrine\ORM\Mapping\Annotation {
    
    /** @var string */
    public $path;
    /** @var string */
    public $name;
    /** @var string */
    public $method = 'ALL';
    /** @var boolean */
    public $csrf = false;
    /** @var string */
    public $rule;
    /** @var string */
    public $description;
}
