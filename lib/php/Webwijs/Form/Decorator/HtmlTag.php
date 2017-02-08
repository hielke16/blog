<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class HtmlTag extends Decorator
{
    public $tag;
    public $notEmpty = true;
    public function render($content, $view)
    {
        if (!$this->notEmpty || !empty($content)) {
            if (!empty($this->tag)) {
                $content = '<' . $this->tag . $this->_renderAttribs() . '>' . $content . '</' . $this->tag . '>';
            }
        }
        return $content;
    }
}
