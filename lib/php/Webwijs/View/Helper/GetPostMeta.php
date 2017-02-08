<?php

namespace Webwijs\View\Helper;

class GetPostMeta
{
    public function getPostMeta( $name, $post = null, $prefix='_' ) {
        if (is_null($post)) {
            $post = $GLOBALS['post'];
        }
        $value = get_post_meta($post->ID, $prefix . $name, true);
        return (strip_tags($value) == '') ? false : $value;
    }
}
