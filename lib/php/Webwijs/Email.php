<?php

namespace Webwijs;

use Webwijs\View;

class Email
{
    public $to;
    public $from;
    public $subject;
    public $message;
    public $template;
    public $baseDirs = array();
    public $layout;
    public $templateVars;
    public $files = array();
    public $contentType = 'text/html';
    public function _construct($options = null)
    {
        $this->setOptions($options);
    }
    public function setOptions($options)
    {
        foreach ((array) $options as $key => $value) {
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
    }
    public function __call($name, $args)
    {
        if (substr($name, 0, 3) == 'set') {
            $key = strtolower($name{3}) . substr($name, 4);
            $this->$key = $args[0];
            return $this;
        }
        elseif (substr($name, 0, 3) == 'get') {
            $key = strtolower($name{3}) . substr($name, 4);
            return $this->$key;
        }
        trigger_error('Undefined function ' . get_class($this) . ':' . $name);
    }
    public function send($options = null)
    {
        $this->setOptions($options);

        $to = $this->_formatAddress($this->to);
        $from = $this->_formatAddress($this->from);
        $subject = $this->subject;

        if (!empty($this->template)) {
            $view = new View;
            $view->baseDirs = $this->baseDirs;
            $message = $view->partial($this->template, $this->templateVars);
            if (!empty($this->layout)) {
                $message = $view->partial($this->layout, array_merge((array) $this->templateVars, array('content' => $message)));
            }
        }
        else {
            $message = $this->message;
        }
        return wp_mail($to, $subject, $message, 'From: ' . $from . "\r\n" . 'Content-type: ' . $this->contentType . '; charset=UTF-8', $this->files);
    }
    public function addFile($file)
    {
        $this->files[] = $file;
        return $this;
    }
    protected function _formatAddress($address)
    {
        if (empty($address)) {
            return get_option('blogname') . '<' . get_option('admin_email') . '>';
        }
        if (is_array($address) && isset($address['name'], $address['email'])) {
            return $address['name'] . ' <' . $address['email'] . '>';
        }
        return $address;
    }
}
