<?php

namespace Webwijs\Validate;

use Webwijs\Validate;

class UploadExtension extends Validate
{
    public $extensions = array();
    public $messages = array('invalid' => 'Het bestand heeft geen geldige extensie');
    public function isValid($value, $context)
    {
        $extension = substr($value['name'], strrpos($value['name'], '.') + 1, strlen($value['name']) - (strrpos($value['name'], '.') + 1));
        if (!in_array($extension, $this->extensions)) {
            $this->addError('invalid');
            return false;
        }
        return true;
    }
}
