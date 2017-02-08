<?php

namespace Webwijs\View\Helper;

use Webwijs\Walker\Sitemap as Walker;

class Sitemap
{
    public function sitemap($args = null)
    {
        global $wp_query;
        global $wpdb;
        $output = '';

        $sql = 'SELECT post_id FROM ' . $wpdb->postmeta . '  WHERE `meta_key` = "_visibility_exclude_from_sitemap" AND meta_value = "yes"';
        $exclude = $wpdb->get_col($sql);
        
        $defaults = array(
            'post_type' => apply_filters('sitemap_post_types', 'public'),
            'depth' => 0, 'child_of' => 0,
            'orderby' => 'menu_order', 'order' => 'ASC',
		    'link_before' => '', 'link_after' => '',
		    'post_status' => 'publish', 'posts_per_page' => 1000,
		    'walker' => new Walker(),
		    'post__not_in' => $exclude
	    );
        $args = wp_parse_args((array) $args, $defaults);
        if ($args['post_type'] == 'public') {
            $args['post_type'] = array_diff(array_values(get_post_types(array('public' => true))), array('attachment', 'post'));
        }
        if (!is_array($args['post_type'])) {
            $args['post_type'] = explode(',', $args['post_type']);
        }
        $query = new \WP_Query();
        $pages = $query->query($args);
        $current_page = $wp_query->get_queried_object_id();

        $output .= '<ul class="sitemap">';
		$output .= walk_page_tree($pages, $args['depth'], $current_page, $args);
		$output .= '</ul>';
		return $output;
    }
}
