<?php

namespace Webwijs\View\Shortcode;

use Webwijs\View;

class ViewHelper
{
    public function __call($name, $args)
    {
        $view = new View();
        return $view->$name();
    }
    public static function __callStatic($name, $args)
    {
        $view = new View();
        return $view->$name();
    }
}
