<?php

namespace Webwijs\Form;

use Webwijs\Loader\ClassLoader;

class Element
{
    public $decorators = array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'p')),
        array('HtmlTag', array('tag' => 'dd')),
        array('Label', array('tag' => 'dt')),
    );
    public $validators = array();
    public $helper;
    public $name;
    public $required;
    public $errors = array();
    public $description;
    public $attribs = array();
    public $value;
    public $options;

    /**
     * Ignore flag (used when retrieving values at form level)
     *
     * @var bool
     */
    protected $ignore = false;

    public function __construct($name, $options = null)
    {
        $this->name = $name;
        $this->setOptions($options);
    }
    public function setOptions($options)
    {
        if (is_array($options)) {
            if (isset($options['attribs'])) {
                $this->attribs = array_merge($this->attribs, $options['attribs']);
                unset($options['attribs']);
            }
            foreach ($options as $key => $option) {
                $method = 'set' . ucfirst($key);
                if (($method != 'setOptions') && method_exists($this, $method)) {
                    $this->$method($option);
                }
                else {
                    $this->$key = $option;
                }
            }
        }
    }

    public function render($view)
    {
        $content = '';
        foreach($this->getDecorators() as $decorator) {
            $decorator->view = $view;
            $content = $decorator->render($content, $view);
        }
        return $content;
    }

    public function isValid($value, $context = null)
    {
        $this->value = $value;
        $valid = true;
        if (!empty($value) || $this->required) {
            foreach ($this->getValidators() as $validator) {
                if (!$validator->isValid($value, $context, $this)) {
                    $this->errors = array_merge($this->errors, $validator->errors);
                    $valid = false;
                    break;
                }
            }
        }
        return $valid;
    }
    
    /**
     * Set the name of this element.
     *
     * @param string $name the element name.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function setName($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
    
        $this->name = $name;
    }
    
    /**
     * Returns the name of the element.
     *
     * @return string the element name.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set the value for this element.
     *
     * @param mixed $value the element value.
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    /**
     * Returns the value for this element.
     *
     * @return mixed the element value.
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Set the ignore flag (used when retrieving values at form level)
     *
     * @param  bool $flag
     * @return Zend_Form_Element
     */
    public function setIgnore($flag)
    {
        $this->ignore = (bool) $flag;
        return $this;
    }

    /**
     * Returns the ignore flag (used when retrieving values at form level)
     *
     * @return bool
     */
    public function getIgnore()
    {
        return $this->ignore;
    }
    
    public function getValidators()
    {
        $validators = array();
        $notEmptyValidatorAdded = false;
        foreach ($this->validators as $key => $validator) {
            if (!is_object($validator)) {
                $validator = $this->loadValidator($validator);
            }
            $validators[] = $validator;
            $notEmptyValidatorAdded = $notEmptyValidatorAdded || (substr(get_class($validator), -8) == 'NotEmpty');
        }

        if ($this->required && !$notEmptyValidatorAdded) {
            $requiredOptions = array();
            if (!is_bool($this->required) && !is_numeric($this->required)) {
                $requiredOptions['messages']['isEmpty'] = $this->required;
            }
            array_unshift($validators, $this->loadValidator(array('NotEmpty', $requiredOptions)));
        }
        return $validators;
    }
    public function getDecorators()
    {
        $decorators = array();
        foreach ($this->decorators as $decorator) {
            if (!is_object($decorator)) {
                $decorator = $this->loadDecorator($decorator);
            }
            $decorators[] = $decorator;
        }
        return $decorators;
    }
    public function loadValidator($value)
    {
        if (is_string($value)) {
            $name = $value;
            $options = array();
        }
        else {
            list($name, $options) = $value;
        }
        $class = ClassLoader::loadStatic('validator', $name);
        if ($class) {
            return new $class($options);
        }
    }
    public function loadDecorator($value)
    {
        if (is_string($value)) {
            $name = $value;
            $options = array();
        }
        else {
            list($name, $options) = $value;
        }
        $class = ClassLoader::loadStatic('formdecorator', $name);
        if ($class) {
            return new $class($this, $options);
        }
    }
}
