<?php

namespace Webwijs;

use Webwijs\Loader\ClassLoader;
use Webwijs\Model\AbstractModel;
use Webwijs\Model\Tables\AbstractTable;

/**
 * this manager allows the creation of new database
 * tables by listening for specific events, it also
 * instantiates models that are not yet registered.
 *
 * So when you instantiate a new model you should
 * also register it with the manager using the
 * {@link setModel($name, $model)} method, this
 * not only allows the manager to create the actual
 * database tables but also increase the performance
 * since all models are stored by the manager.
 * 
 * @author Christopher Andrew Harris
 * @version 1.1.0
 * @since 1.0.0
 */
class ModelManager
{
    /**
     * Contains model instances that are either added or
     * created by the model manager.
     *
     * @var array 
     */
    protected $models;
    
    /**
     * Constructs a manager and registers listeners which create database
     * tables based upon the models stored by the manager.
     */
    public function __construct()
    {      
        // create tables when a theme has switched.
        add_action('after_switch_theme', array($this, 'createTables'), 10, 0);
        // create tables when a site is created.
        add_action('wpmu_new_blog', array($this, 'createTables'), 10, 1);
        // drop tables when a site is deleted.
        add_filter('wpmu_drop_tables', array($this, 'dropTables'), 10, 1);
    }
    
    /**
     * Add multiple models to the manager by passing eiher an associative
     * array or iterable object where each model will be associated with 
     * the name.
     *
     * @param mixed $models either an associative array or iterable object.
     * @return ModelManager returns itself which allows chaining of operations.
     * @since 1.0.0
     */
    public function addModels($models)
    {
        if (is_array($models) || $models instanceof \Traversable) {
            foreach($models as $name => $model) {
                $this->setModel($name, $model);
            }
        }
        
        // allows method chaining.
        return $this;
    }
    
    /**
     * Associates the specified model with the specified name.
     * If the manager contained a mapping for the specified name,
     * the old model is replaced by the specified model.
     * 
     * @param string $name name with which the specified model is to be associated.
     * @param string|AbstractModel $model model to be associated with the specified name.
     * @return ModelManager returns itself which allows chaining of operations.
     * @since 1.0.0
     */
    public function addModel($name, $model)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf('first argument should be a string, received %s.', gettype($name)));
        }

        // create a model object using a dynamic class name, 
        if (is_string($model) && class_exists($model)) {
            $model = new $model();
        } 
        
        // make sure the object is of the right class.
        if ($model instanceof AbstractModel) {
            $this->models[$name] = $model;
        }
        
        // allows method chaining.
        return $this;
    }
    
    /**
     * Returns a model object to which the specified name
     * is mapped.
     *
     * A new model object will be created if not yet instantiated,
     * and a reference is stored for possible future calls.
     * 
     * @param string $name the name whose associated model is to be returned.
     * @return object the model to which the specified name is mapped, or null if
     *                this manager contains no mapping for the name.
     * @since 1.0.0
     */
    public function getModel($name)
    {    
        $model = null;
        if (isset($this->models[$name])) {
            $model = $this->models[$name];
        } else {            
            // find the model using the class loader.
            $class = ClassLoader::loadStatic('model', ucfirst($name));
            
            if ($class) {
                // instantiate the model.
                $model = new $class();
                // set the model for future calls.
                $this->addModel($name, $model);
            }
        }
        
        // returns the model.
        return $model;
    }
    
    /**
     * Returns a table object by searching for a model to which the
     * specified name is mapped.
     *
     * @param string $name the name for a model whose table will be returned.
     * @return object the table for a model to which the specified name is mapped, 
     *                or null if this manager contains no mapping for the name.
     * @see ModelManager::getModel($name)
     * @since 1.0.0
     */
    public function getTable($name)
    {
        $table = null;
        
        /* 
         * get the correct table object by getting the model to 
         * which the specified name is mapped.
         */
        $model = $this->getModel($name);
        if (!empty($model)) {
            $table = $model->getTable();
        }
        
        // returns the table.
        return $table;
    }
    
    /**
     * Drop tables for an existing multisite using the models
     * known to the manager.
     *
     * @param array $tableNames array containing names of tables that will be dropped.
     * @return array returns an array with table names that will .
     */
    public function dropTables($tableNames)
    {        
        /*
         * Make sure the models stored by the manager 
         * are iterable before continuing.
         */
        $models = $this->models;
        if (is_array($models) || $models instanceof \Traversable) {
            /*
             * get the name for each models table and add it to 
             * the array table names.
             */
            foreach($models as $model) {
                $table = $model->getTable();
                if ($table instanceof AbstractTable) {
                    $tableNames[] = $table->tableName;
                }
            }
        }
        
        // return all table names.
        return $tableNames;
    }
    
    /**
     * Create tables for a new multisite using the models
     * known to the manager.
     *
     * @param int $blog_id blog id of the new multisite.
     * @since 1.0.0
     */
    public function createTables($blog_id = null)
    {
        // temporarily switch from blog when multisite is enabled.
        if (is_multisite()) {
            switch_to_blog($blog_id);
        }
        
        /*
         * Make sure we the models stored by the manager 
         * are iterable before continuing.
         */
        $models = $this->models;
        if (is_array($models) || $models instanceof \Traversable) {
            /*
             * iterate over all models and create database tables 
             * for the new multisite.
             */
            foreach($models as $model) {
                $model->setup();
            }
        }
        
        // restore original blog when multisite is enabled. 
        if (is_multisite()) {
            restore_current_blog();
        }
    }
}
