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
 * @Target({"METHOD","PROPERTY"})
 * 
 * @author Amed
 */
final class Inyect implements \Doctrine\ORM\Mapping\Annotation {
    /** @var string */
    public $params;
}
