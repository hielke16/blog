<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Email extends Validate
{
    public $messages = array('noValidEmail' => 'Dit is geen geldig e-mailadres');
    public function isValid($value)
    {
        if (!is_email($value)) {
            $this->addError('noValidEmail');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('name' => 'email', 'value' => true, 'message' => $this->messages['noValidEmail']);
    }
}
