<?php

namespace Webwijs\Controller;

use Webwijs\View;

class Admin
{
    public $view;
    public $prefix;
    public $executeHook = 'admin_init';
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        $this->view = new View();
        $this->registerActions();

        $this->init();
    }
    public function init()
    {

    }
    public function registerActions()
    {
        global $_registered_pages;
        foreach (get_class_methods($this) as $method) {
            if (substr($method, -6) == 'Action') {
                $action = substr($method, 0, -6);
                $hookname = get_plugin_page_hookname($this->prefix . '-' . $action, $this->prefix);
                add_action($hookname, array(&$this, 'render'));
                $_registered_pages[$hookname] = true;
            }
        }
        add_action($this->executeHook, array(&$this, 'execute'));
    }
    public function render()
    {
        global $plugin_page;
        if (!empty($plugin_page) && (strpos($plugin_page, $this->prefix) === 0)) {
            $action = substr($plugin_page, strlen($this->prefix) + 1);
            $output = $this->view->render('admin/templates/'. $this->prefix . '/' . $action . '.phtml');
            $output = $this->view->messages() . $output;
            echo $output;
        }
    }
    public function execute()
    {
        global $plugin_page;
        if (!empty($plugin_page) && (strpos($plugin_page, $this->prefix) === 0)) {
            $method = substr($plugin_page, strlen($this->prefix) + 1) . 'Action';
            $this->$method();
        }
    }
    public function addMessage($type, $message) {
        $_SESSION['webwijs-messages'][] = array('type' => $type, 'message' => $message);
    }
}

