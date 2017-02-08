<?php

namespace Theme\Filter;

use Webwijs\Post;

class Menu
{
    public static function itemAncestors($items)
    {
        global $post;
        if (is_tax()) {
            $term = get_queried_object();
            $ancestors = Post::getTaxonomyAncestors($term->taxonomy);
        }
        elseif ($post && !is_search()) {
            $ancestors = Post::getPostAncestors($post);
        }
        if (!empty($ancestors)) {
            foreach ($items as $key => $item) {
                foreach ($ancestors as $ancestorId) {
                    if ($ancestorId == $item->object_id) {
                        $items[$key]->classes[] = 'current-post-ancestor';
                    }
                }
            }
        }
        return $items;
    }
}
