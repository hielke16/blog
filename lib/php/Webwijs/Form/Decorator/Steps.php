<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Steps extends Decorator
{
    public $placement = 'prepend';
    public function render($content, $view)
    {
        $output = '<ul class="steps">';
        $form = $this->element;
        foreach (array_keys($form->subForms) as $stepNumber => $subFormName) {
            $subForm = $form->getSubForm($subFormName);
            $buttonAttribs = array('label' => $subForm->label);
            $stepAttribs = array();
            if ($stepNumber < $form->currentStep) {
                $buttonAttribs['class'] = 'cancel';
            }
            else {
                if ($stepNumber == $form->currentStep) {
                    $stepAttribs['class'] = 'current';
                    $buttonAttribs['class'] = 'cancel';
                }
                else {
                    $stepAttribs['class'] = 'inactive';
                    $buttonAttribs['disabled'] = 'disabled';
                }
            }
            $output .= '<li' . $this->_renderAttribs($stepAttribs) . '>' . $view->formSubmit('gotostep[' . $stepNumber . ']', 1, $buttonAttribs, null) . '</li>';
        }
        $output .= '</ul>';

        $content = '<h2 class="step-title">' . $view->escape($form->getCurrentSubForm()->label) . '</h2>' . $content;
        $output = $this->_place($output, $content);
        return $output;
    }
}
