<?php

namespace Theme\Helper;

use Webwijs\Application;

class LayoutContainerClass
{
    public function layoutContainerClass()
    {
        return Application::getServiceManager()->get('PageLayout')->getCurrentLayout()->getContainerClass();
    }
}
