<?php

namespace Theme\Filter;

class Embed
{
    public static function responsive($html, $url, $attr) {
        // Only run this process for embeds that don't required fixed dimensions
		$resize = false;
		$accepted_providers = array(
			'youtube',
			'vimeo',
			'slideshare',
			'dailymotion',
			'viddler.com',
			'hulu.com',
			'blip.tv',
			'revision3.com',
			'funnyordie.com',
			'wordpress.tv',
			'scribd.com'
		);

		// Check each provider
		foreach ( $accepted_providers as $provider ) {
			if ( strstr($url, $provider) ) {
				$resize = true;
				break;
			}
		}

		// Remove width and height attributes
		$attr_pattern = '/(width|height)="[0-9]*"/i';
		$whitespace_pattern = '/\s+/';
		$embed = preg_replace($attr_pattern, "", $html);
		$embed = preg_replace($whitespace_pattern, ' ', $embed); // Clean-up whitespace
		$embed = preg_replace('/src="([^"]*)"/', 'src="$1&amp;rel=0"', $embed);
		$embed = trim($embed);
        
        // define valid dimensions.
        $dimensions = self::getDimensions($attr);
		$inline_styles = vsprintf('style="max-width: %s; max-height: %s;"', $dimensions);

		// Add container around the video, use a <p> to avoid conflicts with wpautop()
		$html = '<div class="embed-container"' . $inline_styles . '>';
		$html .= '<div class="embed-container-inner">';
		$html .= $embed;
		$html .= "</div></div>";

		return $html;
    }
    
    /**
     * Returns an array containing a valid width and height.
     * 
     * @param array $dimensions the dimensions to test.
     * @return array the given dimensions, or the default dimensions
     *               if the given dimensions are not valid.
     */
    protected static function getDimensions($dimensions)
    {
        $defaults = array(
            'width' => '100%',
            'height' => 'none',
        );
        $dimensions = array_intersect_key(array_merge($defaults, (array) $dimensions), $defaults);
        
        // allowed suffixes and/or values.
        $allowed = array('px', 'em', '%', 'auto', 'initial', 'inherit', 'none');
        foreach ($dimensions as $key => $dimension) {
            // use default value for non valid dimensions.
            if (!preg_match('#([a-zA-Z%]+)#', $dimension, $matches) || !in_array($matches[1], $allowed)) {
                $dimensions[$key] = $defaults[$key];
            }
        }
        
        return $dimensions;
    }
}
