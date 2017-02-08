<?php

namespace Webwijs\Filter;

class Minify
{
    public static function html($content)
    {
        include('Minify/HTML.php');
        return \Minify_HTML::minify($content);
    }
    
}
