<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Wysiwyg extends AbstractMetabox
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
        'priority' => 'high',
    );

    protected $title;

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
            'name' => 'additional_info',
            'textarea_rows' => 5,
            'editor_css' => '<style type="text/css">.wp-editor-container { background: white }</style>',
            'show_title' => true,
        );
        $this->options = array_merge($defaults, (array) $this->options);
    }

    public function display($post)
    {
        $content = $this->getPostMeta($post->ID, $this->options['name'], true);
    ?>

        <?php if ($this->options['show_title']): ?>
        <h4 style="margin-bottom: 0"><?php echo __($this->title) ?></h4>
        <?php endif ?>

        <?php wp_editor($content, $this->getName($this->options['name']), $this->options) ?>

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
    
    /** 
     * Set a title which is displayed inside the metabox.
     *
     * @param string $title the title to display.
     */
    public function setTitle($title)
    {
        if (!is_string($title)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($description) ? get_class($description) : gettype($description))
            ));
        }
        
        $this->title = $title;
    }
}
