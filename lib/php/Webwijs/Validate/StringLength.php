<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class StringLength extends Validate
{
    public $messages = array(
        'stringLengthInvalid' => "Onverwachte waarde",
        'stringLengthTooShort' => "Het aantal tekens is kleiner dan toegestaan",
        'stringLengthTooLong' => "Het aantal tekens is groter dan toegestaan",
    );
    public $min;
    public $max;
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->addError('stringLengthInvalid');
            return false;
        }
        $length = strlen($value);
        if ($this->min && ($length < $this->min)) {
            $this->addError('stringLengthTooShort');
            return false;
        }
        if ($this->max && ($length > $this->max)) {
            $this->addError('stringLengthTooLong');
            return false;
        }
        return true;
    }
}
