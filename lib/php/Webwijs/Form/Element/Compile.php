<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

/**
 * A button to compile SCSS
 */
class Compile extends Element
{
    public $attribs = array('class' => 'button-primary');
    public $helper = 'compile';

    public function __construct()
    {
        if(is_admin()) {
			add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		}
    }

    /**
     * Enqueue necessary scripts into the head of the admin page.
     *
     * @param string $hook identifies a page, which can be used to target a specific admin page.
     * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('compile-scss', get_bloginfo('stylesheet_directory') . '/assets/lib/js/admin/compile-scss.js', array('jquery'), false, true);
    }
}
