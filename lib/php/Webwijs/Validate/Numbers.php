<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Numbers extends Validate
{
    public $messages = array('noNumber' => 'Dit is geen geldig getal');
    public function isValid($value)
    {
        if (!preg_match('/^\d*$/', $value)) {
            $this->addError('noNumber');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('name' => 'digits', 'value' => true, 'message' => $this->messages['noNumber']);
    }
}

