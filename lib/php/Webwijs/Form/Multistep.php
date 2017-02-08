<?php

namespace Webwijs\Form;

use Webwijs\Form;
use Webwijs\Form\Element\Submit;

class Multistep extends Form
{
    public $currentStep = 0;
    public $decorators = array(
        'Multistep',
        'Steps',
        'Messages',
        'Form',
    );
    public $defaultSubFormDecorators = array(
        'Elements',
        'Groups',
    );

    public function addSubmitByStep()
    {
        $this->addDisplayGroup('submit', array(), array('decorators' => array('Elements', array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'submit multistep-submit'))))));
        $group = $this->displayGroups['submit'];
        if (!$this->isFirstStep()) {
            $group->addElement($this->getPreviousSubmit());
        }
        if (!$this->isLastStep()) {
            $group->addElement($this->getNextSubmit());
        }
        if ($this->isLastStep()) {
            $group->addElement($this->getLastSubmit());
        }
    }

    public function getPreviousSubmit()
    {
        return new Submit('previous', array('attribs' => array('class' => 'cancel button-primary button-previous', 'label' => 'Vorige'), 'decorators' => array('ViewHelper')));
    }
    public function getNextSubmit()
    {
        return new Submit('next', array('attribs' => array('class' => 'button-primary button-next', 'label' => 'Verder'), 'decorators' => array('ViewHelper')));
    }
    public function getLastSubmit()
    {
        return new Submit('confirm', array('attribs' => array('class' => 'button-primary button-confirm', 'label' => 'Bevestigen'), 'decorators' => array('ViewHelper')));
    }

    public function isValid($values)
    {
        $valid = false;
        $steps = array_keys($this->subForms);
        if (isset($steps[$this->currentStep])) {
            $valid = $this->subForms[$steps[$this->currentStep]]->isValid($values);
        }
        return $valid;
    }

    public function isFirstStep()
    {
        return $this->currentStep == min(array_keys(array_keys($this->subForms)));
    }

    public function isLastStep()
    {
        return $this->currentStep == max(array_keys(array_keys($this->subForms)));
    }
    public function setCurrentStep($step)
    {
        $step = (int) $step;
        $steps = array_keys($this->subForms);
        if (isset($steps[$step])) {
            $this->currentStep = $step;
            return true;
        }
        return false;
    }
    public function previousStep()
    {
        if (!$this->isFirstStep()) {
            $this->currentStep--;
            return true;
        }
        return false;
    }
    public function nextStep()
    {
        if (!$this->isLastStep()) {
            $this->currentStep++;
            return true;
        }
        return false;
    }
    public function getCurrentSubForm()
    {
        return $this->getSubFormByStep($this->currentStep);
    }
    public function getSubFormByStep($step)
    {
        $steps = array_keys($this->subForms);
        return $this->subForms[$steps[$step]];
    }
}
