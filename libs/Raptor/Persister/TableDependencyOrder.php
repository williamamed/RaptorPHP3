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
 * TableDependencyOrder return the order of schema creation
 * to avoid the dependency issue about associations
 * 
 */
class TableDependencyOrder {

    private $tables;

    /**
     *
     * @var Store
     */
    private $store;
    private $order;

    /**
     * 
     * @param array $tables The table name to order
     * @param type $store
     */
    function __construct(array $tables, $store) {
        $this->tables = $tables;
        $this->store = $store;
        $this->order = array();
        
        if ($this->store->getManager()->getConnection()->getDatabasePlatform()->getName() == 'postgresql') {
            foreach ($this->tables as $key => $table) {
                if (count(explode('.', $table)) == 1) {
                    $this->tables[$key] = 'public.' . $table;
                }
                
            }
        }
    }

    public function searchAndPush($items) {
        foreach ($items as $item) {
            $find = false;
            foreach ($this->order as $key => $ordered) {
                if ($ordered == $item) {
                    //$this->order = array_splice($this->order, $key, 0, $item);
                    $find = true;
                    break;
                }
            }
            if (!$find)
                $this->order[] = $item;
        }
    }

    /**
     * 
     * @return array An array of ordered schemas
     */
    public function getOrder() {
        

        foreach ($this->tables as $table) {

            $many = $this->store->getManager()->getConnection()->getSchemaManager()->listTableForeignKeys($table);
            $foreign = array();
            foreach ($many as $associations) {
                $converted = $associations->getForeignTableName();
                if ($this->store->getManager()->getConnection()->getDatabasePlatform()->getName() == 'postgresql') {

                    if (count(explode('.', $converted)) == 1) {
                        $converted = 'public.' . $converted;
                    }
                }
                $foreign[] = $converted;
            }
            $foreign[]=$table;
            if (count($many) > 0) {
                $this->recursive($many);
            }
            $this->searchAndPush($foreign);
            
        }
        return $this->order;
    }

    private function recursive($foreign) {

        foreach ($foreign as $f) {
            $converted = $f->getForeignTableName();
            if ($this->store->getManager()->getConnection()->getDatabasePlatform()->getName() == 'postgresql') {

                if (count(explode('.', $converted)) == 1) {
                    $converted = 'public.' . $converted;
                }
            }
            $many = $this->store->getManager()->getConnection()->getSchemaManager()->listTableForeignKeys($converted);
            $foreign = array();
            foreach ($many as $associations) {
                $converted2 = $associations->getForeignTableName();
                if ($this->store->getManager()->getConnection()->getDatabasePlatform()->getName() == 'postgresql') {

                    if (count(explode('.', $converted2)) == 1) {
                        $converted2 = 'public.' . $converted2;
                    }
                }
                $foreign[] = $converted2;
            }
            $foreign[]=$converted;
            if (count($many) > 0) {
                $this->recursive($many);
            }
            $this->searchAndPush($foreign);
        }
    }

}

?>
