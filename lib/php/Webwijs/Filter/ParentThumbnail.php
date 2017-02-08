<?php

namespace Webwijs\Filter;

class ParentThumbnail
{
    public static $types = array();
    public static function getPostThumbnail($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (empty($html)) {
            $post = get_post($post_id);
            if (!empty($post->post_parent) && in_array($post->post_type, self::$types)) {
                return get_the_post_thumbnail($post->post_parent, $size, $attr);
            }
        }
        return $html;
    }
}
