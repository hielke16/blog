<?php

namespace Webwijs\Http\Localization\Locale;

use Webwijs\Common\EquatableInterface;

/**
 * The UnmodifiableLocaleInterface represents a read-only view of the {@link LocaleInterface}.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
interface UnmodifiableLocaleInterface extends EquatableInterface
{
    /**
     * Returns a two-letter lowercase ISO country code.
     *
     * @returns string an ISO country code.
     */
    public function getCountryCode();
    
    /**
     * Returns a two-letter lowercase ISO language code.
     *
     * @returns string an ISO language code.
     */
    public function getLanguageCode();
 
    /**
     * Return the relative degree of preference.
     *
     * @return float a value between 0.0 and 1.0.
     */       
    public function getQuality();
}
