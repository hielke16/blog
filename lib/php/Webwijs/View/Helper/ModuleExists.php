<?php

namespace Webwijs\View\Helper;

use Webwijs\Module\ModuleManager;

class ModuleExists
{
    /**
     * Returns true if a module was found for the specified name.
     *
     * @param string $name the name of the module to find.
     * @return bool true if a module was found, otherwise false.
     */
    public function moduleExists($name)
    {
        $modules = ModuleManager::getModules();
        return (is_string($name) && isset($modules[$name]));
    }
}
