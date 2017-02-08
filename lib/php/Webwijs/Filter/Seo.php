<?php

namespace Webwijs\Filter;

/**
 * The Seo class contains filters which are related to search engine optimization and
 * hook into plugin such as the WordPress SEO by Yoast plugin.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
class Seo
{
    /**
     * Taxonomies containing terms that are associated with one or more posts.
     *
     * @var array
     */
    private static $taxonomyNames = null;

    /**
     * Returns true if the specified taxonomy should be excluded from the sitemap.xml
     *
     * @param bool $exclude $exclude boolean flag that indicates if the specified taxonomy should be excluded.
     * @param string $tax the name of an taxonomy which will be excluded from the sitemap.xml.
     * @return bool true to exclude the specified taxonomy from the sitemap.xml, otherwise false.
     * @see WPSEO::build_root_map()
     */
    public static function excludeTaxonomy($exclude, $tax)
    {
        if (self::$taxonomyNames === null) {
            global $wpdb;
            
            $taxonomies = get_taxonomies(array('public' => true), 'names');
    	    $query = "SELECT taxonomy FROM {$wpdb->term_taxonomy} WHERE count != 0 AND taxonomy IN ('" . implode( "','", $taxonomies ) . "');";
		    self::$taxonomyNames = array_flip($wpdb->get_col($query));
		}
		
		return (!isset(self::$taxonomyNames[$tax]));
    }
}
