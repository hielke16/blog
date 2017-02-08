<?php

namespace Webwijs\Widget;

use Webwijs\View;

class SectionNav extends \WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'section-nav');
        parent::__construct('section-nav', __('Section Navigation'), $widget_ops);
    }

    public function widget($args, $instance)
    {
        $instance['post_types'] = explode(',', $instance['post_types']);
        $instance['taxonomies'] = explode(',', $instance['taxonomies']);

        echo $args['before_widget'];
        $view = new View();
        echo $view->sectionNav($instance);
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        $new_instance['max_level'] = max($new_instance['min_level'], $new_instance['max_level']);
        $new_instance['post_types'] = implode(',', array_keys((array) $new_instance['post_types']));
        $new_instance['taxonomies'] = implode(',', array_keys((array) $new_instance['taxonomies']));
        return $new_instance;
    }

    function form($instance)
    {
        $defaults = array(
            'post_types' => array_diff(get_post_types(array('public' => true)), array('attachment')),
            'taxonomies' => array('category'),
            'min_level' => 0,
            'max_level' => 2,
        );
        $instance = array_merge($defaults, $instance);
        if (!is_array($instance['post_types'])) {
            $instance['post_types'] = explode(',', $instance['post_types']);
        }
        if (!is_array($instance['taxonomies'])) {
            $instance['taxonomies'] = explode(',', $instance['taxonomies']);
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('min_level') ?>">Minimum niveau</label>
            <select id="<?php echo $this->get_field_id('min_level') ?>" name="<?php echo $this->get_field_name('min_level') ?>">
                <?php for ($level = 0; $level <= 4; $level++): ?>
                <option value="<?php echo $level ?>" <?php selected($level, $instance['min_level']) ?>><?php echo $level ?></option>
                <?php endfor ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('max_level') ?>">Maximum niveau</label>
            <select id="<?php echo $this->get_field_id('max_level') ?>" name="<?php echo $this->get_field_name('max_level') ?>">
                <?php for ($level = 0; $level <= 4; $level++): ?>
                <option value="<?php echo $level ?>" <?php selected($level, $instance['max_level']) ?>><?php echo $level ?></option>
                <?php endfor ?>
            </select>
        </p>
        <p>
            <label>Content types</label><br />
            <?php foreach (array_diff(get_post_types(array('public' => true)), array('attachment')) as $postType): ?>
            <?php $postTypeObject = get_post_type_object($postType) ?>
            <label>
                <input type="checkbox" <?php checked(in_array($postType, (array) $instance['post_types'])) ?> name="<?php echo $this->get_field_name('post_types'); ?>[<?php echo $postType ?>]" />
                <?php echo $postTypeObject->labels->singular_name ?>
            </label><br />
            <?php endforeach ?>
        </p>
        <p>
            <label>Taxonomieën</label><br />
            <?php foreach (get_taxonomies(array('public' => true), 'objects') as $taxonomy): ?>
            <label>
                <input type="checkbox" <?php checked(in_array($taxonomy->name, (array) $instance['taxonomies'])) ?> name="<?php echo $this->get_field_name('taxonomies'); ?>[<?php echo $taxonomy->name ?>]" />
                <?php echo $taxonomy->labels->name ?>
            </label><br />
            <?php endforeach ?>
        </p>
        <?php
    }
}
