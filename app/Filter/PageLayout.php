<?php

namespace Theme\Filter;

use Webwijs\Post;

class PageLayout
{
    public static function getDefaultSidebar($sidebarId, $areaCode, $post)
    {
        if (is_category() || (!empty($post) && $post->post_type != 'page')) {
            $postType = is_category() ? 'post' : $post->post_type;
            $parentId = Post::getCustomPostPageId($postType);
            if (!empty($parentId)) {
                $parentSidebarId = get_post_meta($parentId, '_sidebar_' . $areaCode, true);
                if (!empty($parentSidebarId)) {
                    $sidebarId = $parentSidebarId;
                }
            }
        }
        return $sidebarId;
    }
}
