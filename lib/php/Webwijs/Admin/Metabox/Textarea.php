<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Textarea extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'additional_info_box',
        'title'    => 'Aanvullende informatie',
        'context'  => 'normal',
        'priority' => 'default',
    );

    /**
     * Additional options used to display the metabox.
     *
     * @var array
     */
    public $options = array();

    /**
     * Method which will be called once the metabox has been created.
     *
     * @return void
     */
    public function init()
    {
        $defaults = array(
            'class' => 'widefat',
            'name' => '',
            'textarea_rows' => 10,
            'show_title' => true,
        );
        $this->options = array_merge($defaults, (array) $this->options);
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $value = $this->getPostMeta($post->ID, $this->options['name'], true);
    ?>
        <textarea rows="<?php echo $this->options['textarea_rows'] ?>" name="<?php echo $this->getName($this->options['name']) ?>" class="<?php echo $this->options['class'] ?>"><?php echo esc_attr($value)?></textarea>
        <?php if (!empty($this->options['description'])): ?>
            <p><?php echo $this->options['description'] ?></p>
        <?php endif ?>
    <?php
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        $this->updatePostMeta($postId, $this->options['name'], $this->getPostValue($this->options['name']));
    }
    
    /**
     * Set options for this metabox; which include but are limited to
     * a classname and name.
     *
     * @param array $options associative array containing options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
