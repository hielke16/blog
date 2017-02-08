<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Address extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'meta_address',
        'title'    => 'Adres met coordinaten',
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
     * Method which will be called once the metabox has been created.
     *
     * @return void
     */
    public function init()
    {
        $defaults = array(
            'name' => '',
            'class' => 'widefat address-latlng',
            'description' => 'Zoek hier naar een adres om coordinaten op te slaan.',
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
    <?php if (!empty($this->options['description'])): ?>
    <p><?php echo esc_attr($this->options['description']) ?></p>
    <?php endif ?>
        <div class="address-container">
          <input placeholder="Voer een adres in..." type="text" class="widefat geocoding" data-target="<?php echo $this->getName($this->options['name']); ?>" autocomplete="off"/>
          <span class="status"><p></p></span>
          <span class="coordinates"><strong>Huidige coordinaten:</strong> <?php echo esc_attr($value)?></span>
          <div class="geocoding-results widefat"></div>
        </div>
        <input type="hidden" name="<?php echo $this->getName($this->options['name']) ?>" value="<?php echo esc_attr($value)?>" class="<?php echo $this->options['class'] ?>" />
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
     * a classname and description.
     *
     * @param array $options associative array containing options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
