<?php

namespace Webwijs\Net;

use Webwijs\Net\MessageHeader;
use Webwijs\Util\Arrays;
use Webwijs\Util\Strings;

/**
 * The HttpRequest class contains all the necessary information to create a
 * connection with a RESTful web service.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class HttpRequest
{    
    /**
     * the request url.
     *
     * @var string
     */
    protected $baseUrl;
    
    /**
     * the path to locate a specific resource.
     *
     * @var string
     */
    protected $path;
    
    /**
     * query parameters send with the request.
     */
    protected $queryParams = array();
    
    /**
     * the request type. (GET, POST, PUT or DELETE)
     *
     * @var string
     */
    protected $requestMethod;
    
    /**
     * the request headers.
     *
     * @var MessageHeader
     */
    protected $requestHeaders;
    
    /**
     * the post body.
     *
     * @var string
     */
    protected $postBody;
    
    /**
     * identifies the client that created this request.
     *
     * @var string
     */
    protected $userAgent;
    
    /**
     * The referer to send with the HTTP header.
     *
     * @var string|null
     */
    protected $referer;
    
    /**
     * Create a new request from the given information.
     *
     * @param string $url the request url.
     * @param string $method the request method.
     * @param array|\Traversable $headers the request headers.
     * @param string|array|\Traversable $postBody the post body.
     */
    public function __construct($url, $method = 'GET', $headers = array(), $postBody = null)
    {
        $this->setUrl($url);
        $this->setRequestMethod($method);
        $this->setRequestProperties($headers);
        $this->setPostBody($postBody);
    }
    
    /**
     * Set the url for this request.
     *
     * @param string $url the url for this request.
     */
    public function setUrl($url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($url) ? get_class($url) : gettype($url))
            ));
        }
        
        // merge defaults with parts from the parsed url.
        $parts = array('scheme' => '', 'host' => '', 'port' => '');
        if (($urlParts = parse_url($url)) && is_array($urlParts)) {
            $parts = array_merge($parts, $urlParts);
        }
        
        // create a base url.
        $this->baseUrl = sprintf('%s://%s', $parts['scheme'], $parts['host']);
        if (is_numeric($parts['port'])) {
            $this->baseUrl .= sprintf(':%d', $parts['port']);
        }
        
        // store path.
        $this->path = '';
        if (isset($parts['path'])) {
            // separate path into pieces.
            $paths = explode('/', $parts['path']);
            if (empty($paths)) {
                $paths = array($parts['path']);
            }
            // url encode each part of the path.
            $paths = array_map(array($this, 'encodeUrl'), $paths);

            $this->path = implode('/', $paths);
        }
        
        // create query parameters.
        $this->queryParams = array();
        if (isset($parts['query'])) {
            $this->queryParams = $this->parseQuery($parts['query']);
        }
    }
    
    /**
     * Returns the url for this request.
     *
     * @return string the url for this request.
     */
    public function getUrl()
    {
        $url = $this->baseUrl;
        if (is_string($this->path) && strlen($this->path) > 0) {
            $url .= Strings::addLeading($this->path, '/');
        }
        if (is_array($this->queryParams) && !empty($this->queryParams)) {
            $url .= sprintf('?%s', $this->buildQuery($this->queryParams));
        }
        
        return $url;
    }
    
    /**
     * Set the request method.
     *
     * @param string $method the request method.
     */
    public function setRequestMethod($method)
    {
        $allowedMethods = array('GET', 'POST', 'PUT', 'DELETE');
        if (!in_array(strtoupper($method), $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects one of the following request types: %s; received "%s"',
                __METHOD__,
                implode(', ', $allowedMethods),
                (is_object($method) ? get_class($method) : gettype($method))
            )); 
        }
        
        $this->requestMethod = strtoupper($method);
    }
    
    /**
     * Returns the request method.
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
    
    /**
     * Set the given request property to this request, if an existing request property
     * already exists it's value is overriden with the given value.
     *
     * @param mixed $key the name of the request property.
     * @param mixed $value the value for this request property.
     * @return HttpRequest allow method chaining.
     */
    public function setRequestProperty($key, $value)
    {
        // lazy initialize a new collection object.
        if (is_null($this->requestHeaders)) {
            $this->requestHeaders = new MessageHeader();
        }
        
        // add new property.
        $this->requestHeaders->offsetSet($key, $value);
        
        return $this;
    }
    
    /**
     * Set multiple request properties to this request, if an existing request property
     * already exists it's value is overriden with the given value.
     *
     * @param array|\Traversable an array containing one or more request properties.
     * @return HttpRequest allow method chaining
     */
    public function setRequestProperties($requestProperties)
    {
        if(!is_array($requestProperties) && !($requestProperties instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($requestProperties) ? get_class($requestProperties) : gettype($requestProperties))
            ));
        }
        
        foreach ($requestProperties as $propertyName => $propertyValue) {
            $this->setRequestProperty($propertyName, $propertyValue);
        }
        
        return $this;
    }
    
    /**
     * Add the given request property to this request, if an existing request property
     * already exists the given value is added to that property.
     *
     * @param mixed $key the name of the request property.
     * @param mixed $value the value for this request property.
     * @return HttpRequest allow method chaining.
     */
    public function addRequestProperty($key, $value)
    {
        // lazy initialize a new collection object.
        if (is_null($this->requestHeaders)) {
            $this->requestHeaders = new MessageHeader();
        }
        
        // add new request property.
        if ($this->requestHeaders->offsetExists($key)) {
            // copy iterator to array.
            $requestProperty = $this->requestHeaders->offsetGet($key);
            if ($property instanceof \Traversable) {
                if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
                    $requestProperty = iterator_to_array($property);
                } else {
                    $requestProperty = Arrays::iteratorToArray($property);
                }
            } 
            
            // create array with existing value(s).
            if (!is_array($requestProperty)) {
                $requestProperty = array($requestProperty);
            }
            // add new value to array.
            $requestProperty[] = $value;
            
            // update existing property.
            $this->requestHeaders->offsetSet($key, $property);
        } else {
            // add new property.
            $this->requestHeaders->offsetSet($key, $value);
        }
        
        return $this;
    }
    
    /**
     * Add multiple request properties to this request, if an existing request property
     * already exists the given value is added to that property.
     *
     * @param array|\Traversable an array containing one or more request properties.
     * @return HttpRequest allow method chaining
     */
    public function addRequestProperties($requestProperties)
    {
        if(!is_array($requestProperties) || !($requestProperties instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($requestProperties) ? get_class($requestProperties) : gettype($requestProperties))
            );
        }
        
        foreach ($requestProperties as $propertyName => $propertyValue) {
            $this->addRequestProperty($propertyName, $propertyValue);
        }
        
        return $this;
    }
    
    /**
     * Removes all request properties from this request.
     *
     * @return void.
     */
    public function clearRequestProperties()
    {
        if (!is_null($this->requestHeaders) && ($this->requestHeaders instanceof MessageHeader)) {
            $this->requestHeaders->clear();
        }
    }
    
    /**
     * Returns true if this request contains a request property with the given key,
     * false otherwise.
     *
     * @param mixed $key the name of the request property to be tested.
     * @return bool true if the given request property exists, false otherwise.
     */
    public function hasRequestProperty($key)
    {
        if (!is_null($this->requestHeaders) && ($this->requestHeaders instanceof MessageHeader)) {
            return $this->requestHeaders->offsetExists($key);
        }
        
        return false;
    }
    
    /**
     * Returns the request property for the given property name, of null if no property
     * exists for the given property name.
     *
     * @param mixed the name of the request property whose value will be returned.
     * @return mixed the property for the given property name, or null.
     */
    public function getRequestProperty($Key)
    {
        $requestProperty = null;
        if ($this->hasRequestProperty($key)) {
            $requestProperty = $this->requestHeaders->offsetGet($key);
        }
        
        return $requestProperty;
    }
    
    /**
     * Retuns an (associative) array containing all request properties,
     * or null when unable to return an array.
     *
     * @return array|null returns an array with all request properties.
     */
    public function getRequestProperties()
    {
        // lazy initialize a new collection object.
        if (is_null($this->requestHeaders)) {
            $this->requestHeaders = new MessageHeader();
        }
        
        // copy iterator to array.
        $requestProperties = null;
        if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
            $requestProperties = iterator_to_array($this->requestHeaders);
        } else {
            $requestProperties = Arrays::iteratorToArray($this->requestHeaders);
        }
        
        return $requestProperties;
    }
    
    /**
     * Set a new query parameter.
     *
     * @param string the name of the query parameter.
     * @param mixed the value for the query parameter.
     */
    public function setQueryParam($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string as first argument; received "%s"',
                __METHOD__,
                (is_object($key) ? get_class($key) : gettype($key))
            ));
        }
        
        $this->queryParams[$key] = $value;
    }
    
    /**
     * Returns the value from the query parameters for the given key.
     *
     * @param string $key the name of the query parameter.
     * @param mixed $default the default value if the key does not exist.
     * @return mixed the value associated with the query parameter.
     */
    public function getQueryParam($key, $default = null)
    {    
        return (isset($this->queryParams[$key])) ? $this->queryParams[$key] : $default;
    }
    
    /**
     * Returns all query parameters set for this request.
     *
     * @return array the query parameters.
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    /**
     * Set the post body for this request.
     *
     * @param string|array|\Traversable $postBody the post body.
     */
    public function setPostBody($postBody)
    {
        if(is_array($postBody) || $postBody instanceof \Traversable) {
            // copy the iterator into an array.
            if ($postBody instanceof \Traversable) {
                if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
                    $postBody = iterator_to_array($postBody);
                } else {
                    $postBody = Arrays::iteratorToArray($postBody);
                }
            }
            
            // create querystring from array.
            $postBody = $this->buildQuery($postBody);
        } 
            
        $this->postBody = $postBody;
    }
    
    /**
     * Return the post body for this request.
     *
     * @return string the post body.
     */
    public function getPostBody()
    {
        return $this->postBody;
    }
    
    /**
     * Set the name by which the client identifies itself.
     *
     * @param string $userAgent the name of the client.
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string as argument; received "%s"',
                __METHOD__,
                (is_object($userAgent) ? get_class($userAgent) : gettype($userAgent))
            ));
        }
        
        $this->userAgent = $userAgent;
    }
    
    /**
     * Returns the name by which the client identifies itself.
     *
     * @return string the name of the client.
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set the HTTP referer header.
     *
     * @param string|null $referer the referer url, or null to remove any previously set referer.
     */    
    public function setReferer($referer = null)
    {
        if (!(is_string($referer) || $referer === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string as argument or null literal; received "%s"',
                __METHOD__,
                (is_object($referer) ? get_class($referer) : gettype($referer))
            ));
        }
        
        $this->referer = $referer;
    }
    
    /**
     * Returns the HTTP referer header.
     *
     * @return string the referer url.
     */
    public function getReferer()
    {
        return $this->referer;
    }
    
    /**
     * Returns a query string by creating a key-value pair from each part and 
     * joining the pairs into a single query string.
     *
     * @param array $parts an array contaning the parts of a query string.
     * @return string returns a querystring from the given array.
     */
    private function buildQuery(array $parts)
    {
        // array to hold key-value pairs.
        $result = array();
        // create a query string.
        foreach ($parts as $key => $value) {
            if (is_array($value)) {
                // support duplicate keys in query string.
                foreach ($value as $v) {
                    $result[] = sprintf('%s=%s', urlencode($key), urlencode($v));
                }
            } else {
                // create an encoded key-value pair for each part.
                $result[] = sprintf('%s=%s', urlencode($key), urlencode($value));
            }
        }
        
        // return query string.
        return implode('&', $result);
    }
    
    /**
     * Returns an associative array containing (query) parts from the 
     * given query string.
     * 
     * @param string $query the query string to parse.
     * @return array returns an associative array.
     */
    private function parseQuery($query)
    {
        // array to hold query parts.
        $result = array();
        // create array containing key-value pairs.
        $parts = explode('&', $query);
        foreach ($parts as $part) {
            // separate pair into it's key and value.
            list($key, $value) = explode('=', $part, 2);
            // url decode each value.
            $value = urldecode($value);
            if (isset($result[$key])) {
                // allow the existence of duplicate keys.
                if (!is_array($result[$key])) {
                    $result[$key] = array($result[$key]);
                }
                // add value to existing array.
                $result[$key][] = $value;
            } else {
                // create a new part.
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    /**
     * Returns an URL-encoding string according to RFC 3986
     *
     * @param string $str the string to encode.
     * @return string url encoded string.
     * @link http://php.net/manual/en/function.rawurlencode.php
     */
    private function encodeUrl($str)
    {
        return rawurlencode($str);
    }
}
