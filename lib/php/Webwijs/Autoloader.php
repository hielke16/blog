<?php

namespace Webwijs;

class Autoloader
{
    public $namespace = 'Bekent';
    public $basePath;
    public $resourceTypes = array();
    public function __construct($options)
    {
        isset($options['namespace']) && $this->namespace = $this->_addTrailing($options['namespace'], '_');
        isset($options['basePath']) && $this->basePath = $this->_addTrailing($options['basePath'], '/');
        if (isset($options['resourceTypes'])) {
            foreach ($options['resourceTypes'] as $type => $resourceOptions) {
                $this->resourceTypes[$type]['namespace'] = $this->_addTrailing($resourceOptions['namespace'], '_');
                $this->resourceTypes[$type]['path'] = $this->_addTrailing($resourceOptions['path'], '/');
            }
        }
    }
    public function load($name)
    {
        if (strpos($name, $this->namespace) === 0) {
            $localName = substr($name, strlen($this->namespace));
            foreach ($this->resourceTypes as $resourceType) {
                if (strpos($localName, $resourceType['namespace']) === 0) {
                    if ($this->loadfile($resourceType['path'], substr($localName, strlen($resourceType['namespace'])))) {
                        return true;
                    }
                }
            }
            return $this->loadfile('', $localName);
        }
    }
    public function hasResource($type, $name)
    {
        if (isset($this->resourceTypes[$type])) {

            $fullName = $this->namespace . $this->resourceTypes[$type]['namespace'] . $name;
            if ($this->load($fullName)) {
                return $fullName;
            }
        }
    }
    public function loadfile($path, $name)
    {
        $target = $this->basePath . $path . str_replace('_', '/', $name) . '.php';
        if (file_exists($target)) {
            include_once $target;
            return true;
        }
    }
    protected function _addTrailing($string, $char)
    {
        return rtrim($string, $char) . $char;
    }
}
