<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Visibility extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'visibility',
        'title'    => 'Zichtbaarheid',
        'context'  => 'side',
        'priority' => 'default',
    );
    
    /**
     * array with options this metabox supports.
     *
     * @var array
     */
    public $supports = array('sitemap', 'search');
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     * @link http://codex.wordpress.org/Class_Reference/WP_Post
     * @link https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    public function display($post)
    {
        if ($this->_supports('sitemap')) {
            $exclude_sitemap = $this->getPostMeta($post->ID, 'exclude_from_sitemap', true);
            ?>
            <div id="sitemap-visibility-select">
                <label><input type="checkbox" name="<?php echo $this->getName('sitemap-visibility') ?>" value="yes" <?php checked($exclude_sitemap, 'yes') ?> />
                Niet tonen in sitemap</label> <br />
            </div>
            <?php
        }
        
        if ($this->_supports('search')) {
            $exclude_search = $this->getPostMeta($post->ID, 'exclude_from_search', true);
            ?>
            <div id="search-visibility-select">
                <label><input type="checkbox" name="<?php echo $this->getName('search-visibility') ?>" value="yes" <?php checked($exclude_search, 'yes') ?> />
                Niet tonen in zoekresultaten</label> <br />
            </div>
            <?php
        }
        
        if ($this->_supports('featured')) {
            $is_featured = $this->getPostMeta($post->ID, 'is_featured', true);
            ?>
            <div id="sitemap-visibility-select">
                <label><input type="checkbox" name="<?php echo $this->getName('featured-visibility') ?>" value="yes" <?php checked($is_featured, 'yes') ?> />
                Tonen op home</label> <br />
            </div>
            <?php
        }
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        if ($this->_supports('sitemap')) {
            $this->updatePostMeta($postId, 'exclude_from_sitemap', $this->getPostValue('sitemap-visibility', ''));
        }
        
        if ($this->_supports('search')) {
            $this->updatePostMeta($postId, 'exclude_from_search', $this->getPostValue('search-visibility', ''));
        }
        
        if ($this->_supports('featured')) {
            $this->updatePostMeta($postId, 'is_featured', $this->getPostValue('featured-visibility', ''));
        }
    }
    
    /**
     * Returns true if the given type is supported by the metabox, false otherwise.
     *
     * @param string $type the type that needs checking.
     * @return bool true if the type is supported, false otherwise.
     */
    protected function _supports($type)
    {
        return in_array($type, $this->supports);
    }
    
    /**
     * Set the visibility options this metabox supports.
     *
     * @param array $supports an array containing the visibility options 
     *                        this metabox supports.
     */
    public function setSupports(array $supports)
    {
        $this->supports = $supports;
    }
}
