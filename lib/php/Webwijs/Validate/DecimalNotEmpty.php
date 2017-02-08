<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class DecimalNotEmpty extends Validate
{
    public $messages = array('isEmpty' => 'Dit veld moet ingevuld zijn');
    public function isValid($value)
    {
        if (empty($value['number'])) {
            $this->addError('isEmpty');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array('fieldsuffix' => '[number]', 'name' => 'required', 'value' => true, 'message' => $this->messages['isEmpty']);
    }
}
