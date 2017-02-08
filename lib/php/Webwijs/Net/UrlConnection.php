<?php

namespace Webwijs\Net;

use Webwijs\Net\HttpRequest;

abstract class UrlConnection
{
    /**
     * the connection timeout in seconds.
     *
     * @var int
     */
    protected $connectionTimeout = 0;

    /**
     * Returns an array where each value represents a http header.
     *
     * @param array|Traversable an array or collection containing key-value pairs.
     * @return array returns an array containing zero or more http headers.
     */
    protected function getHeaderFields($headers)
    {
        if(!is_array($headers) || $headers instanceof \Traversable) {
            throw new \InvalidArgumentException(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($headers) ? get_class($headers) : gettype($headers))
            );
        }
        
        $httpHeader = array();
        foreach ($headers as $name => $value) {
            $httpHeader[] = sprintf('%s: %s', $name, $value);
        }
        
        return $httpHeader;
    }
    
    /**
     * Returns the response by opening a new connection with the given request.
     *
     * @param HttpRequest $request the request used to create a connection.
     * @return HttpResponse the response that is returned from the connection.
     */
    protected abstract function makeRequest(HttpRequest $request);
    
    /**
     * Returns the response from a Uniform Resource Identifier (URI).
     *
     * @param HttpRequest $request the request used to create a connection.
     * @return HttpResponse the response from a Uniform Resource Identifier (URI). 
     */
    public function getResponse(HttpRequest $request)
    {
        return $this->makeRequest($request);
    }
    
    /**
     * Set the maximum amount of time in seconds that this connection is allowed to keep  to the server
     *
     */
    public function setConnectonTimeOut($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new \InvalidArgumentException(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($timeout) ? get_class($timeout) : gettype($timeout))
            );
        }
        
        $this->connectionTimeout = (int) $timeout;
    }
    
    /**
     * Returns the timeout in seconds before the connection is closed. When this value is set 
     * to 0 it implies that the timeout is disabled (i.e., timeout of infinity).
     *
     * @return int the timeout in seconds.
     */
    public function getConnectionTimeOut()
    {
        return $this->connectionTimeout;
    }

}
