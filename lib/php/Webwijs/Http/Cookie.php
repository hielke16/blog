<?php

namespace Webwijs\Http;

use ReflectionClass;
use Webwijs\Http\Request;

/**
 * The Cookie class can be used to manage a single cookie on the client computer. 
 * Possible options include but are not limited to setting the max age, domain 
 * and security of the cookie.
 * 
 * @author chris harris
 * @version 0.2.0
 */ 
class Cookie implements CookieInterface
{
    /**
     * The name of the cookie.
     *
     * @var strings
     */
    protected $name;
   
    /**
     * The maximum age of the cookie in seconds.
     *
     * @var int
     */
    protected $maxAge = 0;
    
    /**
     * The path on the server in which the cookie is available.
     *
     * @var string
     */
    protected $path = '/';
    
    /**
     * The domain that the cookie is available to.
     * 
     * @var string
     */
    protected $domain;
    
    /**
     * Determines if the cookie should only be transmitted over a secure connection.
     *
     * @var bool
     */
    protected $isSecure = false;
    
    /**
     * Determines if the cookie should only be transmitted through the HTTP protocol.
     *
     * @var bool
     */
    protected $httpOnly = false;
    
    /**
     * Allow this object to introspect itself.
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Create a new cookie with the given name.
     *
     * @param string name the name of the cookie to retrieve and save data to.
     * @param array $options optional arguments 
     */
    public function __construct($name, $options = null)
    {
        // set name of cookie.
        $this->setName($name);
        
        // set domain from current HTTP request.
        $request = new Request();
        if ($domain = $request->getHttpHost()) {
            $this->setDomain($domain);
        }
        
        // set a default age of 7 days.
        $this->setMaxAge((60 * 60 * 24 * 7));
        
        // override default options.
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * Set name of the cookie.
     *
     * @param string $name the name of the cookie.
     */
    protected function setName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        } else if (strlen($name) <= 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string whose string length is larger than 0; received "%s"',
                __METHOD__, strlen($name)
            ));
        }
        
        $this->name = $name; 
    }
    
    /**
     * Returns the name of the cookie.
     *
     * @return string returns the the name of the cookie.
     */
    public function getName()
    {
        return $this->name;
    }    
    
    /**
     * Set options used to create a cookie from the stored data.
     * 
     * @param array|\Traversable $options one or more options.
     * @return Cookie allows for method chaining.
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object of options; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        
        // allow this object to introspect itself.
        $reflection = $this->reflection;
        if (!($reflection instanceof ReflectionClass)) {
            $this->reflection = $reflection = new ReflectionClass($this);
        }
        
        // find and call appropriate method for each option.
        foreach ($options as $option => $value) {
            // split string by hyphen or underscore.
            $names = preg_split("#[\-_]+#", $option);
            if (is_array($names) && !empty($names)) {
                // uppercase first letter of each word.
                $names = array_map('ucfirst', $names);
            } else {
                // uppercase first letter of word.
                $names[] = ucfirst($option);
            }
            
            // the name for a possible (setter) method.
            $methodName = sprintf('set%s', implode('', $names));
            // methods that are forbidden.
            $forbidden = array('setOptions');
            if (!in_array($methodName, $forbidden)) {
                // find a method with the given name.
                if ($reflection->hasMethod($methodName)) {
                    $method = $reflection->getMethod($methodName);
                    // only invoke methods that are public.
                    if ($method->isPublic() && $method->getNumberOfParameters() == 1) {
                        $method->invoke($this, $value);
                    } else {
                        // set as property.
                        $this->{$option} = $value;
                    }
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Returns the maximum age that will be used when creating a new cookie.
     *
     * @return int the maximum age in seconds.
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }
    
    /**
     * Set the maximum age for the cookie in seconds.
     *
     * @param int $maxAge a possitive numeric value.
     * @return Cookie allows for method chaining.
     */
    public function setMaxAge($maxAge)
    {
        if (!is_numeric($maxAge)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($maxAge) ? get_class($maxAge) : gettype($maxAge))
            ));
        }
        
        $this->maxAge = ($maxAge == 0) ? 0 : (current_time('timestamp') + $maxAge);
        return $this;
    }
    
    /**
     * Returns the path on the server in which the cookie is available.
     *
     * @return string the path on the server.
     */
    public function getPath()
    {
        return $this->path;
    }    
    
    /**
     * Set the path on the server in which the cookie is available.
     *
     * @param string $path the path on the server.
     * @return Cookie allows for method chaining.
     */
    public function setPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($path) ? get_class($path) : gettype($path))
            ));
        }
        
        $this->path = (strlen($path) > 0) ? $path : '/';
        return $this;
    }    
    
    /**
     * Returns the domain that the cookie is available to.
     *
     * @return string the domain that the cookie is available to.
     */
    public function getDomain()
    {
        return $this->domain;
    }
     
    /**
     * Set the domain that the cookie is available to.
     *
     * @param string|null set the domain that cookie is available to, or if
     *                    null the site domain will be used.
     * @return Cookie allows for method chaining.
     */
    public function setDomain($domain)
    {
        if (!is_string($domain)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($domain) ? get_class($domain) : gettype($domain))
            ));
        }
        
        $this->domain = $domain;
        return $this;
    } 
       
    /**
     * Determines if the cookie should only be transmitted through the HTTP protocol.
     *
     * @param bool $httpOnly a boolean value indicating if the cookie should only be
     *                       transmitted through the HTTP protocol.
     * @return Cookie allows for method chaining.
     */
    public function setHttpOnly($httpOnly)
    {    
        $this->httpOnly = (bool) $httpOnly;
        return $this;
    }    
    
    /**
     * Returns true if the cookie should only be transmitted through the HTTP protocol, 
     * false otherwise.
     *
     * @return bool returns a boolean value indicating if the cookie should only be 
     *              transmitted through the HTTP protocol.
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }    
    
    /**
     * Determines if the cookie should only be transmitted over a secure connection.
     *
     * @param bool $secure a boolean value indicating if the cookie should only be
     *                     available on a secure connection.
     * @return Cookie allows for method chaining.
     */
    public function setSecure($secure)
    {
        $this->isSecure = (bool) $secure;
        return $this;
    }
     
    /**
     * Returns true if the cookie should only be transmitted over a secure connection, 
     * false otherwise.
     *
     * @return bool returns a boolean value indicating if the cookie should only be 
     *              available over a secure connection.
     */
    public function isSecure()
    {
        return $this->isSecure;
    }
    
    /**
     * Returns the cookie value.
     *
     * @return mixed the cookie value.
     */
    public function getValue()
    {
        $cookieName = $this->getName();
        if ($this->exists()) {
            return $_COOKIE[$cookieName];
        }
        return null;        
    }
    
    /**
     * Returns true if this cookie already exists, false otherwise.
     *
     * @return bool true if the cookie exists, false otherwise.
     */
    public function exists()
    {
        $cookieName = $this->getName();
        return (isset($_COOKIE[$cookieName]));
    }
    
    /**
     * Set the cookie value.
     *
     * @param mixed $value the cookie value.
     * @return Cookie allows for method chaining.
     */
    public function setValue($value)
    {
        // name of the cookie.
        $cookieName = $this->getName();
        // set cookie value.
        $_COOKIE[$cookieName] = $value;

        return $this;
    }    
}
