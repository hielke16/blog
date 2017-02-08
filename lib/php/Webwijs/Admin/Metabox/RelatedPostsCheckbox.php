<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Post;
use Webwijs\Admin\AbstractMetabox;

class RelatedPostsCheckbox extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'related_posts_checkbox',
        'title'    => 'Gerelateerde pagina\'s',
        'context'  => 'side',
        'priority' => 'low',
    );
    
    /**
     * Unique name for the related post checkboxes.
     *
     * @var string
     */
    public $metaKey = '';
    
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
        $relatedPostIds = $this->getRelatedPostIds($post);
        $posts = get_posts(array_merge(array('numberposts' => -1, 'post_type' => $this->postType), $this->queryArgs));

        foreach ($posts as $postItem) {
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->getName($this->metaKey) ?>[]" value="<?php echo $postItem->ID ?>" <?php echo checked(in_array($postItem->ID, $relatedPostIds)) ?> />
                <?php echo esc_html($postItem->post_title) ?>
            </label><br />
            <?php
        }
    }
    
    /**
     * Returns an array with related post ID's for the given post.
     *
     * @return array an array containing related post ID's.
     */
    public function getRelatedPostIds($post)
    {
        return Post::getRelatedPostIds($this->getName($this->metaKey), $post);
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        $posts = (array) $this->getPostValue($this->metaKey, null);
        Post::updateRelatedPosts($postId, $posts, $this->getName($this->metaKey));
    }
    
    /**
     * Set a unique name for the related post checkboxes.
     *
     * @param string $metaKey a unique name for the post select.
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }
    
    /**
     * Determine which post types should be represented by a checkbox.
     *
     * @param string|array $postType a single post type should be represented as 
     *                               a string, or an array for multiple post types.
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;
    }
    
    /**
     * Set the query args used to retrieve the correct posts.
     *
     * @param array $queryArgs query arguments used to retrieve the correct posts.
     */
    public function setQueryArgs(array $queryArgs)
    {
        $this->queryArgs = $queryArgs;
    }
}
