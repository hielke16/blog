<?php

namespace Webwijs\Action;

class SetupRelatedPosts
{
    public static function setup()
    {
        global $wpdb;
        $tableName =  $wpdb->prefix . 'related_posts';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$tableName}'") != $tableName) {
            $sql = <<<SQL
                CREATE TABLE {$tableName} (
                    post_a_id BIGINT(20) UNSIGNED NOT NULL,
                    post_b_id BIGINT(20) UNSIGNED NOT NULL,
                    relation_key VARCHAR(127) NOT NULL,
                    sort_order MEDIUMINT(9) NOT NULL,
                    PRIMARY KEY (post_a_id, post_b_id, relation_key),
                    INDEX (sort_order),
                    FOREIGN KEY (post_a_id) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE,
                    FOREIGN KEY (post_b_id) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
                )
SQL;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}
