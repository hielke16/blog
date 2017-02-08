<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Post;
use Webwijs\View;
use Webwijs\Admin\AbstractMetabox;

class RelatedPostsSelect extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'related_posts_select',
        'title'    => 'Gerelateerde pagina\'s',
        'context'  => 'side',
        'priority' => 'low',
    );

    /**
     * Unique name for the related post checkboxes.
     *
     * @var string
     */
    public $metaKey = 'related_posts';
    
    /**
     * The post type(s) that should be displayed.
     *
     * @var string|array
     */
    public $postType = 'page';
    
    /**
     * Query arguments used to populate the dropdown field.
     *
     * @var array
     */
    public $queryArgs = array();

    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $selected = '';
        $relatedPost = $this->getRelatedPost($post);
        if ($relatedPost) {
            $selected = $relatedPost->ID;
        }

        $view = new View();
        $dropdownArgs = array(
            'attribs' => array('class' => 'widefat'),
            'queryArgs' => array_merge(array('post_types' => $this->postType), $this->queryArgs)
        );
        echo $view->renderFormElement('postSelect', $this->getName($this->metaKey), $selected, $dropdownArgs);
    }
    
    /**
     * Returns an array with related posts for the given post.
     *
     * @return array an array containing related post objects.
     */
    public function getRelatedPost($post)
    {
        $ids = Post::getRelatedPostIds($this->getName($this->metaKey), $post);
        if(!empty($ids)) {
            $relatedPosts = get_posts(array(
                'include' => implode(', ', $ids),
                'post_type' => $this->postType
            ));
            return reset($relatedPosts);
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
        $posts = array();
        $value = $this->getPostValue($this->metaKey);
        if (!empty($value)) {
            $posts[] = $value;
        }
        Post::updateRelatedPosts($postId, $posts, $this->getName($this->metaKey));
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
     * @param string|array $postType a single post type should be represented as 
     *                               a string, or an array for multiple post types.
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;
    }
    
    /**
     * Set the query args used to populate a dropdown field with posts.
     *
     * @param array $queryArgs query arguments used to retrieve the correct posts.
     */
    public function setQueryArgs(array $queryArgs)
    {
        $this->queryArgs = $queryArgs;
    }
}
