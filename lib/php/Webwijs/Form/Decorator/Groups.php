<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Groups extends Decorator
{
    public function render($content, $view)
    {
        $output = '';
        if (!empty($this->element->displayGroups)) {
            foreach ($this->element->displayGroups as $group) {
                $output .= $group->render($view);
            }
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
