<?php

namespace Webwijs\FacetSearch;

use Webwijs\Loader\ClassLoader;

class Factory
{
    protected static $_instance;
    public static function create($type)
    {
        $class = ClassLoader::loadStatic('facetsearch', $type);
        if ($class) {
            self::$_instance = new $class;
            return self::$_instance;
        }
        else {
            // TODO: Use exceptions
            trigger_error('Could not load facetsearch ' . $type);
        }
    }
    public static function getInstance()
    {
        if (self::$_instance) {
            return self::$_instance;
        }
        else {
            // TODO: Use exceptions
            trigger_error('Use create before getInstance');
        }        
    }
}
