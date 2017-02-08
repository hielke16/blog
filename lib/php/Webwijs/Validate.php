<?php

namespace Webwijs;

class Validate
{
    public $errors = array();
    public $messages = array();
    public function __construct($options = null)
    {
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
    public function isValid($value)
    {
        return true;
    }
    public function addError($code)
    {
        if (isset($this->messages[$code])) {
            $this->errors[$code] = $this->messages[$code];
        }
        else {
            $this->errors[$code] = $code;
        }
    }
    public function getJson()
    {
    }
}
