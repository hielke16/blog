<?php

namespace Theme\Helper;

use Webwijs\Application;

class SidebarArea
{
    public function sidebarArea($areaCode, $args = null, $post = null)
    {
        $defaults = array(
            'container' => 'aside',
            'class' => $areaCode
        );
        $args = array_merge($defaults, (array) $args);
        $sidebar = Application::getServiceManager()->get('PageLayout')->getCurrentLayout()->getSidebar($areaCode);

        $output = '';
        if (!empty($sidebar)) {
            if ($sidebar != 'empty') {
                $output = $this->view->sidebar($sidebar);
            }
            if (!empty($args['container'])) {
                $output = sprintf('<%s class="%s">%s</%s>', $args['container'], $args['class'], $output, $args['container']);
            }
        }
        return $output;
    }
}
