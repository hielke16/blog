<?php

namespace Webwijs\Http\Session\Storage;

/**
 * The StorageInterface defines a storage object that can store the session data
 * usually found in the $_SESSION superglobal.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
interface StorageInterface extends
\ArrayAccess,
\Countable
{
    /**
     * Set metadata for this storage object.
     *
     * @param  string $key the key associated with the metadata.
     * @param  mixed $value the metadata to store.
     * @param  bool $overwriteArray whether to overwrite or merge array values; by default, merges.
     */
    public function setMetadata($key, $value, $overwriteArray = false);
    
    /**
     * Returns metadata for the storage object or a specific metadata key.
     *
     * @param null|int|string $key the metadata key for which to return metadata.
     * @return mixed the metadata for the given key, or null if no metadata was found.
     */
    public function getMetadata($key = null);
    
    /**
     * Clear this storage object.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Populate this storage with data from the given array.
     *
     * The data previously stored by the store will removed in this process.
     *
     * @param array $array an array with data.
     */
    public function fromArray(array $array);
    
    /**
     * Returns an array containing all of the values in this storage in proper sequence.
     *
     * @param bool $includeMetaData whether to include metadata.
     * @return array an array containing all of the values in this storage.
     */
    public function toArray($includeMetaData = false);
}
