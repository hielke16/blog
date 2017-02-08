<?php

namespace Theme\Admin\Action;

class TinyMCE
{
    public static function beforeInit($options)
    {
        $options['theme_advanced_styles'] = 'List: checkboxes=checked;List: E-mail=email;List: Telefoon=phone;Text: titel=title';
        if (isset($options['theme_advanced_buttons2']) && !preg_match('/styleselect/', $options['theme_advanced_buttons2'])) {
            $options['theme_advanced_buttons2'] = preg_replace('/,/', ',styleselect,', $options['theme_advanced_buttons2'], 1);
        }

        return $options;
    }
}
