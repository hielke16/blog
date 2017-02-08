<?php

namespace Theme\Admin\Controller\Form\Element;

use RGFormsModel;

use Webwijs\Form\Element;

/**
 * The FormSelect renders a select element containing forms created with GravityForms.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
class FormSelect extends Element
{
    /**
     * The name of a helper that renders this element.
     *
     * @var string
     */
    public $helper = 'select';
    
    /**
     * Set the options for this element.
     *
     * @param array a collection consisting of key-value pairs.
     */
    public function setOptions($options)
    {
        $defaults = array(
            'active' => null,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $args = array_merge($defaults, (array) $options);

        $this->options = array();
        if (class_exists('RGFormsModel')) {
            // populate options property with forms.
            $forms = RGFormsModel::get_forms($args['active'], $args['orderby'], $args['order']);
            foreach ($forms as $form) {
                $this->options[$form->id] = esc_attr($form->title);
            }
        }
        
        return parent::setOptions($options);
    }
}
