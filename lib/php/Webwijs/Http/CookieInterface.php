<?php

namespace Webwijs\Http;

/**
 * The CookieInterface is an interface that defines methods to gain access
 * to cookie data stored in the $_COOKIE superglobal, and it also defines
 * all the methods needed to create a new client side cookie.
 * 
 * @author Chris Harris
 * @version 0.1.0
 */
interface CookieInterface
{    
    /**
     * Returns the name of the cookie.
     *
     * @return string returns the the name of the cookie.
     */
    public function getName();

    /**
     * Returns the maximum age that will be used when creating a new cookie.
     *
     * @return int the maximum age in seconds.
     */
    public function getMaxAge();
    
    /**
     * Set the maximum age for the cookie in seconds.
     *
     * @param int $maxAge a possitive numeric value.
     * @return CookieInterface allows for method chaining.
     */
    public function setMaxAge($maxAge);
    
    /**
     * Returns the path on the server in which the cookie is available.
     *
     * @return string the path on the server.
     */
    public function getPath();
    
    /**
     * Set the path on the server in which the cookie is available.
     *
     * @param string $path the path on the server.
     * @return CookieInterface allows for method chaining.
     */
    public function setPath($path);
    
    /**
     * Returns the domain that the cookie is available to.
     *
     * @return string the domain that the cookie is available to.
     */
    public function getDomain();
    
    /**
     * Set the domain that the cookie is available to.
     *
     * @param string|null set the domain that cookie is available to, or if
     *                    null the site domain will be used.
     * @return Cookie allows for method chaining.
     */
    public function setDomain($domain);
    
    /**
     * Determines if the cookie should only be transmitted through the HTTP protocol.
     *
     * @param bool $httpOnly a boolean value indicating if the cookie should only be
     *                       transmitted through the HTTP protocol.
     * @return Cookie allows for method chaining.
     */
    public function setHttpOnly($httpOnly);
    
    /**
     * Returns true if the cookie should only be transmitted through the HTTP protocol, 
     * false otherwise.
     *
     * @return bool returns a boolean value indicating if the cookie should only be 
     *              transmitted through the HTTP protocol.
     */
    public function isHttpOnly();
    
    /**
     * Determines if the cookie should only be transmitted over a secure connection.
     *
     * @param bool $secure a boolean value indicating if the cookie should only be
     *                     available on a secure connection.
     * @return Cookie allows for method chaining.
     */
    public function setSecure($secure);
    
    /**
     * Returns true if the cookie should only be transmitted over a secure connection, 
     * false otherwise.
     *
     * @return bool returns a boolean value indicating if the cookie should only be 
     *              available over a secure connection.
     */
    public function isSecure();
    
    /**
     * Returns the cookie value.
     *
     * @return mixed the cookie value.
     */
    public function getValue();
    
    /**
     * Set the cookie value.
     *
     * @param mixed $value the cookie value.
     * @return Cookie allows for method chaining.
     */
    public function setValue($value);
}
