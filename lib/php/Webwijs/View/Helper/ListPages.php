<?php

namespace Webwijs\View\Helper;

class ListPages
{
    public function listPages($args=array())
    {
        $defaults = array(          
            'depth'        => 0,
            'child_of'     => 0,
            'exclude'      => '',
            'include'      => '',
            'title_li'     => '',
            'echo'         => 0,
        );
        $args = array_merge($defaults, $args);
        $pages = wp_list_pages($args);                    
        return '<nav class="listpages"><ul>'.$pages.'</ul></nav>';
           
    }
}
