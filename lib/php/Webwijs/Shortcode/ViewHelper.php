<?php

namespace Webwijs\Shortcode;

use Webwijs\View;

class ViewHelper
{
    public function __call($name, $args)
    {
        $view = new View();
        return call_user_func_array(array($view, $name), $args);
    }
    public static function __callStatic($name, $args)
    {
        $view = new View();
        return call_user_func_array(array($view, $name), $args);
    }
}
