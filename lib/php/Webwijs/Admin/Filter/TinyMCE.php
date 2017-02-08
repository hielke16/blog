<?php

namespace Webwijs\Admin\Filter;

class TinyMCE
{
    public static function addButtonPlugin($plugins)
    {
        $plugins['custom_buttons'] = get_bloginfo('template_url') . '/assets/js/tinymce-buttons.js';
        return $plugins;
    }
    public static function registerButtons($buttons)
    {
        $buttons[] = 'custom_buttons';
        return $buttons;
    }
    public static function addTableJs($plugin_array)
    {
        $plugin_array['table'] = get_bloginfo('template_url') . '/assets/lib/js/tinymce/table/editor_plugin.js';
        return $plugin_array;
    }
    public static function addTableControls($buttons)
    {
        array_push( $buttons, 'tablecontrols' );
        return $buttons;
    }

}
