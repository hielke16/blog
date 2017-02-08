<?php

namespace Module\ContentBlock\Widget;

use Webwijs\Http\Request;

/**
 * An abstract widget which will setup all the conditions necessary to display a dropdown with images 
 * and show a preview of a selected image.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
abstract class AbstractImageWidget extends \Wp_Widget
{
    /**
     * Construct a new widget.
     *
	 * @param string $id_base (optional) The base ID for the widget, lowercase and unique. If left empty,
	 *                        a portion of the widget's class name will be used Has to be unique.
	 * @param string $name The name for the widget displayed on the configuration page.
	 * @param array  $widget_options (optional) The widget options. See {@see wp_register_sidebar_widget()} for
	 *                               information on accepted arguments. Default empty array.
	 * @param array  $control_options (optional) The widget control options. See {@see wp_register_widget_control()}
	 *                                for information on accepted arguments. Default empty array.
	 * @see Wp_Widget::__construct($id_base, $name, $widget_options, $control_options)
     */
    public function __construct($id_base, $name, $widget_options = array(), $control_options = array())
    {
        parent::__construct($id_base, $name, $widget_options, $control_options);
        
		// create admin hooks for this widget.
		if(is_admin()) {
		    add_action('wp_ajax_get_attachment_url', array($this, 'getAttachmentUrl'));
			add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		}
    }
    
    /**
     * Converts the given collection of WP_Post ojects into an array that can be used to populate a dropdown form.
     *
     * @param array|\Traversable $posts a collection of posts.
     * @return array an array consisting of (dropdown) options.
     * @throws \InvalidArgumentException if the given argument is not an array or an instance of Traversable.
     */
    protected function asOptions($posts)
    {
        if (!is_array($posts) && !($posts instanceof Traversable)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($posts) ? get_class($posts) : gettype($posts))
            ));
        }

        $options = array();
        foreach ($posts as $post) {
            $options[$post->ID] = $post->post_title; 
        }
        return $options;
    }

    /**
     * Handles an asynchronous HTTP request and finds the image url associated with the given attachment id.
     *
     * The attachment id is retrieved from the POST or GET superglobal depending
     * on the HTTP request method used with the AJAX request.
     *
     * @return void
     * @link http://codex.wordpress.org/AJAX_in_Plugins#Ajax_on_the_Administration_Side
     */
    public function getAttachmentUrl()
    {
        $request = new Request();
        switch ($request->getMethod()) {
            case 'POST':
                $attachmentId = $request->getPost('attachment_id', 0);
                break;
            case 'GET':
            default:
                $attachmentId = $request->getQuery('attachment_id', 0);
                break;
        }
        if ($url = wp_get_attachment_url($attachmentId)) {
            echo esc_url($url);
        }
        exit();
    }

    /**
     * Enqueue necessary scripts into the head of the admin page.
     *
     * @param string $hook identifies a page, which can be used to target a specific admin page.
     * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
     */
    public function enqueueScripts($hook)
    {
        if ('widgets.php' != $hook) {
            return;
        }
        
        wp_enqueue_script('jquery-load-image-url', get_bloginfo('stylesheet_directory') . '/assets/lib/js/admin/jquery.load-image-url.js', array('jquery'), false, true);
        wp_enqueue_style('admin-style', get_bloginfo('stylesheet_directory') . '/assets/lib/css/admin.css');
    }
}
