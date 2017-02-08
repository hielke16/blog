<?php

namespace Theme\Admin\ListTable;

use Webwijs\Application;
use Webwijs\View;

class Sidebars extends \WP_List_Table
{
    function prepare_items() {
        $table = Application::getModelManager()->getTable('Sidebar');
        if (!empty($table)) {
            $this->items = $table->findAll();
        }
    }

    function no_items() {
        echo 'Er zijn nog geen sidebars';
    }
    function get_bulk_actions() {
        $actions = array();
        $actions['delete'] = __('Delete');
        return $actions;
    }
    function get_column_info() {
       $this->_column_headers = array(
            array(
                'cb' => '<input type="checkbox" />',
                'name' => 'Naam',
            ),
            array(),
            array(
                'name' => array('name', false),
            ),
            array()
        );
        return $this->_column_headers;
    }
    function display_rows() {
        $class = '';
        $view = new View();
        foreach ($this->items as $item) {
            $class = $class == 'alternate' ? '' : 'alternate';
            echo $view->partial('app/Admin/templates/theme-sidebars/list-item.phtml', array('item' => $item, 'class' => $class));
        }
    }
}
