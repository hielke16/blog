<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Fieldset extends Decorator
{
    public function render($content, $view)
    {
        $output = '<fieldset id="' . $this->element->name . '-fieldset">';
        if (!empty($this->element->title)) {
            $output .= '<h3>' . $this->element->title . '</h3>';
        }
        $output .= $content . '</fieldset>';
        return $output;
    }
}
