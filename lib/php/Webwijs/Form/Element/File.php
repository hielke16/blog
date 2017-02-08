<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

class File extends Element
{
    public $helper = 'file';
    public $showOrigFile = true;
    public function isValid($value, $context)
    {
        $valid = true;
        $upload = $_FILES[$this->name];
        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValid($upload, $context)) {
                $this->errors = array_merge($this->errors, $validator->errors);
                $valid = false;
                break;
            }
        }
        if ($valid) {
            $uploadDirectory = dirname(dirname(TEMPLATEPATH)) . '/uploads/tmp/';
            $filename = wp_unique_filename($uploadDirectory, preg_replace('/[^a-zA-Z0-9.]+/', '-', $upload['name']));
            if (move_uploaded_file($upload['tmp_name'], $uploadDirectory . $filename)) {
                $this->value = $filename;
            }
            else {
                $this->errors[] = __('Het bestand kon niet worden verplaatst naar de upload-directory');
                $valid = false;
            }
        }
        elseif (!empty($value)) {
            $this->value = $value;
            $valid = true;
        }
        return $valid;
    }
    public function getValidators()
    {
        $validators = array();
        $notEmptyValidatorAdded = false;
        $uploadValidatorAdded = false;
        foreach ($this->validators as $key => $validator) {
            if (!is_object($validator)) {
                $validator = $this->loadValidator($validator);
            }
            $validators[] = $validator;
            $notEmptyValidatorAdded = $notEmptyValidatorAdded || (substr(get_class($validator), -8) == 'NotEmpty');
            $uploadValidatorAdded = $uploadValidatorAdded || (substr(get_class($validator), -6) == 'Upload');
        }

        if (!$uploadValidatorAdded) {
            array_unshift($validators, $this->loadValidator(array('Upload', array('field' => $this->name))));
        }

        if ($this->required && !$notEmptyValidatorAdded) {
            $requiredOptions = array('field' => $this->name);
            if (!is_bool($this->required) && !is_numeric($this->required)) {
                $requiredOptions['messages']['isEmpty'] = $this->required;
            }
            array_unshift($validators, $this->loadValidator(array('UploadNotEmpty', $requiredOptions)));
        }
        return $validators;
    }
}
