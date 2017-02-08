<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\View;
use Webwijs\Admin\AbstractMetabox;

class UserSelect extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'related_user',
        'title'    => 'Gekoppelde gebruiker',
        'context'  => 'side',
        'priority' => 'low',
    );

    /**
     * Unique name for the dropdown field.
     *
     * @var string
     */
    public $metaKey = '';
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $selected = $this->getPostMeta($post->ID, $this->metaKey, true);
        $view = new View();
        echo $view->dropdownUsers(array('name' => $this->getName($this->metaKey), 'selected' => $selected));
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
        if (!empty($value)) {
            $this->updatePostMeta($postId, $this->metaKey, $value);
        }
    }
    
    /**
     * Set a unique name for the dropdown field.
     *
     * @param string $metaKey a unique name for the dropdown field.
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }
}
