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
 * @Target({"METHOD"})
 * 
 * @author Amed
 */
final class Api implements \Doctrine\ORM\Mapping\Annotation {
    
    /** 
     * @var string 
     * @Required
     */
    public $name;
    /** 
     * @var string 
     * @Required
     */
    public $category;
    /** 
     * @var string 
     * @Required
     */
    public $version = '0.0.0';
}
