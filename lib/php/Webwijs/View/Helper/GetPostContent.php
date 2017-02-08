<?php

namespace Webwijs\View\Helper;

class GetPostContent
{
    public function getPostContent($postId)
    {
        $post = get_post($postId);
        if ($post) {
            return apply_filters('the_content', $post->post_content);
        }
    }
}
