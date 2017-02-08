<?php

namespace Webwijs\Http\Localization\Detector;

use Webwijs\Http\Localization\Locale\Locale;
use Webwijs\Http\RequestInterface;

/**
 * The AcceptLanguage detector will determine the language preference from the Accept Language header.
 * Almost all browsers will send this header to a server when making an HTTP request.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
class AcceptLanguage extends AbstractDetector
{
    /**
     * {@inheritDoc}
     */
    public function detect(RequestInterface $request)
    {
        $locales = array();
        if ($header = $request->getServer('HTTP_ACCEPT_LANGUAGE')) {
            $languages = explode(',', $header);
            foreach ($languages as $language) {
                $defaults = array(
                    'language_code' => '',
                    'country_code'  => '',
                    'quality'       => '1.0',
                );
                
                if (preg_match('/(?P<language_code>[a-z]{2,8})(?:-(?P<country_code>[a-z]{2,8}))?(?:;q=(?P<quality>\d(?:\.\d)?))?/i', $language, $matches)) {
                    $result    = array_merge($defaults, array_intersect_key($matches, $defaults));
                    $locales[] = new Locale($result['language_code'], $result['country_code'], $result['quality']);
                }
            }
            
            return $locales;
        }
        
        if (($detector = $this->detector) !== null) {
            $locales = $detector->detect($request);
        }
        
        return $locales; 
    }
}
