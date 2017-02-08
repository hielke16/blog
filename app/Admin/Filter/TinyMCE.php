<?php

namespace Theme\Admin\Filter;

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
    
    public static function editorStyle() {
        ?>
        <style>
            .wp_themeSkin .mceListBoxMenu { width: 220px !important; overflow-x: auto !important }
            .wp_themeSkin .mceListBoxMenu table { width: 100% }
        </style>
        <?php
    }
    
    public static function editorInit($init)
    {
        $init['body_class'] = 'std';
        return $init;
    }
    
    /**
     * Link a custom stylesheet file to the TinyMCE visual editor. 
     *
     * @link http://codex.wordpress.org/Function_Reference/add_editor_style
     */
    public static function enqueueStyles()
    {
        add_editor_style(get_template_directory_uri() . '/assets/css/text.css');
    }
    
    /**
     * Enable a dropdown menu to select a style.
     *
     * @param array $buttons the buttons for the toolbar this filter is hooked to.
     * @return array $buttons the buttons with a styleselect button addded to it.
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4
     * @link http://www.wpexplorer.com/wordpress-tinymce-tweaks/
     */
    public static function styleSelect($buttons)
    {
        // determine if this toolbar doesn't already contain this button.
        $buttonExists = false;
        foreach ($buttons as $button) {
            $buttonExists = ('styleselect' == strtolower($button));
            if ($buttonExists) {  
                break;
            }
        }
        
        // add 'styleselect' button.
        if (!$buttonExists) {
            // insert as second button.
            array_splice($buttons, 1, 0, array('styleselect'));
        }
        
        return $buttons;
    }
    
    /**
     * Add custom styles for the 'styleselect' dropdown.
     *
     * @param array $settings the default TinyMCE settings.
     * @see TinyMCE::styleSelect($buttons)
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/tiny_mce_before_init
     * @link http://www.wpexplorer.com/wordpress-tinymce-tweaks/
     */
    public static function stylesDropdown($settings) 
    {
        // array containing custom styles.
		$styles = array(
			array(
				'title'	=> 'Anders..',
				'items'	=> array(
					array(
						'title'		=> 'Lees verder',
						'selector'	=> 'a',
						'classes'	=> 'readmore'
					),
				),
			),
		);
		// merge styles with existing style.
		$settings['style_formats_merge'] = true;
		// add custom styles.
		$settings['style_formats'] = json_encode($styles);

		return $settings;  
    }
}
