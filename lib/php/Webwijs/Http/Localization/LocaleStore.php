<?php

namespace Webwijs\Http\Localization;

use Webwijs\Http\Localization\Locale\LocaleInterface;
use Webwijs\Http\Localization\Locale\UnmodifiableLocale;
use Webwijs\Http\RequestInterface;

/**
 * The LocaleStore is concrete implementation of the {@link LocaleStoreInterface}.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.1.0
 * @since 1.0.0
 */
class LocaleStore implements LocaleStoreInterface
{
    /**
     * A set of locales.
     *
     * @var array
     */
    private $locales = array();

    /**
     * A collection of locales stored by country.
     *
     * @var array
     */
    private $countries = array();
    
    /**
     * A collection of locales stored by language.
     *
     * @var array
     */
    private $languages = array();

    /**
     * Construct a new LocaleStore.
     *
     * @param HttpRequestInterface $request (optional) the HTTP request object containing the language headers.
     * @param Localization $localization (optional) the localization object used to obtain locale objects.
     */
    public function __construct(RequestInterface $request = null, Localization $localization = null)
    {
        if ($request !== null) {
            $localization = ($localization) ?: new Localization();
            $this->addAll($localization->detect($request));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add(LocaleInterface $locale)
    {    
        $added = false;
        $hashCode = $this->computeHashCode($locale);
        if ($added = !isset($this->locales[$hashCode])) {
            $locale   = new UnmodifiableLocale($locale);
            $country  = (($code = $locale->getCountryCode()) !== '') ? $code : 'none';
            $language = (($code = $locale->getLanguageCode()) !== '') ? $code : 'none';

            $this->locales[$hashCode] = $locale;
            $this->countries[$country][]  = $locale;
            $this->languages[$language][] = $locale;
        }

        return $added;
    }
    
    /**
     * Add a collection of {@link LocaleInterface} instances.
     *
     * Only elements which implement the {@link LocaleInterface} will be added to the locale store.
     *
     * @param array|Traversable $locales a collection of locales to add.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function addAll($locales)
    {
        if (!is_array($locales) && !($locales instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($locales) ? get_class($locales) : gettype($locales))
            ));
        }
        
        foreach ($locales as $locale) {
            if ($locale instanceof LocaleInterface) {
                $this->add($locale);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove(LocaleInterface $locale)
    {
        $removed  = false;
        $hashCode = $locale->computeHashCode($locale);
        if ($removed = isset($this->locales[$hashCode])) {   
            unset($this->locales[$hashCode]);
            
            // remove first locale found from country.
            $countryCode = (($code = $locale->getCountryCode()) !== '') ? $code : 'none';
            $countries   = $this->getLocalesByCountry($countryCode);
            foreach ($countries as $key => $country) {
                if ($country->equals($locale)) {
                    unset($countries[$key]);
                    break;
                }
            }
            
            // remove first locale found from languages.
            $languageCode = (($code = $locale->getCountryCode()) !== '') ? $code : 'none';
            $languages    = $this->getLocalesByLanguage($languageCode);
            foreach ($languages as $key => $language) {
                if ($language->equals($locale)) {
                    unset($languages[$key]);
                    break;
                }
            }
        }
        
        return $removed;
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeAll()
    {
        $this->locales   = array();
        $this->countries = array();
        $this->languages = array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isAvailable(LocaleInterface $locale)
    {
        $hashCode = $this->computeHashCode($locale);
        return isset($this->locales[$hashCode]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return array_values($this->locales);
    }
    
    /**
     * Returns a collection of unique ISO 3166 country codes.
     *
     * @return array a collection of unique ISO 3166 country codes.
     */
    public function getCountries()
    {
        return (array_diff(array_keys($this->countries), array('none')));
    }
    
    /**
     * Returns a collection of unique ISO 639-1 language codes.
     *
     * @return array a collection of unique ISO 639-1 language codes.
     */
    public function getLanguages()
    {
        return (array_diff(array_keys($this->languages), array('none')));
    }
    
    /**
     * Returns an {@link ImmutableLocaleInterface} instances for the specified hash code.
     *
     * @param string $hashCode the hash code whose associated locale is to be returned.
     * @return ImmutableLocaleInterface|null a locale for the specified hash code, or null on failure.
     */
    public function getLocale($hashCode)
    {
        return (isset($this->locales[$hashCode])) ? $this->locales[$hashCode] : null;
    }

    /**
     * Returns a collection of {@link ImmutableLocaleInterface} instances for the specified country.
     * Omitting the first argument will return all locales which have no country set.
     *
     * @param string $country (optional) the country for which to return a locale.
     * @return array a collection of locales for the specified language.
     */
    public function getLocalesByCountry($country = 'none')
    {
        return (isset($this->countries[$country])) ? $this->countries[$country] : array();
    }
    
    /**
     * Returns a collection of {@link ImmutableLocaleInterface} instances for the specified language.
     * Omitting the first argument will return all locales which have no language set.
     *
     * @param string $language (optional) the language for which to return a locale.
     * @return array a collection of locales for the specified language.
     */
    public function getLocalesByLanguage($language = 'none')
    {
        return (isset($this->languages[$language])) ? $this->languages[$language] : array();
    }
    
    /**
     * Returns a computated hash code for the specified locale.
     *
     * The computated hashcode consists of a lowercase language and uppercase country code which 
     * are separated by an underscore (e.g. the United States locale yields "en_US" as hash code).
     *
     * @param LocaleInterface the local for which to compute a hash code.
     * @return string the computated hash code.
     */      
    private function computeHashCode(LocaleInterface $locale)
    {
        $parts = array();
        if (($language = trim($locale->getLanguageCode())) !== '') {
            $parts[] = strtolower($language);
        }
        if (($country = trim($locale->getCountryCode())) !== '') {
            $parts[] = strtoupper($country);
        }
        
        return join('_', $parts);
    }
}
