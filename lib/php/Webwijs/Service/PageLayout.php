<?php

namespace Webwijs\Service;

use Webwijs\PageLayout as Layout;

class PageLayout
{
    protected $layouts = array();
    protected $defaultLayout;
    protected $sidebars = array();

    public function addLayout($code, $options)
    {
        $this->layouts[$code] = new Layout($code, $options);
        $this->sidebars = array_merge($this->sidebars, $options['sidebars']);
        return $this;
    }
    public function getDefaultSidebar($sidebarAreaCode, $post = null)
    {
        if (!is_object($post)) {
            $post = (isset($GLOBALS['post'])) ? $GLOBALS['post'] : null; 
        }
        return apply_filters('theme_default_sidebar', get_option('theme_default_sidebar_' . $sidebarAreaCode), $sidebarAreaCode, $post);
    }

    public function setDefaultSidebar($sidebarAreaCode, $sidebarId)
    {
        return update_option('theme_default_sidebar_' . $sidebarAreaCode, $sidebarId);
    }
    public function getTemplateLayouts($template)
    {
        $layouts = array();
        
        if(!$file = locate_template($template)){
            if(file_exists($template)){
                $file = $template;
            }
        }

        if ($file) {
            $contents = file_get_contents($file);
            if (preg_match('/Layouts:(.*)$/mi', $contents, $match)) {
                $layouts = preg_split('/\s*,\s*/', _cleanup_header_comment($match[1]), -1, PREG_SPLIT_NO_EMPTY);
            }
            elseif ($template != 'page.php') {
				$layouts = $this->getTemplateLayouts('page.php');
			}
        }
        elseif ($template != 'page.php') {
			$layouts = $this->getTemplateLayouts('page.php');
		}
        return $layouts;
    }
    public function getCurrentLayout($post = null)
    {
        // use the current post.
        if (!is_object($post)) {
            $post = $GLOBALS['post'];
        }
        
        // get layout code for current post.
        $layoutCode = '';
        if (is_object($post)) {
            $layoutCode = get_post_meta($post->ID, '_page_layout', true);
        }
    
        /*
         * use the default layout if the given layout is not available
         * or no page is being displayed.
         */        
        if (empty($layoutCode) || !isset($this->layouts[$layoutCode])) {
            $layoutCode = $this->getDefaultLayout();
        }
        return $this->layouts[$layoutCode];
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
}
