<?php

namespace Theme\Admin\Form;

use Webwijs\Admin\Form;

class SidebarForm extends Form
{
    public function init()
    {
        $this->addElement('text', 'name', array('required' => true, 'label' => 'Naam'));
        $this->addElement('hidden', 'ID', array('required' => false, 'decorators' => array('ViewHelper')));

        $this->addDisplayGroup('fields', array('name'));
        $this->addElement('submit', 'submit', array('attribs' => array('class' => 'button-primary', 'label' => 'Opslaan'), 'decorators' => array('ViewHelper')));
        $this->addDisplayGroup('submit', array('submit'), array('decorators' => array('Elements', array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'submit'))))));
    }
}
