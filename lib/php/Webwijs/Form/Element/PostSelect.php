<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

class PostSelect extends Element
{
    public $helper = 'select';
    public function setOptions($options)
    {
        $args = (isset($options['queryArgs']) && is_array($options['queryArgs'])) ? $options['queryArgs'] : array(); 
        $this->queryPosts($args);
        
        return parent::setOptions($options);
    }
    protected function queryPosts($args)
    {
        $defaults = array(
            'post_types'       => 'public',
            'orderby'          => 'menu_order title',
            'nopaging'         => true,
            'show_option_none' => __('--Selecteer')
        );
        $args = array_merge($defaults, (array) $args);
        if ($args['post_types'] == 'public') {
            $args['post_types'] = array_diff(get_post_types(array('public' => true)), array('attachment'));
        }
        
        $postTypeObjects = array();
        foreach ((array) $args['post_types'] as $postType) {
            $postTypeObjects[] = $postTypeObject = get_post_type_object($postType);
        }
        
        $options = array();
        if (isset($args['show_option_none'])) {
            $options[''] = $args['show_option_none'];
        }
        
        foreach ($postTypeObjects as $postTypeObject) {
            $queryArgs = $args;
            $queryArgs['post_type'] = $postTypeObject->name;
            $posts = get_posts($queryArgs);
            if (!empty($posts)) {
                if ($postTypeObject->hierarchical) {
                    $posts = $this->makeHierarchical($posts);
                }
                $postTypeOptions = $this->optionsLevel($posts);
                
                if (!empty($postTypeOptions)) {
                    if (count($args['post_types']) > 1) {
                        $options[$postTypeObject->labels->name] = $postTypeOptions;
                    }
                    else {
                        $options = $this->_merge($options, $postTypeOptions);
                    }
                }
            }
        }
        $this->options = $options;
    }
    public function optionsLevel($posts, $level = 0)
    {
        $options = array();
        foreach ($posts as $post) {
            $options[(string) $post->ID] = str_repeat('&nbsp;', $level * 3) . esc_attr($post->post_title);
            if (!empty($post->children)) {
                $options = $this->_merge($options, $this->optionsLevel($post->children, $level + 1));
            }
        }
        return $options;
    }
    public function makeHierarchical($pages, $parentId = null) {
	    $pageList = array();
	    foreach ( (array) $pages as $page ) {
		    if ($page->post_parent == $parentId ) {
		        $page->children = $this->makeHierarchical($pages, $page->ID);
		        $pageList[] = $page;
		    }
	    }
	    if (empty($pageList) && is_null($parentId)) {
	        $pageList = $pages;
	    }
	    return $pageList;
    }
    protected function _merge($a, $b)
    {
        foreach ($b as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }
}
