<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Messages extends Decorator
{
    public $placement = 'prepend';
    public function render($content, $view)
    {
        $output = $view->messages();
        $output = $this->_place($output, $content);
        return $output;
    }
}
