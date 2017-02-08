<?php

namespace Webwijs\Http\Session;

use Webwijs\Http\Session\Handler\SaveHandlerInterface;
use Webwijs\Http\Session\Storage\StorageInterface;

interface SessionInterface
{
    /**
     * Starts the session.
     *
     * @throws \RuntimeException If the session cannot be started.
     * @return bool true if the session has started, false otherwise.
     */
    public function start();
    
    /**
     * Returns true if the session has already started, false otherwise.
     *
     * @return bool true if the session has started, false otherwise.
     */
    public function isStarted();
    
    /**
     * Returns the session id for the current session.
     *
     * @return string the session id for the current session.
     */
    public function getSessionId();
    
    /**
     * Set a new session id for the current session.
     *
     * @param string $id the new session id for the current session.
     * @throw \InvalidArgumentException if the given argument is not of type string.
     * @throw \LogicException if a session is already active.
     * @link http://php.net/manual/en/function.session-id.php
     */
    public function setSessionId($id);
    
    /**
     * Update the current session id with a newly created one.
     *
     * @param bool $destroy Whether to delete the old session data or not.
     * @param int lifetime Sets the lifetime of session data.
     * @link http://php.net/manual/en/function.session-regenerate-id.php
     */
    public function regenerateSessionId($destroy = false, $lifetime = null);
    
    /**
     * Returns the session name.
     *
     * @return string the name of the current session.
     */
    public function getName();

    /**
     * Set the session name.
     *
     * @param string $name the name of the session.
     * @throw \InvalidArgumentException if the given argument is not of type string.
     * @throws \LogicException if a session if already active.
     */
    public function setName($name);
    
    /**
     * Store a new attribute in the session with the given key.
     *
     * @param mixed $key the key associated with the attribute to store.
     * @param mixed $value the attribute the store. 
     */
    public function set($key, $value);
    
    /**
     * Returns the session attribute with the given key, or null if no attribute was found.
     *
     * @param mixed $key the key for which to retrieve an attribute.
     * @param mixed $default the default to return if no session attribute was found for the given key.
     */
    public function get($key, $default = null);
    
    /**
     * Save and close the current session.
     *
     * @throws \RuntimeException If the session is saved without being opened, or the session is already closed.
     * @link http://php.net/manual/en/function.session-write-close.php
     */
    public function save();
    
    /**
     * Remove the session attribute with the given key.
     *
     * @param mixed $key the key for which to remove an attribute.
     * @return mixed returns the attribute associated with the given key, or null if no attribute was found.
     */
    public function remove($key);
    
    /**
     * Clear all session data.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Set a session handler to provide custom session storage capabilities. A handler could be used to implement 
     * database or memcache storage of sessions.
     *
     * @param SaveHandlerInterface $saveHandler a session save handler.
     * @throws \InvalidArgumentException if the given argument is not of type SaveHandlerInterface.
     */
    public function setSaveHandler(SaveHandlerInterface $saveHandler);
    
    /**
     * Returns if set the session handler used for storing sessions, if no handler is set the PHP sessions default
     * save handler is used.
     *
     * @return SaveHandlerInterface|null a session save handler, or null if the PHP sessions default
     *                                                               save handler is used.
     */
    public function getSaveHandler();
    
    /**
     * Set a storage object for storing session data from the $_SESSION superglobal.
     * 
     * @param StorageInterface $storage a storage object.
     */
    public function setStorage(StorageInterface $storage);
    
    /**
     * Returns the storage object.
     *
     * @return StorageInterface a storage object.
     */
    public function getStorage();
    
    /**
     * Set metadata for this storage object.
     *
     * This method enforces the Law of Demeter (LoD) or principle of least knowledge so that knowledge
     * about the inner working of this class is kept to a minimum.
     *
     * @param  string $key the key associated with the metadata.
     * @param  mixed $value the metadata to store.
     * @param  bool $overwriteArray whether to overwrite or merge array values; by default, merges.
     */
    public function setMetadata($key, $value, $overwriteArray = false);
    
    /**
     * Returns metadata for the storage object or a specific metadata key.
     *
     * This method enforces the Law of Demeter (LoD) or principle of least knowledge so that knowledge
     * about the inner working of this class is kept to a minimum.
     *
     * @param null|int|string $key the metadata key for which to return metadata.
     * @return mixed the metadata for the given key, or null if no metadata was found.
     */
    public function getMetadata($key = null);
}
