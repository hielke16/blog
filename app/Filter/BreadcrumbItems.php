<?php

namespace Theme\Filter;

use Webwijs\Post;

class BreadcrumbItems
{
    public static function customPostTypes($trail)
    {
        global $post;
        if ($post && empty($post->ancestors) && !is_search()) {
            if (is_home() && !get_option('page_for_posts')) {
                $trail['trail_end'] = __('Zoekresultaten', 'webwijs');
            }
            else {
                $parent = Post::getCustomPostPage($post->post_type);
                if ($parent) {
                    $trailEnd = $trail['trail_end'];
                    unset($trail['trail_end']);
                    _get_post_ancestors($parent);
                    foreach ($parent->ancestors as $ancestorId) {
                        $ancestor = get_post($ancestorId);
                        $trail = self::_addTrailItem($trail, $ancestor);
                    }
                    $trail = self::_addTrailItem($trail, $parent);
                    $trail['trail_end'] = $trailEnd;
                }
            }
        }

        return $trail;
    }
    protected static function _addTrailItem($trail, $item)
    {
        if ($item) {
            $trail[] = '<a href="' . get_permalink( $item->ID ) . '" title="' . esc_attr($item->post_title) . '">' . esc_attr($item->post_title) . '</a>';
        }
        return $trail;
    }
}
