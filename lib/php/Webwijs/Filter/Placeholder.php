<?php

namespace Webwijs\Filter;

class Placeholder
{
    public static function getPlaceholder($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (empty($html)) {
            $attachmentId = get_option('theme_thumbnail_placeholder');
            if (!empty($attachmentId)) {
                $attr = (array) $attr;
                $attr['class'] = empty($attr['class']) ? 'is-placeholder' : $attr['class'] . ' is-placeholder';
                $html = wp_get_attachment_image($attachmentId, $size, false, $attr);
            }
        }
        return $html;
    }
}
