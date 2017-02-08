<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;
use Webwijs\Form\Decorator\Elements;
use Webwijs\Form\Decorator\Groups;

class Multistep extends Decorator
{
    public function render($content, $view)
    {
        $output = $content;
        $form = $this->element;
        foreach (array_keys($form->subForms) as $stepNumber => $subFormName) {
            $subForm = $form->getSubForm($subFormName);
            if ($stepNumber == $form->currentStep) {
                $output .= $subForm->render();
            }
            else {
                foreach ($subForm->getElements() as $element) {
                    $this->addHiddenElement($element->name, $element->value);
                }
            }
        }
        $this->addHiddenElement('step', $form->currentStep);
        $form->addSubmitByStep();

        $elementsDecorator = new Elements($form);
        $output .= $elementsDecorator->render('', $view);

        $groupsDecorator = new Groups($form);
        $output .= $groupsDecorator->render('', $view);

        return $output;
    }
    public function addHiddenElement($name, $value)
    {
        if (is_array($value)) {
            foreach ($value as $subkey => $subvalue) {
                $this->addHiddenElement($name . '[' . $subkey . ']', $subvalue);
            }
        }
        else {
            $this->element->addElement('hidden', $name, array('value' => $value, 'decorators' => array('ViewHelper')));
        }
    }
}
