<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

class CompilerSelect extends Element
{
    public $helper = 'select';
    public function setOptions($options)
    {
        $this->getOptions();

        return parent::setOptions($options);
    }

    protected function getOptions()
    {
        $this->options = array(
            ''         => __('Automatisch'),
            'forced'   => __('Geforceerd'),
            'disabled' => __('Uitgeschakeld')
        );
    }

}
