<?php

namespace Webwijs\View\Helper;

use Webwijs\Module\Module;
use Webwijs\Module\ModuleManager;

class ModuleActive extends ModuleExists
{
    /**
     * Returns true if the module associated with the specified name is active.
     *
     * @param string $name the name of the module whose active state to determine.
     * @return bool true if a module was found and the module is active, otherwise false.
     */
    public function moduleActive($name)
    {
        $isActive = false;
        if ($this->moduleExists($name)) {
            $modules  = ModuleManager::getModules();
            $isActive = $modules[$name]->isActive();
        }
        
        return $isActive;
    }
}
