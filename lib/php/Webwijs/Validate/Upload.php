<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class Upload extends Validate
{
    public $errorMap = array(
        1 => 'ini_size', 2 => 'form_size', 3 => 'partial', 5 => 'no_tmp_dir', 6 => 'cant_write', 7 => 'extension'
    );
    public $messages = array(
        'ini_size' => 'Het bestand is groter dan toegestaan',
        'form_size' => 'Het bestand is groter dan toegestaan',
        'partial' => 'De upload is voortijdig afgebroken',
        'no_tmp_dir' => 'Er is een technisch probleem opgetreden',
        'cant_write' => 'Er is een technisch probleem opgetreden',
        'extension' => 'Er is een technisch probleem opgetreden',
        'invalid' => 'Er is een technisch probleem opgetreden',
    );
    public function isValid($value)
    {
        if (!is_array($value) || !isset($value['error']) || !is_scalar($value['error'])) {
            $this->addError('invalid');
            return false;
        }
        elseif (isset($this->errorMap[$value['error']])) {
                $this->addError($this->errorMap[$value['error']]);
                return false;
        }
        elseif ($value['error'] == 4) {
            return false;
        }
        return true;
    }
}
