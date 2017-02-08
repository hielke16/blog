<?php

namespace Webwijs\Http\Localization;

use Webwijs\Http\Localization\Locale\LocaleInterface;

/**
 * The LocaleStore represents a storage for {@link LocaleInterface} instances.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.1.0
 * @since 1.0.0
 */
interface LocaleStoreInterface
{
    /**
     * Add the specified {@link LocaleInterface} instance to this store.
     *
     * @param LocaleInterface $locale the local to add.
     * @return bool true if the locale was added, otherwise false if the local already exists.
     */
    public function add(LocaleInterface $locale);
    
    /**
     * Removes if present the specified {@link LocaleInterface} instances from this store.
     *
     * @param LocaleInterface $locale the locale to remove.
     * @return bool true if the specified locale was removed, otherwise false if it doesn't exist.
     */
    public function remove(LocaleInterface $locale);
    
    /**
     * Removes all locales from this store. The LocaleStore will be empty after this call returns.
     *
     * @return void
     */
    public function removeAll();
    
    /**
     * Returns true if the specified {@link LocaleInterface} if present in this store.
     * 
     * The locale quality is not taken into account when searching for a locale within the store.
     *
     * @param LocaleInterface the locale whose presence will be tested.
     * @return bool true if the specified locale is available, otherwise false.
     */
    public function isAvailable(LocaleInterface $locale);
    
    /**
     * Returns a collection {@link ImmutableLocaleInterface} instances contained by this store.
     *
     * @return array a collection of {@link ImmutableLocaleInterface} instances.
     */
    public function toArray();
}
