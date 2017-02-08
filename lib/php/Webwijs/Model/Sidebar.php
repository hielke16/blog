<?php

namespace Webwijs\Model;

use Webwijs\Model\AbstractModel;

class Sidebar extends AbstractModel
{
    public $type = 'Sidebar';
    public $tableConfig = array(
        'fields' => array('id' => '%d', 'name' => '%s'),
        'orderby' => 'name',
        'table' => 'sidebars'
    );
    public function getCode()
    {
        return 'sidebar-' . $this->id;
    }
    public function setup()
    {
        global $wpdb;
        $tableName = self::getTable()->getTableName();

        if ($wpdb->get_var("SHOW TABLES LIKE '{$tableName}'") != $tableName) {
            $sql = <<<SQL
                CREATE TABLE {$tableName} (
                    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    PRIMARY KEY (id)
                )
SQL;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}
