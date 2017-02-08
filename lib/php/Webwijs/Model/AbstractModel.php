<?php

namespace Webwijs\Model;

use Webwijs\Loader\ClassLoader;

abstract class AbstractModel
{
    public $type;
    public $tableConfig;
    
    protected $table;
    protected $data = array();
    protected $modified = array();
    
    public function __construct($data = null)
    {
        $this->setData($data);
    }

    public function setData($values)
    {
        foreach ((array) $values as $key => $value) {
            $this->__set($key, $value);
        }
    }
    public function getData()
    {
        return $this->data;
    }
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }
    public function __set($name, $value)
    {
        $this->setModified($name);
        $method = 'get' . $name;
        if (method_exists($this, $name)) {
            return $this->$method($value);
        }
        $this->data[$name] = $value;
    }
    public function setModified($name, $modified = true)
    {
        $this->modified[$name] = $modified;
        return $this;
    }
    public function isModified($name)
    {
        return !empty($this->modified[$name]);
    }
    public function save()
    {
        return self::getTable()->save($this);
    }
    public function delete()
    {
        return self::getTable()->delete($this);
    }
    
    /**
     * Returns the table associated with this model.
     *
     * @return object the table for this model.
     */
    public function getTable()
    {
        global $wpdb;
        
        /*
         * find and instantiate a new model table and
         * store it for future calls.
         */
        $table = $this->table;
        if (empty($table)) {        
            /*
             * find a table that matches with this model, otherwise
             * the default table will be loaded.
             */
            $class = ClassLoader::loadStatic('modeltable', $this->type);
            if (!$class) {
                $class = ClassLoader::loadStatic('modeltable', 'DefaultTable');
            }
            
            // verify that a class was found that can be instantiated.
            if ($class) {                
                // instantiate the table object.
                $table = new $class($this->type, $this->tableConfig);
            }
            
            // store the instantiated table.
            $this->table = $table;
        }
        
        // return table.
        return $table;
    }
    
    /**
     * Creates a new database table that this model represents.
     */
    abstract public function setup();
}

