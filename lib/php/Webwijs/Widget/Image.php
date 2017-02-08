<?php

namespace Webwijs\Widget;

use Webwijs\View;
use Webwijs\Http\Request;

class Image extends \Wp_Widget
{
    /**
     * Create Image widget.
     */
    public function __construct()
    {
        $options = array('description' => 'Toon een afbeelding uit de media bibliotheek.', 'classname' => 'widget-image');
        parent::__construct('Theme_Widget_Image', 'Uitgelichte afbeelding', $options);
        
		// create admin hooks for this widget.
		if(is_admin()) {
		    add_action('wp_ajax_get_attachment_url', array($this, 'getAttachmentUrl'));
			add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		}
    }
    
    /**
     * The form that is displayed in wp-admin and is used to save the settings 
     * for this widget.
     *
     * @param array $instance the form values stored in the database.
     */
    public function form($instance)
    {    
        $defaults = array(
            'attachment_id'   => '',
            'attachment_size' => '',
            'classname'       => '',
            'alt_text'        => '',
        );
        $instance = array_merge($defaults, (array) $instance);

        $images = get_posts(array(
            'post_type'      => 'attachment', 
            'post_mime_type' => 'image', 
            'post_status'    => 'inherit', 
            'posts_per_page' => -1,
        ));
        $view = new View();
    ?>
        <?php if (is_numeric($instance['attachment_id']) && ($url = wp_get_attachment_url($instance['attachment_id']))): ?>
        <div class="image-container">
            <img src="<?php echo esc_url($url) ?>" />
        </div>
        <?php endif ?>
    
        <p>
            <label for="<?php echo $this->get_field_id('attachment_id') ?>">Afbeelding:</label>
            <?php echo $view->dropdown($this->get_field_name('attachment_id'), array(
                'class' => 'widefat widget-image-dropdown-field',
                'selected' => $instance['attachment_id'],
                'options' => $this->asOptions($images),
            )); ?>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('attachment_size') ?>">Afmeting:</label>
            <?php echo $view->dropdown($this->get_field_name('attachment_size'), array(
                'class' => 'widefat',
                'selected' => $instance['attachment_size'],
                'options' => $this->imageSizeOptions(),
            )); ?>
        </p>
        
        <p>
            <label>Css-class: <small style="font-weight: bold; float: right;">(optioneel)</small><br />
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('classname') ?>" value="<?php echo $instance['classname'] ?>" />
            </label>
        </p>
        
        <p>
            <label>Alt-tekst: <small style="font-weight: bold; float: right;">(optioneel)</small><br />
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('alt_text') ?>" value="<?php echo $instance['alt_text'] ?>" />
            </label>
        </p> 
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
        $instance['attachment_id'] = (isset($new_instance['attachment_id']) && is_numeric($new_instance['attachment_id'])) ? (int) $new_instance['attachment_id'] : '';
        $instance['attachment_size'] = (isset($new_instance['attachment_size'])) ? strip_tags($new_instance['attachment_size']) : 'full';
        $instance['classname'] = (is_string($new_instance['classname']) && strlen($new_instance['classname'])) ? strip_tags($new_instance['classname']) : null;
        $instance['alt_text'] = (is_string($new_instance['alt_text']) && strlen($new_instance['alt_text'])) ? strip_tags($new_instance['alt_text']) : null;
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
            'attachment_id'   => '',
            'attachment_size' => 'full',
            'classname'       => '',
            'alt_text'        => '',
        );
        $instance = array_merge($defaults, (array) $instance);
    
        $attr = array();
        if (!empty($instance['classname'])) {
            $attr['class'] = $instance['classname'];
        }
        if (!empty($instance['alt_text'])) {
            $attr['alt'] = $instance['alt_text'];
        }

        if ($image = wp_get_attachment_image($instance['attachment_id'], $instance['attachment_size'], false, $attr)) {
            echo $image;
        }
    }
    
    /**
     * Returns an associative array containing all registered image sizes.
     *
     * The array returned can be used to populate a dropdown form.
     *
     * @return array an array consisting of image sizes.
     * @link http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes#Examples
     */
    private function imageSizeOptions()
    {
        $additionalSizes = (isset($GLOBALS['_wp_additional_image_sizes'])) ? $GLOBALS['_wp_additional_image_sizes'] : array();
        $intermediateSizes = get_intermediate_image_sizes();

        $options = array();
        foreach ($intermediateSizes as $size) {
            // the 'thumbnail','medium', 'large' sizes are stored in the options table.
            if (in_array($size, array('thumbnail', 'medium', 'large'))) {
                $widthOption = sprintf('%s_size_w', $size);
                $heightOption = sprintf('%s_size_h', $size);
            
                $options[$size] = sprintf('%s - %s x %s', $size, get_option($widthOption), get_option($heightOption));
            } else if (isset($additionalSizes[$size])) {
                $options[$size] = sprintf('%s - %s x %s', $size, $additionalSizes[$size]['width'], $additionalSizes[$size]['height']);
            }
        }
        return $options;
    }
    
    /**
     * Converts the given collection of WP_Post ojects into an array that can be 
     * used to populate a dropdown form.
     *
     * @param array|\Traversable $posts a collection of posts.
     * @return array an array consisting of (dropdown) options.
     * @throws \InvalidArgumentException if the given argument is not an array or an instance of Traversable.
     */
    private function asOptions($posts)
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
     * Handles an asynchronous HTTP request and finds the image url associated 
     * with the given attachment id.
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
