<?php

namespace Webwijs\View\Shortcode;

class Sitemap
{
    function render()
    {
        ob_start();
        echo '<ul class="sitemap">';
            wp_list_pages();
        echo '</ul>';
        $return = ob_get_clean();

        return $return;
    }
}
