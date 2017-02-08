<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Timeselect extends Validate
{
    public $messages = array('isEmpty' => 'Dit veld moet ingevuld zijn');
    public function isValid($value)
    {
        if (empty($value['hours']) || empty($value['mins'])) {
            $this->addError('isEmpty');
            return false;
        }
        return true;
    }
}
