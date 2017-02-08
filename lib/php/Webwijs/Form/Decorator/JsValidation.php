<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;
use Webwijs\Form\Multistep;

class JsValidation extends Decorator
{
    public $rules = array();
    public $messages = array();
    public function render($content, $view)
    {
        if ($this->element instanceof Multistep) {
            $this->addRules($this->element->getCurrentSubForm());
        }
        else {
            $this->addRules($this->element);
        }
        wp_enqueue_script('jquery-validation', get_bloginfo('template_directory') . '/assets/lib/js/jquery.validate.js', array('jquery'));
        wp_enqueue_script('jquery-validation-extras', get_bloginfo('template_directory') . '/assets/lib/js/webwijs.jquery.validate.extras.js', array('jquery'));
        add_action('wp_head', array(&$this, 'renderJs'), 100);
        return $content;
    }
    public function addRules($form)
    {
        foreach ($form->getElements() as $element) {

            foreach ($element->getValidators() as $validator) {
                $json = $validator->getJson($element);
                if ($json) {
                    if (isset($json['name'])) {
                        $field = $element->name;
                        !empty($json['fieldsuffix']) && $field .= $json['fieldsuffix'];
                        $this->rules[$field][$json['name']] = $json['value'];
                        $this->messages[$field][$json['name']] = $json['message'];
                    }
                    else {
                        foreach ($json as $inputRule) {
                            $field = $element->name;
                            !empty($inputRule['fieldsuffix']) && $field .= $inputRule['fieldsuffix'];
                            $this->rules[$field][$inputRule['name']] = $inputRule['value'];
                            $this->messages[$field][$inputRule['name']] = $inputRule['message'];
                        }
                    }
                }
            }
        }
    }
    public function renderJs()
    {
        ?>
    <script type="text/javascript">
    (function($){
        jQuery().ready(function($) {
            $('#<?php echo $this->element->getId() ?>').validate({
                'rules': <?php echo json_encode($this->rules) ?>,
                'messages': <?php echo json_encode($this->messages) ?>,
                'errorPlacement': function(error, element) {
                    error.appendTo(element.parents('dd'));
                }
            });
        });
        $.validator.messages.required = 'Dit veld moet ingevuld zijn'
        $.validator.messages.email = 'Dit is geen geldig e-mailadres'
        $.validator.messages.digits = 'Alleen cijfers zijn toegestaan'
    })(jQuery);

    </script>

        <?php
    }
}
