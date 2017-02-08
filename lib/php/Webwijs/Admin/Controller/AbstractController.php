<?php

namespace Webwijs\Admin\Controller;

use Webwijs\View;
use Webwijs\Util\Strings;

class AbstractController
{
    public $view;
    public $prefix;
    public $template;
    public $templateDir = 'app/Admin/templates/';
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
        $hookname = get_plugin_page_hookname($this->prefix, $this->prefix);
        add_action($hookname, array(&$this, 'render'));
        add_action($this->executeHook, array(&$this, 'execute'));
    }
    public function render()
    {
        global $plugin_page;
        if (!empty($plugin_page) && ($plugin_page == $this->prefix)) {
            $output = $this->view->render(Strings::addTrailing($this->templateDir, \DIRECTORY_SEPARATOR) . ltrim($this->template, \DIRECTORY_SEPARATOR));
            $output = $this->view->messages() . $output;
            echo $output;
        }
    }
    public function execute()
    {
        global $plugin_page;
        if (!empty($plugin_page) && ($plugin_page == $this->prefix)) {
            $action = !empty($_REQUEST['action']) ? (string) $_REQUEST['action'] : 'index';
            $camelized = preg_replace_callback('/-+(.)?/', array($this, 'camelize'), $action);
            $method = $camelized . 'Action';
            if (method_exists($this, $method)) {
                $this->template = $this->prefix . '/' . $action . '.phtml';
                $this->$method();
            }
            else {
                trigger_error('Action not found: ' . $action);
            }
        }
    }
    public function forward($action)
    {
        $camelized = preg_replace_callback('/-+(.)?/', array($this, 'camelize'), $action);
        $method = $camelized . 'Action';
        if (method_exists($this, $method)) {
            $this->template = $this->prefix . '/' . $action . '.phtml';
            $this->$method();
        }
        else {
            trigger_error('Action not found: ' . $action);
        }
    }
    public function renderScript($script)
    {
        $this->template = $script;
    }
    public function camelize($match)
    {
        return strtoupper($match[1]);
    }
}
