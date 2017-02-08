<?php

namespace Webwijs\View\Helper;

class Menu
{
    public function menu($name, $options = null)
    {
        $defaults = array(
            'container' => 'nav',
            'container_class' => $name . '-menu-container navbar',
            'menu_class' => $name .  '-menu',
            'menu_id' => $name .  '-menu',
            'fallback_cb' => '',
            'link_before' => '<span>',
            'link_after' => '</span>',
            'theme_location' => $name,
        );

        $options = array_merge($defaults, (array) $options);
        
        ob_start();
        wp_nav_menu($options);
        return ob_get_clean();
    }
}
