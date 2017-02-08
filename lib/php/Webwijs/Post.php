<?php

namespace Webwijs;

use Webwijs\Util\Arrays;

class Post
{
    static public $customPostPageIds= array();
    static public $customPostPages = array();
    static public $taxonomyPageIds = array();
    static public $taxonomyPages = array();

    public static function queryRelatedPosts($args = null, $key = null, $post = null)
    {
        global $wpdb;
        if (is_null($post)) {
            $post = $GLOBALS['post'];
        }
        $ids = self::getRelatedPostIds($key, $post);
        if (!empty($ids)) {
            $defaults = array(
                'post__in' => $ids,
                'post_type' => 'public',
                'nopaging' => true,
            );
            $args = wp_parse_args($args, $defaults);
            if ($args['post_type'] == 'public') {
                $args['post_type'] = array_diff(get_post_types(array('public' => true)), array('attachment'));
            }
            if (empty($args['orderby']) && empty($args['custom_orderby'])) {
                $args['custom_orderby'] = 'FIELD(' . $wpdb->posts . '.ID, ' . implode(', ', array_map('absint', $ids)) . ')';
            }
            query_posts($args);
            return true;
        }
        return false;
    }

    public static function getRelatedPosts($args = null, $key = null, $post = null)
    {
        global $wpdb;
        if ($post === null) {
            $post = $GLOBALS['post'];
        }
        
        $ids = self::getRelatedPostIds($key, $post);
        if (!empty($ids)) {
            $defaults = array(
                'post__in' => $ids,
                'post_type' => 'public',
                'nopaging' => true,
            );
            $args = Arrays::addAll($defaults, (array) $args, array('suppress_filters' => false));
            
            if ($args['post_type'] == 'public') {
                $args['post_type'] = array_diff(get_post_types(array('public' => true)), array('attachment'));
            }
            if (empty($args['orderby']) && empty($args['custom_orderby'])) {
                $args['custom_orderby'] = 'FIELD(' . $wpdb->posts . '.ID, ' . implode(', ', array_map('absint', $ids)) . ')';
            }  
            return get_posts($args);
        }
        return array();
    }

    public static function getRelatedPostIds($key = null, $post = null)
    {
        global $wpdb;
        if (is_null($post)) {
            $post = $GLOBALS['post'];
        }
        $keyCondition = '';
        if (!empty($key)) {
            $keyCondition = $wpdb->prepare(' AND relation_key = %s', $key);
        }
        $subQuery = <<<SQL
            SELECT
                CASE WHEN r.post_a_id = %d THEN r.post_b_id ELSE r.post_a_id END AS related_id
            FROM {$wpdb->prefix}related_posts AS r
            WHERE (r.post_a_id = %d OR r.post_b_id = %d) {$keyCondition}
            ORDER BY sort_order ASC
SQL;

        $subQuery = $wpdb->prepare($subQuery, $post->ID, $post->ID, $post->ID, $key);
        $ids = $wpdb->get_col($subQuery);
        return $ids;
    }
    
    public static function updateRelatedPosts($postId, $posts, $key)
    {
        global $wpdb;

        $posts = (array) $posts;
        $posts = array_map('intval', $posts);

        $deleteSql = <<<SQL
            DELETE FROM {$wpdb->prefix}related_posts
            WHERE (post_a_id = %d OR post_b_id = %d) AND relation_key = %s
SQL;
        $deleteSql = $wpdb->prepare($deleteSql, $postId, $postId, $key);
        if (!empty($posts)) {
            $notIn = implode(', ', $posts);
            $deleteSql .= ' AND post_a_id NOT IN (' . $notIn . ') AND post_b_id NOT IN (' . $notIn . ')';
        }
        $wpdb->query($deleteSql);

        foreach ($posts as $sort_order => $relatedPostId) {
            if ($relatedPostId < $postId) {
                $post_a_id = $relatedPostId;
                $post_b_id = $postId;
            }
            else {
                $post_a_id = $postId;
                $post_b_id = $relatedPostId;
            }
            $sql = <<<SQL
                INSERT INTO {$wpdb->prefix}related_posts
                SET post_a_id = %d, post_b_id = %d, relation_key = %s, sort_order = %d
                ON DUPLICATE KEY UPDATE sort_order = %d
SQL;
            $sql = $wpdb->prepare($sql, $post_a_id, $post_b_id, $key, $sort_order, $sort_order);
            $wpdb->query($sql);
        }
    }
    
    public static function addTaxonomyPageId($taxonomy, $pageId)
    {
        self::$taxonomyPageIds[$taxonomy] = $pageId;
    }
    
    public static function getTaxonomyPageId($taxonomy)
    {
        if (isset(self::$taxonomyPageIds[$taxonomy])) {
            return self::$taxonomyPageIds[$taxonomy];
        }
    }
    
    public static function getPageTaxonomies($pageId)
    {
        $taxonomies = array();
        foreach (self::$taxonomyPageIds as $taxonomy => $taxonomyPageId) {
            if ($taxonomyPageId == $pageId) {
                $taxonomies[] = $taxonomy;
            }
        }
        return $taxonomies;
    }
    
    public static function getTaxonomyPage($taxonomy)
    {
        $page = null;
        if (!empty(self::$taxonomyPageIds[$taxonomy])) {
            if (!isset(self::$taxonomyPages[$taxonomy])) {
                self::$taxonomyPages[$taxonomy] = get_page(self::$taxonomyPageIds[$taxonomy]);
                _get_post_ancestors(self::$taxonomyPages[$taxonomy]);
            }
            $page = self::$taxonomyPages[$taxonomy];
        }
        return $page;
    }
    
    public static function getTaxonomyAncestors($taxonomy)
    {
    	$page = self::getTaxonomyPage($taxonomy);
        if(!empty($page)){
            return array_merge(array($page->ID), self::getPostAncestors($page));
        }
    }

    public static function addCustomPostPageId($postType, $pageId)
    {
        self::$customPostPageIds[$postType] = $pageId;
    }

    public static function getCustomPostPageIds(){
        return self::$customPostPageIds;
    }
    
    public static function getCustomPostPageId($postType)
    {
        if (isset(self::$customPostPageIds[$postType])) {
            return self::$customPostPageIds[$postType];
        }
    }

    public static function getCustomPostPage($postType)
    {
        $page = null;
        if (!empty(self::$customPostPageIds[$postType])) {
            if (!isset(self::$customPostPages[$postType])) {
                self::$customPostPages[$postType] = get_page(self::$customPostPageIds[$postType]);
                self::getPostAncestors(self::$customPostPages[$postType]);
            }
            $page = self::$customPostPages[$postType];
        }
        return $page;
    }
    
    public static function getPostAncestors($post = null)
    {
        if (is_null($post)) {
            $post = $GLOBALS['post'];
        }
        
        $parents = get_post_ancestors($post);
        if (!empty(self::$customPostPageIds[$post->post_type])) {
            $parent = self::getCustomPostPage($post->post_type);
            if ($parent) {
                $post->ancestors = array_merge((array) $parents, array($parent->ID), $parent->ancestors);
            }
        }
        return (isset($post->ancestors)) ? $post->ancestors : null;
    }
    
    public static function getPostParent($post = null)
    {
        $ancestors = self::getPostAncestors($post);
        if (!empty($ancestors)) {
            return end($ancestors);
        }
    }
}
