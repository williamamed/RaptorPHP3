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

namespace Raptor\Persister;

/**
 * 
 * La clase Importer sube los datos previamente salvador con el Exporter hacia la base
 * de datos
 * 
 */
class Importer {

    /**
     *
     * @var Store
     */
    private $store;
    private $create;
    private $errors;
    private $delete;

    function __construct($store) {
        $this->create = false;
        $this->delete = false;
        $this->store = $store;
    }

    /**
     * Realiza una operacion CREATE si es posible,esta funcion es solo para archivos de datos generados a partir de Entidades
     * Determina si la clase Importer debe buscar por definiciones create en base a las entidades
     * usadas en la generacion del archivo de datos
     * [SI LOS DATOS FUERON GENERADOS POR TABLE NAMES ENTONCES NO SE PUEDE REALIZAR ESTA ACCION]
     * 
     * $this->getStore()->getImporter()->createIfNotExist(true)->import(__DIR__.'/exportExample.php');
     * 
     * @param boolean $create
     * @return \Raptor\Persister\Importer
     */
    public function createIfNotExist($create) {
        $this->create = $create;
        return $this;
    }
    
    public function deleteIfExist() {
        $this->delete = true;
        return $this;
    }

    /**
     * 
     * Importa los datos previamente salvados con el Exporter hacia la base de datos
     * Esta funcion retorna TRUE si la rutina fue realizada con exito, FALSE en caso contrario
     * 
     * $this->getStore()->getImporter()->createIfNotExist(true)->import(__DIR__.'/exportExample.php');
     * 
     * @param string $file el archivo a subir
     * @throws \Exception throws una excepcion cuando el archivo a subir no exista
     * @return boolean
     */
    public function import($file) {
        if (!file_exists($file))
            throw new \Exception("The file especified in the Importer does not exist");
        include $file;

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->store->getManager());
       
        
        try {
            $this->store->getManager()->getConnection()->beginTransaction();
            if($this->delete){
                
                foreach (array_keys($data) as $tablename) {
                    
                    if($this->store->getManager()->getConnection()->getSchemaManager()->tablesExist(array($this->inverseNormalizeTable($tablename)))){
                       $this->store->getManager()->getConnection()->exec("DROP TABLE ".$this->inverseNormalizeTable($tablename)." CASCADE");
                       
                    }
                    if($this->store->getManager()->getConnection()->getSchemaManager()->tablesExist(array($tablename))){
                       $this->store->getManager()->getConnection()->exec("DROP TABLE  ".$tablename." CASCADE");
                       
                    }
                    
                }
            }
//            $this->store->getManager()->getConnection()->commit();
//            die;
            $meta = array();
            
            if ($this->create) {
                $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
                $cmf->setEntityManager($this->store->getManager()); // we must set the EntityManager    
                foreach ($create as $classB) {
                    $currentMeta=$this->store->getManager()->getMetadataFactory()->getMetadataFor($classB);
                    $find=false;
                    $metaDisconect=null;
                    foreach ($this->store->getSchemaClass() as $tableName) {
                        $metaDisconect = $cmf->getMetadataFor($tableName);
                        
                        if($this->normalizeTable($metaDisconect->getTableName())==$this->normalizeTable($currentMeta->getTableName())){
                            
                            $find=true;
                            break;
                        }
                    }
                    if($find && $metaDisconect){
                        
                        $currentMeta->setSequenceGeneratorDefinition($metaDisconect->sequenceGeneratorDefinition);
                        
                    }
                    
                    $meta[] = $currentMeta;
                    
                }
                
                
                $schemaTool->updateSchema($meta, true);
            }
            
            $this->store->getManager()->getConnection()->commit();
        } catch (\Exception $exc) {
            $this->errors = $exc->getMessage() ." ".$exc->getTraceAsString();
            return false;
        }
        //die;
       
        try {

            $this->store->getManager()->getConnection()->beginTransaction();
            $ord = new TableDependencyOrder(array_keys($data), $this->store);
            $dependency = $ord->getOrder();
            
            foreach ($dependency as $tableName) {
                $normalized = $tableName;
                $valid = false;
                if (isset($data[$normalized]))
                    $valid = true;
                if(!$valid){
                    $normalized=  str_replace('public.', '', $normalized);
                }
                if (isset($data[$normalized]))
                    $valid = true;
                if ($valid) {
                    $rows = $data[$normalized];
                    foreach ($rows as $row) {
                        
                        $this->store->getManager()->getConnection()->insert($normalized, $row);
                    }
                }
            }
            $this->store->getManager()->getConnection()->commit();
            //return true;
        } catch (\Exception $exc) {
            $this->errors = $exc->getMessage() ." ".$exc->getTraceAsString();
            $this->store->getManager()->getConnection()->rollback();
            return false;
        }
        
        try {

            $meta = array();
            if ($this->create) {
                $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
                $cmf->setEntityManager($this->store->getManager()); // we must set the EntityManager    
                foreach ($create as $classB) {
                    $currentMeta=$this->store->getManager()->getMetadataFactory()->getMetadataFor($classB);
                    $find=false;
                    $metaDisconect=null;
                    foreach ($this->store->getSchemaClass() as $tableName) {
                        $metaDisconect = $cmf->getMetadataFor($tableName);
                        
                        if($this->normalizeTable($metaDisconect->getTableName())==$this->normalizeTable($currentMeta->getTableName())){
                            
                            $find=true;
                            break;
                        }
                    }
                    if($find && $metaDisconect){
                        
                        $currentValue=$this->store
                                ->getManager()
                                ->getConnection()
                                ->fetchColumn('select max('.$metaDisconect->getIdentifier()[0].') from '.$metaDisconect->getTableName());
                        if($currentValue)
                            $currentValue=  intval($currentValue)+1;
                        else
                            $currentValue=1;

                        $sequence=new \Doctrine\DBAL\Schema\Sequence($metaDisconect->sequenceGeneratorDefinition['sequenceName'], 1, 21);

                                    
                        $affected=$this->store
                                ->getManager()
                                ->getConnection()
                                ->exec("ALTER SEQUENCE ".$sequence->getQuotedName($this->store
                                            ->getManager()
                                            ->getConnection()
                                            ->getDatabasePlatform())." START WITH ".$currentValue." RESTART");

                    }
                    
                }
                
                return true;
               
            }
        } catch (\Exception $exc) {
            $this->errors = $exc->getMessage() ." ".$exc->getTraceAsString();
            return false;
        }
        
        
    }

    /**
     * 
     * Si ha ocurrido un error en la rutina de importacion esta funcion retorna los errores
     * 
     * $result=$this->getStore()->getImporter()->createIfNotExist(true)->import(__DIR__.'/exportExample.php');
     * 
     * if ($result==false){
     * echo $this->getStore()->getImporter()->getErrors();
     * }
     * 
     * @return boolean
     */
    public function getErrors() {
        return $this->errors;
    }
    
    public function normalizeTable($name) {
        $table=$name;
        $ex=explode('public.', $table);
        if(count($ex)>1){
            $table=$ex[1];
        }
        return $table;
    }
    
    public function inverseNormalizeTable($name) {
        $table=$name;
        $ex=explode('public.', $table);
        if(count($ex)==1){
            $table='public.'.$table;
        }
        return $table;
    }
    
    public function tablesExist($tables) {
        $normalized=array();
        foreach ($tables as $value) {
            $normalized[]='public.'.$value;
        }
        
        return $this->store->getManager()->getConnection()->getSchemaManager()->tablesExist($normalized) ||
                $this->store->getManager()->getConnection()->getSchemaManager()->tablesExist($tables);
        
    }

}

?>
