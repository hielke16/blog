<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class UploadNotEmpty extends Validate
{
    public $field;
    public $messages = array('isEmpty' => 'Dit veld moet ingevuld zijn');
    public function isValid($value, $context)
    {
        if ((@$value['error'] == 4) && empty($context[$this->field])) {
            $this->addError('isEmpty');
            return false;
        }
        return true;
    }
}
