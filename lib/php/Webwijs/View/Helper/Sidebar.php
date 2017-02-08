<?php

namespace Webwijs\View\Helper;

class Sidebar
{
    function sidebar($name)
    {
        ob_start();
        dynamic_sidebar($name);
        return ob_get_clean();
    }
}
