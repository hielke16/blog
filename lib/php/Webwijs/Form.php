<?php

namespace Webwijs;

use Webwijs\View;
use Webwijs\Loader\ClassLoader;
use Webwijs\Form\Element;
use Webwijs\Form\DisplayGroup;
use Webwijs\Form\SubForm;

class Form
{
    public $name;
    public $attribs = array(
        'action' => '',
        'method' => 'post',
    );
    public $elements = array();
    public $displayGroups = array();
    public $subForms = array();
    public $decorators = array(
        'Elements',
        'Groups',
        'Form',
    );
    public $nonce;
    public static $instanceCount = 0;
    public function __construct($name = null, $options = null)
    {
        $this->name = $name;
        self::$instanceCount++;
        $this->setOptions($options);
        $this->init();
    }
    public function init()
    {
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
        }
        else {
            $elementType = $element;
            $elementClass = ClassLoader::loadStatic('formelement', ucfirst($elementType));
            if ($elementClass) {
                $element = new $elementClass($name);
            }
            else {
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
        foreach ($this->displayGroups as $group) {
            $group->removeElement($name);
        }
        foreach ($this->subForms as $subForm) {
            $subForm->removeElement($name);
        }
    }
    public function getElement($name)
    {
        $element = null;
        if (isset($this->elements[$name])) {
            $element = $this->elements[$name];
        }
        else {
            foreach ($this->displayGroups as $group) {
                $element = $group->getElement($name);
                if ($element) {
                    break;
                }
            }
            if (is_null($element)) {
                foreach ($this->subForms as $subForm) {
                    $element = $subForm->getElement($name);
                    if ($element) {
                        break;
                    }
                }
            }
        }
        return $element;
    }
    
    /**
     * Returns a collection of {@link Element} instances contained by this form and the display groups.
     *
     * @return arary a collection of {@link Element} instances.
     */
    public function getElements()
    {
        $elements = $this->elements;
        foreach ($this->displayGroups as $displayGroup) {
            $elements = array_merge($elements, $displayGroup->getElements());
        }
        foreach ($this->subForms as $subForm) {
            $elements = array_merge($elements, $subForm->getElements());
        }

        return $elements;
    }

    /**
     * Add a new display group to this form.
     *
     * @param DisplayGroup|string a display group or the name for a new display group.
     * @param array $elements (optional) a collection of elements that will be added to the display group.
     * @param array $options (optional) options that will be set for the display group.
     */
    public function addDisplayGroup($group, array $elements = array(), array $options = array())
    {
        if (!$group instanceof DisplayGroup) {
            $group = new DisplayGroup($group);
            if (!empty($this->defaultGroupDecorators) && empty($options['decorators'])) {
                $options['decorators'] = $this->defaultGroupDecorators;
            }
            if (!empty($this->defaultElementDecorators) && empty($options['defaultElementDecorators'])) {
                $options['defaultElementDecorators'] = $this->defaultElementDecorators;
            }
        }

        if (!empty($elements)) {
            foreach ($elements as $elementName) {
                if (isset($this->elements[$elementName])) {
                    $group->addElement($this->elements[$elementName]);
                    unset($this->elements[$elementName]);
                }
            }
        }
        $group->setOptions($options);
        $this->displayGroups[$group->name] = $group;
    }
    
    /**
     * Removes if present the display group from this form with the specified name.
     *
     * @param string $name the name of a display group to remove.
     * @return DisplayGroup|null the display group that was removed, or null if no display group was removed.
     */
    public function removeDisplayGroup($name)
    {
        $group = null;
        if ($this->hasDisplayGroup($name)) {
            $group = $this->displayGroups[$name];
            unset($this->displayGroups[$name]);            
        }
    
        return $group;
    }
    
    /**
     * Returns true if a display group exists for the specified name.
     *
     * @param string $name the name of the display group whose presence will be tested.
     * @return bool true if a display group was found, otherwise flse.
     */
    public function hasDisplayGroup($name)
    {
        return isset($this->displayGroups[$name]);
    }
    
    /**
     * Returns if present a display group for the specified name.
     *
     * @return DisplayGroup|null a display group, or null on failure.
     */
    public function getDisplayGroup($name)
    {
        return ($this->hasDisplayGroup($name)) ? $this->displayGroups[$name] : null;
    }

    public function addSubForm($subForm, $groups = null, $options = null)
    {
        if (!is_object($subForm)) {
            $subForm = new SubForm($subForm);
            if (!empty($this->defaultSubFormDecorators) && empty($options['decorators'])) {
                $options['decorators'] = $this->defaultSubFormDecorators;
            }
        }

        if (!empty($groups)) {
            foreach ($groups as $groupName) {
                if (isset($this->displayGroups[$groupName])) {
                    $subForm->addDisplayGroup($this->displayGroups[$groupName]);
                    unset($this->displayGroups[$groupName]);
                }
            }
        }
        $subForm->setOptions($options);
        $this->subForms[$subForm->name] = $subForm;
    }

    public function removeSubForm($name)
    {
        unset($this->subForms[$name]);
    }
    public function getSubForm($name)
    {
        return $this->subForms[$name];
    }

    public function setNonce($name)
    {
        $this->nonce = $name;
        $this->addElement('nonce', 'nonce', array('options' => array('nonce' => $name), 'decorators' => array('ViewHelper')));
    }
    public function verifyNonce()
    {
        return !empty($this->nonce) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], $this->nonce);
    }

    public function render($view = null)
    {
        $view = ($view !== null) ? $view : new View();
        
        $content = '';
        foreach($this->getDecorators() as $decoratorConfig) {
            list($decoratorName, $decoratorOptions) = $decoratorConfig;
            $decoratorClass = 'Webwijs\Form\Decorator\\' . $decoratorName;
            $decorator = new $decoratorClass($this, $decoratorOptions);
            $content = $decorator->render($content, $view);
        }
        
        return $content;
    }

    public function __tostring()
    {
        return $this->render();
    }

    public function isValid($values)
    {
        $valid = true;
        foreach ($this->elements as $name => $element) {
            $value = isset($values[$name]) ? $values[$name] : null;
            $valid = $element->isValid($value, $values) && $valid;
        }
        foreach ($this->displayGroups as $group) {
            $valid = $group->isValid($values) && $valid;
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
    /**
     * Return a collection containing key-value pairs for all elements contained by this form and display groups.
     *
     * @return array an associative array containing element values and the keys are the element names.
     */
    public function getValues()
    {
        $values = array();
        foreach ($this->elements as $name => $element) {
            $values[$name] = $element->value;
        }
        foreach ($this->displayGroups as $group) {
            $values = array_merge($values, $group->getValues());
        }
        foreach ($this->subForms as $subForm) {
            $values = array_merge($values, $subForm->getValues());
        }
        return $values;
    }
    
    /**
     * Returns if present the value for the {@link Element} instance associated with the specified name.
     *
     * @param string $name the name associated with the element whose value to return.
     * @return mixed|null the value for the element, or null on failure.
     */
    public function getValue($name)
    {
        $element = $this->getElement($name);
        return ($element instanceof Element) ? $element->value : null;
    }
    
    public function setDefault($fieldname, $value)
    {
        $element = $this->getElement($fieldname);
        if ($element) {
            $element->setValue($value);
        }
    }
    public function setDefaults($values)
    {
        foreach ($values as $fieldName => $value) {
            $this->setDefault($fieldName, $value);
        }
    }
    public function getId()
    {
        return str_replace('_', '-', strtolower(get_class($this))) . '-' . self::$instanceCount;
    }

    public function __clone()
    {
        $elements = array();
        foreach ($this->elements as $name => $element) {
            $elements[$name] = clone $element;
        }
        unset($this->elements);
        $this->elements = $elements;

        $displayGroups = array();
        foreach ($this->displayGroups as $name => $group)  {
            $displayGroups[$name] = clone $group;
        }
        unset($this->displayGroups);
        $this->displayGroups = $displayGroups;


        $subForms = array();
        foreach ($this->subForms as $name => $subForm) {
            $subForms[$name] = clone $subForm;
        }
        unset($this->subForms);
        $this->subForms = $subForms;
   }
}
