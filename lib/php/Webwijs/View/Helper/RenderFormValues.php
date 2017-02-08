<?php

namespace Webwijs\View\Helper;

class RenderFormValues
{
    public $form;
    public $formDecorators = array('Elements', 'Groups', 'SubForms');
    public $subFormDecorators = array('Elements', 'Groups', array('Header', array('tag' => 'h3')));
    public $displayGroupDecorators = array('Elements', array('HtmlTag', array('tag' => 'table')), array('Header', array('tag' => 'h3', 'attr' => 'title')));
    public $elementDecorators = array('ElementValue', array('HtmlTag', array('tag' => 'td')), array('ElementValueLabel', array('tag' => 'td')), array('HtmlTag', array('tag' => 'tr')));
    public function renderFormValues($form)
    {
        $this->form = clone $form;
        return $this;
    }

    public function __tostring()
    {
        $this->_prepareForm($this->form);
        return (string) $this->form;
    }

    public function setFormDecorators($dec)
    {
        $this->formDecorators = $dec;
        return $this;
    }

    public function setSubFormDecorators($dec)
    {
        $this->subFormDecorators = $dec;
        return $this;
    }

    public function setDisplayGroupDecorators($dec)
    {
        $this->displayGroupDecorators = $dec;
        return $this;
    }

    public function setElementDecorators($dec)
    {
        $this->elementDecorators = $dec;
        return $this;
    }

    protected function _prepareForm($form)
    {
        $form->decorators = $this->formDecorators;
        foreach ($form->subForms as $subForm) {
            $this->_prepareSubForm($subForm);
        }
        foreach ($form->displayGroups as $group) {
            $this->_prepareDisplayGroup($group);
        }
        foreach ($form->elements as $element) {
            $this->_prepareElement($element);
        }
    }

    protected function _prepareSubForm($subForm)
    {
        $subForm->decorators = $this->subFormDecorators;
        foreach ($subForm->displayGroups as $group) {
            $this->_prepareDisplayGroup($group);
        }
        foreach ($subForm->elements as $element) {
            $this->_prepareElement($element);
        }
    }
    protected function _prepareDisplayGroup($group)
    {
        $group->decorators = $this->displayGroupDecorators;
        foreach ($group->elements as $element) {
            $this->_prepareElement($element);
        }
    }
    protected function _prepareElement($element)
    {
        $element->decorators = $this->elementDecorators;
    }
}
