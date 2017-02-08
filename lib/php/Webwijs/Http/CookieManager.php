<?php

namespace Webwijs\Http;

use Webwijs\Http\CookieInterface
use Webwijs\Http\Cookie;

/**
 * The CookieManager provides a way to persistent information on the clients computer
 * through the use of cookies.
 * 
 * This manager is capable of retrieving, storing and deleting cookies through the use of 
 * cookie objects. A cookie object stores the information retrieved by the manager. 
 * The CookieManager enforces the single responsibility principle where each class should 
 * have a single responsibility.
 * 
 * @author Chris Harris
 * @version 0.1.0
 * @link http://java.dzone.com/articles/single-responsibility
 */
class CookieManager
{
    /**
     * Store a cookie object on the clients computer.
     *
     * @param CookieInterface a cookie to store.
     * @return bool true if the cookie was stored, false otherwise.
     */
    public function add(CookieInterface $cookie)
    {
        // determine if cookie was created.
        $isCreated = false;
        
        // fail silently if headers have already been sent.
        if (!headers_sent()) {
            $maxAge = $cookie->getMaxAge();
            $path = $cookie->getPath();
            $domain = $cookie->getDomain();
            $isSecure = $cookie->isSecure();
            $isHttpOnly = $cookie->isHttpOnly();
            $value = $cookie->getValue();
            
            // store complex types in an array of cookie data.
            if (is_array($value) || $value instanceof Traversable) {
                foreach ($value as $key => $value) {
                    // name of the cookie.
                    $cookieName = sprintf('%s[%s]', $cookie->getName(), $key);
                    // store client cookie.
                    $isCreated = @setcookie($cookieName, $value, $maxAge, $path, $domain, $isSecure, $isHttpOnly); 
                }
            } else {
                // name of the cookie.
                $cookieName = $cookie->getName();
                // store client cookie.
                $isCreated = @setcookie($cookieName, $value, $maxAge, $path, $domain, $isSecure, $isHttpOnly); 
            }
            
            // unable to create a client cookie.
            if (!$isCreated) {
                // name of the cookie.
                $cookieName = $cookie->getName();
                // remove possible server cookie.
                if (isset($_COOKIE[$cookieName])) {
                    unset($_COOKIE[$cookieName]);
                }
            }
        }
        
        return $isCreated;        
    }
    
    /**
     * Removes a cookie with the given name.
     *
     * @param string $name the name of the cookie to remove.
     * @return bool returns true if a cookie was removed, false otherwise.
     */
    public function remove(CookieInterface $cookie)
    {    
        // determine if cookie was deleted.
        $isRemoved = false;
        
        // set max age of the cookie to a past time.       
        $maxAge = current_time('timestamp') - 3600;
        $path = $cookie->getPath();
        $domain = $cookie->getDomain();
        $isSecure = $cookie->isSecure();
        $isHttpOnly = $cookie->isHttpOnly();
        $value = $cookie->getValue();
            
        // array cookies require each value to be removed.
        if (is_array($value) || $value instanceof Traversable) {
            foreach ($value as $key => $value) {
                // get name of each value contained by the array cookie.
                $cookieName = sprintf('%s[%s]', $cookie->getName(), $key);
                // remove client cookie, and on success update flag.
                if (@setcookie($cookieName, '', $maxAge, $path, $domain, $isSecure, $isHttpOnly)) {
                    $isRemoved = true;
                }
            }
        } else {
            // normal cookies only contains a single scalar value.
            $cookieName = $cookie->getName();
            // remove client cookie.
            $isRemoved = @setcookie($cookieName, '', $maxAge, $path, $domain, $isSecure, $isHttpOnly);
        }
        
        // name of the cookie.
        $cookieName = $cookie->getName();
        // remove possible server cookie.
        if (isset($_COOKIE[$cookieName])) {
            unset($_COOKIE[$cookieName]);
        }
        
        return $isRemoved;
    }
        
    /**
     * Returns a cookie object containing the data stored by the cookie with the given name, or null
     * if no cookie was found for the given name.
     *
     * Other possible information (metadata) will not be set on the cookie object because this information 
     * is not available to PHP after a cookie has been stored on the clients computer.
     *
     * @param string $name the name of the cookie to retrieve.
     * @return a cookie object for the given name, or null if no cookie was found.
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
    
        $cookie = null;
        if ($this->exists($name)) {
            $cookie = new Cookie($name);
        }
        
        return $cookie;
    }
    
    /**
     * Returns an array containing zero or more cookie objects retrieved from
     * the $_COOKIE superglobal.
     *
     * @return array zero or more cookie objects.
     */
    public function getAll()
    {
        // array to hold cookies.
        $cookies = array();
        if (isset($_COOKIE) && is_array($_COOKIE)) {
            // iterate through the $_COOKIE superglobal.
            foreach (array_keys($_COOKIE) as $name) {
                $cookie = $this->get($name);
                // push existing cookies to the array.
                if (!is_null($cookie)) {
                    $cookies[] = $cookie;
                }
            }
        }
        
        return $cookies;
    }
    
    /**
     * Returns true if a cookie with the given name exists, false otherwise.
     *
     * @param string $name the name of cookie to find.
     * @return bool returns true if the cookie exists, false otherwise.
     * @throws \InvalidArgumentException if the provided argument is not of type 'string'. 
     */
    public function exists($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
    
        return isset($_COOKIE[$name]);
    }
}
