<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class DateselectNotEmpty extends Validate
{
    public $messages = array('isEmpty' => 'Dit veld moet ingevuld zijn');
    public function isValid($value)
    {
        if (empty($value['day']) || empty($value['month']) || empty($value['year'])) {
            $this->addError('isEmpty');
            return false;
        }
        return true;
    }
    public function getJson()
    {
        return array(
            array('fieldsuffix' => '[day]', 'name' => 'dateselect', 'value' => true, 'message' => 'Selecteer a.u.b. een dag'),
            array('fieldsuffix' => '[month]', 'name' => 'dateselect', 'value' => true, 'message' => 'Selecteer a.u.b. een maand'),
            array('fieldsuffix' => '[year]', 'name' => 'dateselect', 'value' => true, 'message' => 'Selecteer a.u.b. een jaar')
        );
    }
}
