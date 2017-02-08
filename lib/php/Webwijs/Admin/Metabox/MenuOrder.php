<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class MenuOrder extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'menu_order',
        'title'    => 'Volgorde',
        'context'  => 'side',
        'priority' => 'low',
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
        <input type="text" name="menu_order" size="4" value="<?php echo $post->menu_order ?>" />
    <?php
    }
    
    /**
     * Allow built-in functions of WordPress to save the menu order.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {}
}
