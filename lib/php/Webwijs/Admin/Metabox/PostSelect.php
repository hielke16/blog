<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\View;
use Webwijs\Admin\AbstractMetabox;

class PostSelect extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'related_post',
        'title'    => 'Gerelateerde pagina',
        'context'  => 'side',
        'priority' => 'low',
    );

    /**
     * Unique name for the post select.
     *
     * @var string
     */
    public $metaKey = '';
    
    /**
     * The post type(s) that should be displayed.
     *
     * @var string|array
     */
    public $postTypes = 'public';
    
    /**
     * A description displayed below the form.
     *
     * @var string
     */
    protected $description;
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $view = new View();
        
        $selected = $this->getPostMeta($post->ID, $this->metaKey, true);
        
        echo $view->renderFormElement('postSelect', $this->getName($this->metaKey), $selected, array(
                'attribs' => array('class' => 'widefat'),
                'queryArgs' => array('post_types' => $this->postTypes)
        ));

		if ($description = $this->getDescription()) {
            printf('<p>%s</p>', $description);
        }
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        $this->deletePostMeta($postId, $this->metaKey);
        
        $value = $this->getPostValue($this->metaKey);
        if(!empty($value)) {
            $this->updatePostMeta($postId, $this->metaKey, $value);
        }
    }
    
    /**
     * Set a unique name for the post select.
     *
     * @param string $metaKey a unique name for the post select.
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }
    
    /**
     * Determine which post types should be displayed by the post select.
     *
     * @param string|array $postTypes a single post type should be represented as 
     *                                a string, or an array for multiple post types.
     */
    public function setPostTypes($postTypes)
    {
        $this->postTypes = $postTypes;
    }
    
    /**
     * Set the description displayed below the form.
     *
     * @param string $description the description to display.
     */
    public function setDescription($description)
    {
        if (!is_string($description)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($description) ? get_class($description) : gettype($description))
            ));
        }
        
        $this->description = $description;
    }
    
    /**
     * Returns the description to display below the form.
     *
     * @return string the description to display.
     */
    public function getDescription()
    {
        return $this->description;
    }
}
