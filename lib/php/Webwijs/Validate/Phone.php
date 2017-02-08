<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Phone extends Validate
{
    public $messages = array('noValidPhone' => 'Dit is geen geldig telefoonnummer');
    public function isValid($value)
    {
        if (!preg_match('/(\d)?(\s|-)?$/', $value)) {
            $this->addError('noValidPhone');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('name' => 'phone', 'value' => true, 'message' => $this->messages['noValidPhone']);
    }
}
