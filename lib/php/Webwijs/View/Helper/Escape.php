<?php

namespace Webwijs\View\Helper;

class Escape
{
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
