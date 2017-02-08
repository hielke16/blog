<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

/**
 * Although this class is deprecated it's still available for plugins written
 * before the {@link Wysiwyg} class.
 *
 * @deprecated use Wysiwyg instead.
 * @since 1.0.9
 */
class ExtraText extends AbstractMetabox
{

    static public $id = 'extra_text';
    static public $title = 'Extra informatie';
    static public $context = 'normal';
    static public $priority = 'high';

    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'exclude_from_sitemap',
        'title'    => 'Zichtbaarheid in sitemap',
        'context'  => 'side',
        'priority' => 'default',
    );

    /**
     * Additional options used to display the metabox.
     *
     * @var array
     */
    public $options = array();

    /**
     * Method which will be called once the metabox has been created
     *
     * @return void
     */
    public function init()
    {
        $defaults = array(
            'name' => 'extra_text',
        );
        $this->options = array_merge($defaults, (array) $this->options);
        add_action('admin_head', array('Webwijs\Admin\Action\TinyMCE', 'enable'));
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $extra_text = $this->getPostMeta($post->ID, $this->options['name'], true);
        ?>
        <p>
            <label><?php echo __($this->settings['title'])?></label><br />
            <textarea type="text" class="wysiwyg" name="<?php echo $this->getName($this->options['name']) ?>"><?php echo esc_attr($extra_text)?></textarea>
        </p>
        <?php
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        $this->updatePostMeta($postId, $this->options['name'], $this->getPostValue($this->options['name']));
    }
    
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
