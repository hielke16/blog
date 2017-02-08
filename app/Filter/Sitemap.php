<?php

namespace Theme\Filter;

class Sitemap
{
    public static function postTypes()
    {
        return 'public';
    }
    public static function xmlSitemapExclude($options)
    {
        $options['sm_b_exclude'] = self::excludeIds();
        return $options;
    }
    public static function excludeIds($ids = null)
    {
        global $wpdb;
        $sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_exclude_from_sitemap' AND meta_value = 'yes'";
        $ids = $wpdb->get_col($sql);
        return $ids;
    }
}
