<?php

namespace Webwijs;

abstract class AbstractBootstrap
{
    public function init()
    {
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, '_init') === 0) {
                $this->$method();
            }
        }
    }
}
