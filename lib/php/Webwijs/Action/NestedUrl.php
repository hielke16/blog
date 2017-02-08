<?php

namespace Webwijs\Action;

use Webwijs\Post;

class NestedUrl
{
    public static function createTermLink($link, $term, $taxonomy)
    {
        $taxonomyPageId = Post::getTaxonomyPageId($taxonomy);
        if ($taxonomyPageId) {
            $link = rtrim(get_permalink($taxonomyPageId), '/') . '/' . $term->slug . '/';
        }
        return $link;
    }
    public static function pagenumLink($link)
    {
        if (preg_match('/\/page\/(\d+)/', $link, $match)) {
            return add_query_arg('pg', $match[1]);
        }
        else {
            return remove_query_arg('pg');
        }
    }
    public static function yearLink($link, $year)
    {
        return add_query_arg('year', $year, get_permalink(Post::getCustomPostPageId('post')));
    }
    public static function monthLink($link, $year, $month)
    {
        return add_query_arg('month', $month, add_query_arg('year', $year, get_permalink(Post::getCustomPostPageId('post'))));
    }

    public static function redirectCanonical($redirectUrl, $requestedUrl)
    {
        if (preg_match('/(\?|&)pg=\d/', $requestedUrl) && (get_query_var('paged') > 0)) {
            return false;
        }
        if (preg_match('/(\?|&)(year=\d+|month=\d+)/', $requestedUrl)) {
            return false;
        }
        return $redirectUrl;
    }
    public static function parseRequest($wp)
    {
        if (!empty($wp->query_vars)) {
            if (isset($wp->query_vars['name']) && ($wp->query_vars['name'] == 'robots.txt')) {
                $wp->query_vars['robots'] = true;
                unset($wp->query_vars['name']);
            }
            $pathParts = self::_splitPath($wp->request);
            $page = self::_getPostByPath($pathParts);
            if ($page) {
                // redirect URIs with wrong case

                $permalink = parse_url(get_permalink($page->ID));
                if (!empty($permalink['path'])) {
                    if ((strcmp($_SERVER['REQUEST_URI'], $permalink['path']) != 0) && (strcasecmp($_SERVER['REQUEST_URI'], $permalink['path']) == 0)) {
                        wp_redirect(get_permalink($page->ID), 301);
                    }
                }
                //

                if ($page->post_type == 'page') {
                    $wp->query_vars = array(
                        $page->post_type => '',
                        'page_id' => $page->ID
                    );
                }
                else {
                    $wp->query_vars = array(
                        $page->post_type => $page->post_name,
                        'post_type' => $page->post_type,
                        'name' => $page->post_name
                    );
                }
                $wp->matched_rule = '(.?.+?)(/[0-9]+)?/?$';
                $wp->matched_query = $page->post_type . '=' . $page->post_name;
            }
            else {
                $term = self::_getTermByPath($pathParts);
                if ($term) {
                    switch ($term->taxonomy) {
                        case 'category':
                            $wp->query_vars = array(
                                'category_name' => $term->slug
                            );
                            break;
                        default:
                            $wp->query_vars = array(
                                $term->taxonomy => $term->slug
                            );
                            break;
                    }
                }
            }
            if (!empty($_GET['pg'])) {
                $wp->query_vars['paged'] = (int) $_GET['pg'];
            }
            if (!empty($_GET['s'])) {
                $wp->query_vars['s'] = $_GET['s'];
            }
            if (!empty($_GET['year'])) {
                $wp->query_vars['year'] = $_GET['year'];
                unset($wp->query_vars['page_id']);
            }
            if (!empty($_GET['month'])) {
                $_GET['monthnum'] = $_GET['month'];
                $wp->query_vars['monthnum'] = $_GET['month'];
            }
        }
    }
    protected static function _getPostByPath($pathParts)
    {
        global $wpdb;
        if (!empty($pathParts) && !empty($pathParts[0])) {
            $postNames = "'". implode( "','", $pathParts ) . "'";
            $postTypes = "'" . implode("','", get_post_types(array('public' => true))) . "'";
            $pages = $wpdb->get_results("SELECT ID, post_name, post_parent, post_type FROM $wpdb->posts WHERE post_name IN ($postNames) AND post_type IN ($postTypes)", OBJECT_K);

            $revparts = array_reverse($pathParts);

            $foundid = 0;
            foreach ((array) $pages as $page) {
                if ($page->post_name == @$revparts[0]) {
                    $count = 0;
                    $p = $page;
                    while ($parent = self::_getParentForPage($p, $pages)) {
                        $count++;
                        if (!isset($revparts[$count]) || $parent->post_name != $revparts[$count]) {
                            break;
                        }
                        $p = $parent;
                    }

                    if ($p->post_parent == 0 && $count+1 == count( $revparts ) && $p->post_name == $revparts[ $count ]) {
                        $foundid = $page->ID;
                        break;
                    }
                }
            }

            if ($foundid) {
                return $page;
            }
            return null;
        }
    }
    protected static function _getTermByPath($pathParts)
    {
        $lastPart = array_pop($pathParts);
        $parentPage = self::_getPostByPath($pathParts);
        if ($parentPage) {
            $taxonomies = Post::getPageTaxonomies($parentPage->ID);
            if (!empty($taxonomies)) {
                $terms = get_terms($taxonomies, array('slug' => $lastPart));
                if (!empty($terms)) {
                    return array_pop($terms);
                }
            }
        }
    }
    protected static function _getParentForPage($p, $pages)
    {
        if ($p->post_parent != 0 && isset($pages[$p->post_parent])) {
            return $pages[$p->post_parent];
        }
        elseif ($p->post_parent == 0) {
            $parentId = Post::getCustomPostPageId($p->post_type);
            if ($parentId && isset($pages[$parentId])) {
                return $pages[$parentId];
            }
        }
    }
    protected static function _splitPath($path)
    {
        $path = rawurlencode(urldecode($path));
        $path = str_replace('%2F', '/', $path);
        $path = str_replace('%20', ' ', $path);
        $parts = explode('/', trim($path, '/'));
        $parts = array_map('esc_sql', $parts);
        $parts = array_map('sanitize_title_for_query', $parts);
        return $parts;
    }
}
