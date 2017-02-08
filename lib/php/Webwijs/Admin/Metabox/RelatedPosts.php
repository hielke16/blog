<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Post;
use Webwijs\View;
use Webwijs\Admin\AbstractMetabox;

class RelatedPosts extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'related_pages',
        'title'    => 'Gerelateerde pagina\'s',
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
    public $postTypes = 'page';
    
    /**
     * Query arguments used to populate the dropdown field.
     *
     * @var array
     */
    public $queryArgs = array();

    /**
     * Method which will be called once the metabox has been created
     * and can be overridden by a concrete implementation of the metabox.
     *
     * @return void
     */
    public function init()
    {
        wp_enqueue_script(
            'related-posts',
            get_template_directory_uri() . '/assets/lib/js/related-posts.js' ,
            array('jquery', 'jquery-ui-sortable')
        );

        wp_enqueue_style(
            'related-posts' ,
            get_template_directory_uri() . '/assets/lib/css/related-posts.css'
        );
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $posts = $this->getPosts();
        $view = new View();
        $dropdownArgs = array(
            'attribs' => array('class' => 'widefat'),
            'queryArgs' => array_merge(array('post_types' => $this->postTypes), $this->queryArgs)
        );
        $dropdown = $view->renderFormElement('postSelect', $this->getName('page_id'), '', $dropdownArgs);
        ?>
        <div class="related-posts-wrapper">
        <ol class="related-posts-list">
            <?php foreach($posts as $page): ?>
            <li>
                <input name="<?php echo $this->getName($this->metaKey); ?>[]" value="<?php echo $page->ID; ?>" type="checkbox" checked="checked" />
                <span><?php echo $page->post_title; ?></span>
            </li>
            <?php endforeach; ?>
        </ol>

        <?php if ($dropdown): ?>
        <?php echo $dropdown ?>
        <input type="button" name="<?php echo $this->getName($this->metaKey) ?>-button" value="<?php _e('Add'); ?>" class="button" />
        <?php endif ?>
        </div>
        <?php
    }
    
    /**
     * Returns an array with related posts for the current post that is being edited.
     *
     * @return array an array containing related post objects.
     */
    public function getPosts()
    {
        $posts = array();

        global $post, $wpdb;

        $ids = Post::getRelatedPostIds($this->getName($this->metaKey), $post);

        if(!empty($ids)) {
            $pages = get_posts(array(
                'include' => implode(', ', $ids),
                'post_type' => $this->postTypes
            ));

            foreach($pages as $page) {
                $key = array_search($page->ID, $ids);
                $posts[$key] = $page;
            }

            ksort($posts);
        }
        return $posts;
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
        return $postId;
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
     *                               a string, or an array for multiple post types.
     */
    public function setPostTypes($postTypes)
    {
        $this->postTypes = $postTypes;
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
