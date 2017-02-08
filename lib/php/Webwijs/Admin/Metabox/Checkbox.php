<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Checkbox extends AbstractMetabox
{   
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'meta_checkbox',
        'title'    => 'Checkbox',
        'context'  => 'side',
        'priority' => 'low',
    );
    
    /**
     * Additional options used to display the metabox.
     *
     * @var array
     */
    public $options = array();
    
    /**
     * Method which will be called once the metabox has been created
     * and can be overridden by a concrete implementation of the metabox.
     *
     * @return void
     */
    public function init()
    {
        $defaults = array(
            'name' => '',
            'description' => '',
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
        <label><input type="checkbox" name="<?php echo $this->getName($this->options['name']) ?>" value="1" <?php checked($value) ?> /> Ja</label>
        <?php if (!empty($this->options['description'])): ?>
            <p><?php echo esc_attr($this->options['description']) ?></p>
        <?php endif ?>
    <?php
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        $this->updatePostMeta($postId, $this->options['name'], $this->getPostValue($this->options['name'], ''));
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
