<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Postcode extends Validate
{
    public $messages = array('noValidPostcode' => 'Dit is geen geldige postcode');
    public function isValid($value)
    {
        if(!preg_match("/^[0-9]{4}\s*[a-z]{2}$/i", trim($value))) {
            $this->addError('noValidPostcode');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('name' => 'postcode', 'value' => true, 'message' => $this->messages['noValidPostcode']);
    }
}
