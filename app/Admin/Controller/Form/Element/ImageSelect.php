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
class ImageSelect extends Element
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
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $args = array_merge($defaults, (array) $options);

        $this->options = array();

        $posts_array = get_posts( $args );
        foreach ($posts_array as $post) {
            $this->options[$post->ID] = esc_attr($post->post_title);
        }
        
        return parent::setOptions($options);
    }
}
