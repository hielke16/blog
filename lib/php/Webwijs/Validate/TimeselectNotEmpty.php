<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class TimeselectNotEmpty extends Validate
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
    public function getJson()
    {
        return array(
            array('fieldsuffix' => '[hours]', 'name' => 'timeselect', 'value' => true, 'message' => 'Selecteer a.u.b. een uur'),
            array('fieldsuffix' => '[mins]', 'name' => 'timeselect', 'value' => true, 'message' => 'Selecteer a.u.b. een minuut'),
        );
    }
}
