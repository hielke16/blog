<?php

namespace Webwijs\Http\Localization\Locale;

/**
 * The UnmodifiableLocale creates a read-only view of {@link LocaleInterface} instance.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
class UnmodifiableLocale implements UnmodifiableLocaleInterface
{
    /**
     * The underlying locale.
     *
     * @var LocaleInterface
     */
    private $locale;
    
    /**
     * Construct a new ImmutableLocale.
     *
     * @param LocaleInterface $locale a locale object whose properties will be exposed as read-only.
     */
    public function __construct(LocaleInterface $locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCountryCode()
    {
        return $this->locale->getCountryCode();
    }
        
    /**
     * {@inheritDoc}
     */
    public function getLanguageCode()
    {
        return $this->locale->getLanguageCode();
    }
        
    /**
     * {@inheritDoc}
     */      
    public function getQuality()
    {
        return $this->locale->getQuality();
    }
    
    /**
     * {@inheritDoc}
     */
    public function equals($locale)
    {
        if ($locale instanceof UnmodifiableLocaleInterface) {
            return $this->locale->equals($locale);
        }
        
        return false;
    }
}
