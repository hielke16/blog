<?php

namespace Webwijs\Walker;

use Webwijs\Post;

class Sitemap extends \Walker_Page
{
    public function walk($elements, $max_depth)
    {
        $args = array_slice(func_get_args(), 2);
        
        if ($max_depth < -1 || empty($elements)) {
            return;
        }
        
        $output = '';
        $top_level_elements = $this->_getTopLevelElements($elements);
        $children_elements = $this->_getChildElements($elements);

        foreach ( $top_level_elements as $e ) {
            $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

        }

        return $output;
    }
    protected function _getTopLevelElements($elements)
    {
        $topLevelElements = array();
        foreach ($elements as $e) {
            if ($e->post_parent == 0 && !Post::getCustomPostPageId($e->post_type)) {
                $topLevelElements[] =& $e;
            }
            unset($e);
        }
        return $topLevelElements;
    }
    protected function _getChildElements($elements)
    {
        $childElements = array();
        foreach ($elements as $e) {
            $customPostPageId = Post::getCustomPostPageId($e->post_type);
            if ($customPostPageId) {
                $childElements[$customPostPageId][] =& $e;
            }
            elseif ($e->post_parent > 0) {
                $childElements[$e->post_parent][] =& $e;
            }
            unset($e);
        }
        return $childElements;
    }
}
