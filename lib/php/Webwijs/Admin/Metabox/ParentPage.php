<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class ParentPage extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'parent_id',
        'title'    => 'Onderdeel van',
        'context'  => 'side',
        'priority' => 'low',
    );

    /**
     * Additional options used to display the metabox.
     *
     * @var array
     */
    public $options = null;

    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $defaults = array(
            'post_type' => 'page',
            'template' => null,
            'hierarchical' => 1,
            'selected' => $post->post_parent,
            'show_option_none' => '--',
            'name' => 'parent_id',
        );
        $options = array_merge($defaults, (array) $this->options);
        if (!empty($options['template'])) {
            $options['meta_key'] = '_wp_page_template';
            $options['meta_value'] = $options['template'];
            unset($options['template']);
        }
        wp_dropdown_pages($options);
    }

    /**
     * Allow built-in functions of WordPress to save the parent page.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {}
    
    /**
     * Set one or more options for this metabox.
     *
     * @param array $options array containing options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
