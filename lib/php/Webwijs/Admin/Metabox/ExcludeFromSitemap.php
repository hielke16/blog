<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class ExcludeFromSitemap extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'exclude_from_sitemap',
        'title'    => 'Zichtbaarheid in sitemap',
        'context'  => 'side',
        'priority' => 'default',
    );
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        $exclude = $this->getPostMeta($post->ID, 'visibility', true);
    ?>
        <div id="sitemap-visibility-select">
            <input type="checkbox" name="<?php echo $this->getName('visibility') ?>" id="<?php echo $this->getName('visibility-radio-show') ?>" value="yes" <?php checked($exclude, 'yes') ?> /> 
            <label for="<?php echo $this->getName('visibility-radio-show') ?>">Niet tonen</label> <br />
        </div>
    <?php
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {    
        $this->updatePostMeta($postId, 'visibility', $this->getPostValue('visibility', ''));
    }
}

