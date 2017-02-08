<?php
namespace Theme\Admin;

use Theme\Bootstrap;

class Ajax
{
    public static function compile()
    {
        Bootstrap::compileSCSS(true);
        exit;
    }
}
