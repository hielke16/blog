<?php

namespace Webwijs\Action;

class MySQL
{
    public static function setupRelatedPosts($oldname, $oldtheme = false)
    {
        global $wpdb;
        
        $db_version = "1.0";

        $table_name = $wpdb->prefix . 'related_posts';
        if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS ". $table_name ." (
                post_a_id bigint(20) UNSIGNED NOT NULL,
                post_b_id bigint(20) UNSIGNED NOT NULL,
                relation_key varchar(255) NOT NULL,
                sort_order int(11) NOT NULL,
                PRIMARY KEY (post_a_id, post_b_id),
                FOREIGN KEY (post_a_id) REFERENCES ". $wpdb->posts ."(ID),
                FOREIGN KEY (post_b_id) REFERENCES ". $wpdb->posts ."(ID)
            );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            add_option("wordpressapi_db_version", $db_version);
        }
    }
}
