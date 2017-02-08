<?php

namespace Webwijs\View\Helper;

use Webwijs\View;
use Webwijs\Template\Exception\TemplateNotFoundException;

class Partial
{
    public function partial($template, $vars = null)
    {
        $script = $this->view->locateTemplate($template);
        if (!empty($script)) {
            $view = new View();
            foreach ((array) $vars as $key => $value) {
                $view->$key = $value;
            }

            return $view->render($script);
        }
        else {
            throw new TemplateNotFoundException(
                sprintf('Template not found: %s', print_r($template, 1))
            );
        }
    }
}
