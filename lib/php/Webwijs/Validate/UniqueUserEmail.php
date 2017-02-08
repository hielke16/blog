<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class UniqueUserEmail extends Validate
{
    public $messages = array('exists' => 'Er bestaat al een gebruiker met dit e-mailadres');
    public function isValid($value, $context)
    {
        $valid = true;
        $userId = email_exists($value);
        if ($userId && (!isset($context['ID']) || ($context['ID'] != $userId))) {
            $this->addError('exists');
            $valid = false;
        }
        else {
            $userId = username_exists($value);
            if ($userId && (!isset($context['ID']) || ($context['ID'] != $userId))) {
                $this->addError('exists');
                $valid = false;
            }
        }
        return $valid;
    }
}
