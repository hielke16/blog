<?php

namespace Webwijs;

use Webwijs\Application;

class PageLayout
{
    public $code;
    public $name;
    public $sidebars;
    public $containerClass;

    public function __construct($code, $options)
    {
        $this->code = $code;
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }
    public function __call($method, $args)
    {
        $type = substr($method, 0, 3);
        $property = strtolower(substr($method, 3, 1)) . substr($method, 4);
        switch ($type) {
            case 'get':
                return $this->{$property};
                break;
            case 'set':
                $this->{$property} = $args[0];
                return $this;
                break;
            default:
                trigger_error('Undefined method ' . $method . ' on ' . get_class($this));
                break;
        }
    }
    public function getContainerClass()
    {
        if (empty($this->containerClass)) {
            return $this->code;
        }
        return $this->containerClass;
    }
    public function getSidebar($areaCode, $post = null)
    {
        is_null($post) && $post = $GLOBALS['post'];
        if (isset($this->sidebars[$areaCode])) {
            $sidebarId = null;
            if ($post) {
                $sidebarId = get_post_meta($post->ID, '_sidebar_' . $areaCode, true);
            }
            if (empty($sidebarId)) {
                $sidebarId = Application::getServiceManager()->get('PageLayout')->getDefaultSidebar($areaCode, $post);
            }
            if (!empty($sidebarId) && ($sidebarId != 'empty')) {
                return 'sidebar-' . $sidebarId;
            }
            return $sidebarId;
        }
    }
}
