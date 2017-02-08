<?php

namespace Webwijs\Filter;

class HTML
{
    public function shortcode($content)
    {
        $content = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);
		$content = preg_replace('#<br \/>#', '', $content);

		$content = preg_replace('/^\s*<\/p>/s', '', $content);
        $content = preg_replace('/(<p>|<br\s*\/>)\s*$/s', '', $content);

		return $content;
    }
    
}
