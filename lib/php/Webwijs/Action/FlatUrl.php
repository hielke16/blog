<?php

namespace Webwijs\Action;

class FlatUrl
{
    public function createCustomPermalink( $link, $post, $leavename, $sample = false )
    {
        $postName = $sample ? '/%postname%' : '/' . $post->post_name;
        return site_url($postName);
    }
    public function createPagePermalink( $link, $id, $sample = false )
    {
        global $post, $wp_rewrite;
        if (!$id) {
            $id = (int) $post->ID;
        }
        else {
            $post = &get_post($id);
        }
        if ('page' == get_option('show_on_front') && $id == get_option('page_on_front')) {
            return $link;
        }
        
        if (in_array($post->post_status, array('draft', 'pending', 'auto-draft'))) {
            return home_url('?page_id=' . $id);
        }
        $postName = $sample ? '/%postname%' : '/' . $post->post_name;
        return site_url($postName);
    }
    
    public static function parseRequest($wp)
    {
        if (!empty($wp->query_vars) && empty($wp->query_vars['s'])) {
            $page = self::_getPostByPath($wp->request);
            if ($page) {
                if ($page->post_type == 'page') {
                    $wp->query_vars = array(
                        'page_id' => $page->ID,
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
        }
    }
    protected static function _getPostByPath($path)
    {
        global $wpdb;
        $path = rawurlencode(urldecode($path));
        $path = str_replace('%2F', '/', $path);
        $path = str_replace('%20', ' ', $path);
        $parts = explode('/', trim($path, '/'));
        $parts = array_map('esc_sql', $parts);
        $parts = array_map('sanitize_title_for_query', $parts);

        $postName = end($parts);
        $postTypes = "'" . implode("','", get_post_types(array('public' => true))) . "'";
        $page = $wpdb->get_row("SELECT ID, post_name, post_parent, post_type FROM $wpdb->posts WHERE post_name = '$postName' AND post_type IN ($postTypes)", OBJECT);
        if ($page) {
            return $page;
        }
        return null;
    }
}
