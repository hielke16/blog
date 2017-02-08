<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class SubForms extends Decorator
{
    public function render($content, $view)
    {
        $output = '';
        if (!empty($this->element->subForms)) {
            foreach ($this->element->subForms as $subForm) {
                $output .= $subForm->render($view);
            }
        }
        return $content . $output;
    }
}
