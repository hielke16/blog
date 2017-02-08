<?php

namespace Webwijs\View\Helper;

class UrlByTemplate
{
    public function urlByTemplate($template, $sort_order='asc')
    {
        foreach (get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => $template, 'hierarchical' => false, 'sort_column' => 'menu_order', 'sort_order' => $sort_order)) as $page) {
            return get_permalink($page->ID);
        }
        return get_site_url();
    }
}
