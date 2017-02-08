<?php

namespace Webwijs\Http\Localization\Locale;

/**
 * The Locale is a concrete implementation of the {@link LocaleInterface} and provides infomation
 * about (browser) languages that are sent with a HTTP request.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
class Locale implements LocaleInterface
{
    /**
     * A two-letter lowercase ISO language code.
     *
     * @var string
     */
    private $languageCode = '';
    
    /**
     * A two-letter lowercase ISO country code.
     *
     * @var string
     */
    private $countryCode = '';
    
    /** 
     * The relative degree of preference.
     *
     * @var int
     */
    private $quality = 0.5;
    
    /**
     * Construct a new Locale.
     *
     * @param string $code an ISO language code.
     * @param string $code an ISO language code.
     * @param float $quality (optional) a value between 0 and 1.
     */
    public function __construct($languageCode, $countryCode, $quality = 0.0)
    {
        $this->setLanguageCode($languageCode);
        $this->setCountryCode($countryCode);
        $this->setQuality($quality);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setCountryCode($code)
    {
        if (!is_string($code)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($code) ? get_class($code) : gettype($code))
            ));
        }
    
        $this->countryCode = $code;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setLanguageCode($code)
    {
        if (!is_string($code)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($code) ? get_class($code) : gettype($code))
            ));
        }
    
        $this->languageCode = $code;
    }
        
    /**
     * {@inheritDoc}
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }
        
    /**
     * {@inheritDoc}
     */
    public function setQuality($quality)
    {
        if (!is_numeric($quality)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($quality) ? get_class($quality) : gettype($quality))
            ));
        } else if ($quality < 0 || $quality > 1) {
            throw new \LogicException(sprintf(
                '%s: expects a numeric argument between 0.0 and 1.0; received "%s"',
                __METHOD__,
                $quality
            ));
        }
        
        $this->quality = round($quality, 1);
    }
        
    /**
     * {@inheritDoc}
     */      
    public function getQuality()
    {
        return $this->quality;
    }
    
    /**
     * {@inheritDoc}
     */
    public function equals($locale)
    {
        if ($locale instanceof UnmodifiableLocaleInterface) {
            return ($locale->getCountryCode() === $this->getCountryCode() && 
                    $locale->getLanguageCode() === $this->getLanguageCode());
        }
        
        return false;
    }
}
