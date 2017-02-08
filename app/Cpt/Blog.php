<?php
namespace Theme\Cpt;
use Webwijs\Cpt;
/**
 * A custom post type that allows the creation of content blocks.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
class Blog extends Cpt
{
    /**
     * The name under which this post type is registered.
     *
     * @var string
     */
    public $type = 'blog';
    
    /**
     * The init method acts as a substitute constructor.
     */
    public function init()
    {
        $this->labels = array(
            'name'               => __('Blogs'),
            'singular_name'      => __('Blog'),
            'add_new'            => __('Nieuwe blog'),
            'add_new_item'       => __('Nieuw blog'),
            'edit_item'          => __('Blog bewerken'),
            'new_item'           => __('Nieuwe blog'),
            'view_item'          => __('Blog bekijken'),
            'search_items'       => __('Blogs zoeken'),
            'not_found'          => __('Geen blogs gevonden'),
            'not_found_in_trash' => __('Geen blogs gevonden'),
            'menu_name'          => __('Blogs'),
        );
        $this->settings = array(
            'rewrite'      => false,
            'hierarchical' => false,
            'public'       => false,
            'show_ui'      => true,
            'labels'       => $this->labels,
            'supports'     => array('title', 'editor'),
        );
    }
    
    /**
     * Register the custom post type.
     *
     * @param array $options (optional) options with which to register the post type.
     */
    public static function register(array $options = null)
    {
        new self($options);
    }
    
    /**
     * Returns a collection of posts for the specified arguments.
     *
     * Don't forget to call {@link wp_reset_query()} function to restore the global post data.
     *
     * @param array $args (optional) arguments to change the query.
     * @return array a collection of posts for the specified arguments.
     * @link https://codex.wordpress.org/Function_Reference/query_posts query_posts
     */
    public static function queryPosts(array $args = null)
    {
        $defaults = array(
            'post_type' => $this->type,
            'orderby'   => 'menu_order',
            'order'     => 'ASC',
        );
        $args = array_merge($defaults, $args);
        
        return query_posts($args);   
    }
}