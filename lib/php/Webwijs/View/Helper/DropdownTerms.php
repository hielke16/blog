<?php

namespace Webwijs\View\Helper;

use Webwijs\Util\Arrays;

class DropdownTerms
{
    public function dropdownTerms($args = null)
    {
        $defaults = array(
            'taxonomy' => '',
            'field' => 'term_id',
            'term_args' => array(
                'hide_empty' => true,
            ),
            'show_option_none' => __('--Selecteer'),
            'class' => '',
            'selected' => '',
            'name' => ''
        );
        $args = Arrays::addAll($defaults, (array) $args);
        $terms = get_terms($args['taxonomy'], $args['term_args']);
        
        ob_start();
        ?>
        <select class="<?php echo $args['class'] ?>" name="<?php echo $args['name'] ?>">
            <?php if ($args['show_option_none']): ?>
            <option value=""><?php echo $args['show_option_none'] ?></option>
            <?php endif ?>
            <?php foreach ($terms as $term): ?>
            <?php $value = $term->{$args['field']} ?>
            <option value="<?php echo $value ?>" <?php selected($args['selected'], $value) ?>><?php echo esc_attr($term->name) ?></option>
            <?php endforeach ?>
        </select>
        <?php
        return ob_get_clean();
    }
}
