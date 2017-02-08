<?php

namespace Module\ContentBlock\Widget;

use WP_Widget;

use Webwijs\View;

/**
 * Widget that displays the contents of a 'content block' post type.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
class ContentBlock extends WP_Widget
{
    /**
     * Construct a new ContentBlock.
     */
    public function __construct()
    {
        $options = array('classname' => 'widget-contentblock', 'description' => 'Toont de inhoud van het geselecteerde content block.');
        parent::__construct('widget_content_block', 'Contentblock', $options);
    }

    /**
     * The form that is displayed in wp-admin and is used to save the settings for this widget.
     *
     * @param array $instance the form values stored in the database.
     */
    public function form($instance)
    {
        $defaults = array(
            'classname' => '',
            'block_id'  => '',
        );
        $instance = array_merge($defaults, (array) $instance);
        $view = new View();
    ?>
        <p><label>Contentblock<br />
            <?php echo $view->dropdownPosts(array(
                'post_types'       => 'webwijs_contentblock',
                'name'             => $this->get_field_name('block_id'),
                'class'            => 'widefat',
                'selected'         => $instance['block_id'],
                'show_option_none' => false,
            )) ?>
        </label></p>

        <p><label>CSS-class voor container<br />
            <?php echo $view->formText($this->get_field_name('classname'), $instance['classname'], array('class' => 'widefat')) ?>
        </label></p>
    <?php
    }
    
    /**
     * Filter and normalize the form values before they are updated.
     *
     * @param array $new_instance the values entered in the form.
     * @param array $old_instance the previous form values stored in the database.
     * @return array the filtered form values that will replace the old values.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['block_id']  = (isset($new_instance['block_id']) && is_numeric($new_instance['block_id'])) ? $new_instance['block_id'] : 0;
        $instance['classname'] = (isset($new_instance['classname']) && is_string($new_instance['classname'])) ? $new_instance['classname'] : '';
        
        return $instance;
    }
    
    /**
     * Displays the widget using values retrieved from the database.
     *
     * @param array $args an array containing (generic) arguments for all widgets.
     * @param array $instance array the values stored in the database. 
     */
    public function widget($args, $instance)
    {
        $defaults = array(
            'block_id'   => '',
            'classname' => '',
        );
        $instance = array_merge($defaults, $instance);
    
        $view = new View();
        if (is_string($instance['classname']) && strlen($classname) !== 0) {
            $args['before_widget'] = preg_replace('/class="/', 'class="' . $instance['classname'] . ' ', $args['before_widget'], 1);
        }

        if (($post = get_post($instance['block_id'])) !== null) {
            echo $args['before_widget'];
            echo $view->partial('partials/widgets/contentblock.phtml', array_merge($args, $instance, array('post' => $post)));
            echo $args['after_widget'];
        }
    }
}
