<?php

namespace Webwijs\Http\Session;

use Webwijs\Http\Session\SessionInterface;
use Webwijs\Http\Session\Storage\StorageInterface;
use Webwijs\Http\Session\Handler\SaveHandlerInterface;

class Session implements SessionInterface
{
    /**
     * This flag is toggled between starting and closing a session.
     *
     * @var boolean
     */
    protected $active = false;

    /**
     * A custom session handler for storing and retrieving session data.
     *
     * @var SaveHandlerInterface
     */
    protected $saveHandler;

    /**
     * An array object to store data from the $_SESSION superglobal.
     *
     * @var SessionStorage
     */
    protected $storage;
    
    /**
     * Default storage class to use when no storage provided
     * @var string
     */
    protected $defaultStorageClass = 'Webwijs\Http\Session\Storage\SessionStorage';

    /**
     * Create a new session.
     *
     * @param StorageInterface $storage the storage to hold data from the $_SESSION superglobal.
     * @param SaveHandlerInterface $saveHandler a session handler used to store the session data.
     * @throws \RuntimeException if the default storage class could not be found.
     */
    public function __construct(StorageInterface $storage = null, SaveHandlerInterface $saveHandler = null)
    {
        if (null === $storage) {
            if (!class_exists($this->defaultStorageClass)) {
                throw new \RuntimeException(sprintf(
                    'Unable to locate storage class "%s"; class does not exist',
                    $this->defaultStorageClass
                ));
            }
            
            $storage = new $this->defaultStorageClass();

            if (!$storage instanceof StorageInterface) {
                throw new \RuntimeException(sprintf(
                    'Default storage class %s is invalid; must implement StorageInterface',
                    $this->defaultConfigClass
                ));
            }
        }
        $this->setStorage($storage);
        
        if (null !== $saveHandler) {
            $this->setSaveHandler($saveHandler);
        }
        
        // write session data after script execution finishes or exit() is called.
        register_shutdown_function(array($this, 'save'));
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $isStarted = $this->isStarted();
        if (!$isStarted) {
            $saveHandler = $this->getSaveHandler();
            if ($saveHandler instanceof SaveHandlerInterface) {
                // register a session handler.
                $this->registerSaveHandler($saveHandler);
            }
        
            // try to start a session.
            if (!session_start()) {
                throw new \RuntimeException('Failed to start the session');
            }
            $isStarted = true;
        }
        
        if ($isStarted) {
            if (($storage = $this->getStorage()) && $storage instanceof StorageInterface && $_SESSION !== $storage) {
                // populate storage with data from the $_SESSION superglobal.
                $storage->fromArray($_SESSION);
            }
            // use storage object to store future session data.
            $_SESSION = $storage;
            
            // still required for PHP versions lower than 5.4.0.
            $this->setActive(true);
        }
        return $isStarted;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        $isStarted = false;
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $isStarted = (session_status() != PHP_SESSION_NONE);
        } else {
            $isStarted = ($this->getSessionId() != '');
        }
        
        return $isStarted;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return session_id();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSessionId($id, $deleteOldSession = false)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException(sprinf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($id) ? get_class($id) : gettype($id))
            ));
        }
        
        if ($this->isActive()) {
            throw new \LogicException(sprintf(
                'Session has already been started, to change the session ID call %s::regenerateSessionId($destroy)',
                __CLASS__
            ));
        }
    
        session_id($id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function regenerateSessionId($destroy = false, $lifetime = null)
    {
        // set lifetime of session data.
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }
    
        $ret = session_regenerate_id((bool) $destroy);

        // required for bug fix (https://bugs.php.net/bug.php?id=61470)
        session_write_close();
        if (isset($_SESSION)) {
            $backup = $_SESSION;
            session_start();
            $_SESSION = $backup;
        } else {
            session_start();
        }
    
        return $ret;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprinf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
    
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the name of an active session');
        }

        session_name($name);
    }
    
    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if ($storage = $this->getStorage()) {
            $storage[$key] = $value;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $value = $default;
        if (($storage = $this->getStorage()) && isset($storage[$key])) {
            $value = $storage[$key];
        }
        return $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save()
    {
        /*
         * The assumption is that we're using PHP's ext/session.
         * session_write_close() will actually overwrite $_SESSION with an
         * empty array on completion -- which leads to a mismatch between what
         * is in the storage object and $_SESSION. To get around this, we
         * temporarily reset $_SESSION to an array, and then re-link it to
         * the storage object.
         */
        if ($storage = $this->getStorage()) {
            $_SESSION = $storage->toArray(true);
            session_write_close();
            $storage->fromArray($_SESSION);
        }
        
        // still required for PHP versions lower than 5.4.0.
        $this->setActive(false);
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $retval = null;
        if (($storage = $this->getStorage()) && isset($storage[$key])) {
            $retval = $storage[$key];
            unset($storage[$key]);
        }
        
        return $retval;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->getStorage()->clear();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSaveHandler(SaveHandlerInterface $saveHandler)
    {
        if (null === $saveHandler) {
            throw new \InvalidArgumentException(sprinf(
                '%s: needs to implement SaveHandlerInterface; received "%s"',
                __METHOD__,
                (is_object($saveHandler) ? get_class($saveHandler) : gettype($saveHandler))
            ));
        }
        
        $this->saveHandler = $saveHandler;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStorage(StorageInterface $storage)
    {
        if (null === $storage) {
            throw new \InvalidArgumentException(sprinf(
                '%s: needs to implement StorageInterface; received "%s"',
                __METHOD__,
                (is_object($storage) ? get_class($storage) : gettype($storage))
            ));
        }
        
        $this->storage = $storage;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setMetadata($key, $value, $overwriteArray = false)
    {
        if ($storage = $this->getStorage()) {
            $metadata = $storage->setMetadata($key, $value, $overwriteArray = false);
        }  
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = null;
        if ($storage = $this->getStorage()) {
            $metadata = $storage->getMetadata($key);
        }
        
        return $metadata;
    }
    
    /**
     * Register a custom save handler for session storage.
     *
     * @param SaveHandlerInterface $saveHandler the handler to use.
     * @return bool returns true on success or false on failure.
     * @throws \InvalidArgumentException if no save handler is provided.
     * @link http://php.net/manual/en/function.session-set-save-handler.php
     */
    protected function registerSaveHandler(SaveHandlerInterface $saveHandler)
    {
        if (null === $saveHandler) {
            throw new \InvalidArgumentException(sprinf(
                '%s: expects an object that implements the SaveHandlerInterface; received "%s"',
                __METHOD__,
                (is_object($saveHandler) ? get_class($saveHandler) : gettype($saveHandler))
            ));
        }
    
        return session_set_save_handler(
            array($saveHandler, 'open'),
            array($saveHandler, 'close'),
            array($saveHandler, 'read'),
            array($saveHandler, 'write'),
            array($saveHandler, 'destroy'),
            array($saveHandler, 'gc')
        );
    }
    
    /**
     * Set the active to true if a session is active, and false if session data is saved.
     *
     * @param bool $active if true the session is active, false if session data is saved 
     *                     and the session is closed.
     */
    private function setActive($active) 
    {
        // only applies to PHP version lower than 5.4.0
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->active = (bool) $active;
        }
    }
    
    /**
     * Returns true if the session is active, false otherwise.
     *
     * @return bool true if the session is active, false otherwise.
     */
    private function isActive()
    {
        // for PHP 5.4.0 and newer the session status is retrievable.
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return (\PHP_SESSION_ACTIVE === session_status());
        }
        return $this->active;
    }
}
