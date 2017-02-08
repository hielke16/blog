<?php

namespace Webwijs\View\Helper;

use Webwijs\View;

class Gallery
{
    var $defaults = array();
    var $images = null;
    
    function Gallery($options=array(), $images=array())
    {
        $post = $GLOBALS['post'];
        
        $defaults = array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post->ID,
            'size'       => 'thumbnail',
            'fullsize'   => 'large',
            'partial'    => false
        );
        
        $this->options = array_merge($defaults, $options);
        
        return $this;
    }
    function setPartial($partial)
    {
        $this->options['partial'] = $partial;
        return $this;
    }
    function hasImages()
    {
        return count($this->getImages());
    }
    function getImages()
    {
        global $post;
        
        $this->images = get_children( array(
            'post_parent' => $post->ID,
            'post_type' => 'attachment',
            'orderby' => 'menu_order ASC, ID',
            'order' => 'DESC',
            'meta_key' => '_bgallery',
            'meta_value' => 'bgallery'
        ) );
        return $this->images;            
    }
    
    function __toString()
    {

        $images = $this->getImages();
        $html = '';
        if($this->options['partial']){
            $view = new View();            
            $html = $view->partial($this->options['partial'], array('images' => $images, 'options' => $this->options));
        }
        else{
            $html = '<div class="gallery">';
            foreach($images as $image){            
                $src = wp_get_attachment_image_src($image->ID, $this->options['fullsize']);
                $html .= '<a class="image" href="' . $src[0] . '">';
                $html .= wp_get_attachment_image($image->ID, $this->options['size'], false);                              
                $html .= '</a>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}
?>
