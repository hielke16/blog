<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Identical extends Validate
{
    public $refField;
    public $messages = array('noMatch' => 'Dit velden komen niet overeen');
    public function isValid($value, $context)
    {
        if ($value != $context[$this->refField]) {
            $this->addError('noMatch');
            return false;
        }
        return true;
    }
}
