<?php

namespace Webwijs\Http\Localization\Locale;

/**
 * The LocaleInterface contains information about (browser) languages that are were sent with an HTTP request.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
interface LocaleInterface extends UnmodifiableLocaleInterface
{
    /**
     * Set a two-letter ISO country code.
     *
     * @param string $code an ISO language code.
     */
    public function setCountryCode($code);

    /**
     * Set a two-letter ISO language code.
     *
     * @param string $code an ISO language code.
     */
    public function setLanguageCode($code);

    /**
     * Set the relative degree of preference.
     *
     * @param float $quality a value between 0.0 and 1.0.
     */       
    public function setQuality($quality);
}
