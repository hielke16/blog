<?php

namespace Webwijs\Form;

use Webwijs\Loader\ClassLoader;
use Webwijs\Form\Element;

class DisplayGroup
{
    public $name;
    public $decorators = array(
        'Elements',
        array('HtmlTag', array('tag' => 'dl')),
        array('Description', array('tag' => 'p')),
        'Fieldset',
    );
    public $elements = array();
    public function __construct($name, $options = null)
    {
        $this->name = $name;
        $this->setOptions($options);
    }
    public function setOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $this->$key = $option;
            }
        }
    }
    public function addElement($element, $name = null, $options = null)
    {
        if (is_object($element)) {
            is_null($name) && $name = $element->name;
        } else {
            $elementType = $element;
            $elementClass = ClassLoader::loadStatic('formelement', ucfirst($elementType));
            if ($elementClass) {
                $element = new $elementClass($name);
            } else {
                $element = new Element($name);
                $element->helper = $elementType;
            }
        }
        
        if (!empty($this->defaultElementDecorators) && empty($options['decorators'])) {
            $options['decorators'] = $this->defaultElementDecorators;
        }
        
        if (!empty($options) && is_array($options)) {
            $element->setOptions($options);
        }
        
        $this->elements[$name] = $element;
    }
    public function removeElement($name)
    {
        unset($this->elements[$name]);
    }
    public function getElement($name)
    {
        $element = null;
        if (isset($this->elements[$name])) {
            $element = $this->elements[$name];
        }
        return $element;
    }
    public function getElements()
    {
        return $this->elements;
    }
    public function render($view)
    {
        $content = '';
        foreach($this->getDecorators() as $decoratorConfig) {
            list($decoratorName, $decoratorOptions) = $decoratorConfig;
            $decoratorClass = 'Webwijs\Form\Decorator\\' . $decoratorName;
            $decorator = new $decoratorClass($this, $decoratorOptions);
            $content = $decorator->render($content, $view);
        }
        
        return $content;
    }
    public function isValid($values)
    {
        $valid = true;
        foreach ($this->elements as $name => $element) {
            $value = isset($values[$name]) ? $values[$name] : null;
            $valid = $element->isValid($value, $values) && $valid;
        }
        return $valid;
    }
    public function getDecorators()
    {
        $decorators = array();
        foreach ($this->decorators as $decoratorOptions) {
            if (is_string($decoratorOptions)) {
                $decoratorOptions = array($decoratorOptions, array());
            }
            $decorators[] = $decoratorOptions;
        }
        return $decorators;
    }
    public function getValues()
    {
        $values = array();
        foreach ($this->elements as $name => $element) {
            $values[$name] = $element->value;
        }
        return $values;
    }
    public function __clone()
    {
        $elements = array();
        foreach ($this->elements as $name => $element) {
            $elements[$name] = clone $element;
        }
        $this->elements = $elements;
   }
}
