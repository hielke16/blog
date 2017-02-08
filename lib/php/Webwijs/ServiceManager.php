<?php

namespace Webwijs;

use Webwijs\Loader\ClassLoader;

class ServiceManager
{
    protected $instances;
    public function get($name)
    {
        if (!isset($this->instances[$name])) {
            $class = ClassLoader::loadStatic('service', ucfirst($name));
            if ($class) {
                $this->instances[$name] = new $class;
            }
            else {
                trigger_error('Service not found: ' . $name);
            }
        }
        return $this->instances[$name];
    }
    public function setService($name, $service)
    {
        $this->instances[$name] = $service;
    }
}
