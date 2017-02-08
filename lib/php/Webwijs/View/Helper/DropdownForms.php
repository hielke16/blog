<?php

namespace Webwijs\View\Helper;

class DropdownForms
{
    public function dropdownForms($args = null)
    {
        $defaults = array(
            'active' => null,
            'orderby' => 'title',
            'order' => 'ASC',
            'selected' => null,
            'name' => 'form_id',
            'class' => '',
        );
        $args = array_merge($defaults, (array) $args);
        $forms = array();

        if (class_exists('RGFormsModel')) {
            $forms = \RGFormsModel::get_forms($args['active'], $args['orderby'], $args['order']);
        }
        ob_start();
        ?>
        <select name="<?php echo $args['name'] ?>" class="<?php echo $args['class'] ?>">
            <option value="">--Selecteer</option>
            <?php foreach ($forms as $form): ?>
            <option value="<?php echo $form->id ?>" <?php selected($args['selected'], $form->id) ?>><?php echo esc_attr($form->title) ?></option>
            <?php endforeach ?>
        </select>
        <?php
        return ob_get_clean();
    }
}

