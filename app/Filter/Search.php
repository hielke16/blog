<?php

namespace Theme\Filter;

class Search
{
    public static function filter($search, $query)
    {
        global $wpdb;
        if (!empty($search) && !is_admin()) {
            $search .= " AND {$wpdb->posts}.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_visibility_exclude_from_search' AND meta_value = 'yes')";
        }
        return $search;
    }
}
