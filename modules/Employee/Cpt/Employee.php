<?php

namespace Module\Employee\Cpt;

use Webwijs\Cpt;

/**
 * The Employee custom post type
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class Employee extends Cpt
{
    /**
     * The post type name
     * @var string $type the post type name
     */
    public $type = 'employee';

    /**
     * Sets the labels and settings for the custom post type
     * @return void
     */
    public function init()
    {
        $this->labels = array(
            'name'                  => __('Werknemers', 'employee'),
            'singular_name'         => __('Werknemer', 'employee'),
            'add_new'               => __('Nieuwe werknemer', 'employee'),
            'add_new_item'          => __('Nieuwe werknemer', 'employee'),
            'edit_item'             => __('Werknemer bewerken', 'employee'),
            'new_item'              => __('Nieuwe werknemer', 'employee'),
            'view_item'             => __('Werknemer bekijken', 'employee'),
            'search_items'          => __('Werknemer zoeken', 'employee'),
            'not_found'             => __('Geen werknemers gevonden', 'employee'),
            'not_found_in_trash'    => __('Geen werknemers gevonden', 'employee'),
            'menu_name'             => __('Werknemers', 'employee')
        );

        $this->settings = array(
            'rewrite'       => false,
            'hierarchical'  => false,
            'public'        => false,
            'show_ui'       => true,
            'supports'      => array('title', 'editor', 'excerpt', 'thumbnail')
        );
    }

    /**
     * Registers the custom post type
     * @param  array $options the options for registering the custom post type
     * @return void
     */
    public static function register($options = null)
    {
        new self($options);
    }

    /**
     * Default query function for retrieving post of the custom post type
     * @param  array $args array of arguments for retrieving the posts
     * @return WP_Query $posts WP Query object containing the retrieved posts
     */
    public static function queryPosts($args = null)
    {
        $defaults = array(
            'post_type' => $this->getType(),
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );
        $args = array_merge( $defaults, (array) $args);

        return query_posts($args);
    }

    /**
     * Returns the post type name
     * @return string $type the post type name
     */
    public function getType()
    {
        return $this->type;
    }
}
