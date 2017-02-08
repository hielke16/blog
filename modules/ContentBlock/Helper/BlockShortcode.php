<?php

namespace Module\ContentBlock\Helper;

/**
 * The BlockShortcode class should be invoked through use of a WordPress shortcode and
 * displays the post content of the specified content block.
 *
 * @author Chris Harris <chris@bwebwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
class BlockShortcode
{
    /**
     * A shortcode which returns the post content for the specified post id.
     * This shortcode should be in conjunction with the WordPress editor as following:
     *
     * <code>
     *     [content-block id='5']
     * </code>
     *
     * @param array $args (optional) argument used to obtain the correct content block.
     * @return string the post content of the specified content block, or empty string on failure.
     */
    public function blockShortcode(array $args = null)
    {    
        $output = '';
        if (is_array($args) && isset($args['id'])) {
            $post = get_post($args['id']);
            if (is_object($post) && isset($post->post_content)) {
                $output = apply_filters('the_content', $post->post_content);
            }
        }

        return $output;
    }
}
