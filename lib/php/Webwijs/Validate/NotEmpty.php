<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class NotEmpty extends Validate
{
    public $messages = array('isEmpty' => 'Dit veld moet ingevuld zijn');
    public function isValid($value)
    {       
        if (empty($value)) {
            $this->addError('isEmpty');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('name' => 'required', 'value' => true, 'message' => $this->messages['isEmpty']);       
    }
}
