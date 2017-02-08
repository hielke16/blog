<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Excerpt extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'postexcerpt',
        'title'    => 'Uittreksel',
        'context'  => 'normal',
        'priority' => 'high',
    );

    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
    ?>
        <textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea>
        <p><?php _e('Excerpts are optional hand-crafted summaries of your content. You can <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" target="_blank">use them in your template</a>'); ?></p>
    <?php
    }
    
    /**
     * Allow built-in functions of WordPress to save the excerpt.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {}
}
