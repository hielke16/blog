<?php

namespace Webwijs\Net;

use Webwijs\Util\Arrays;

/**
 * The HttpResponse class contains information that a RESTful web service
 * might return after making a request to the service.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class HttpResponse
{
    /**
     * HTTP status code 200: OK.
     *
     * @var int
     */
    const HTTP_OK = 200;

    /**
     * HTTP status code 201: Created.
     *
     * @var int
     */
    const HTTP_CREATED = 201;

    /**
     * HTTP status code 202: Accepted.
     *
     * @var int
     */
    const HTTP_ACCEPTED = 202;

    /**
     * HTTP status code 203: Non-Authoritative Information.
     *
     * @var int
     */
    const HTTP_NOT_AUTHORITATIVE = 203;

    /**
     * HTTP status code 204: No Content.
     *
     * @var int
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * HTTP status code 205: Reset Content.
     */
    const HTTP_RESET = 205;

    /**
     * HTTP status code 206: Partial Content.
     *
     * @var int
     */
    const HTTP_PARTIAL = 206;

    /**
     * HTTP status code 300: Multiple Choices.
     *
     * @var int
     */
    const HTTP_MULT_CHOICE = 300;

    /**
     * HTTP status code 301: Moved Permanently.
     *
     * @var int
     */
    const HTTP_MOVED_PERM = 301;

    /**
     * HTTP status code 302: Temporary Redirect.
     *
     * @var int
     */
    const HTTP_MOVED_TEMP = 302;

    /**
     * HTTP status code 303: See Other.
     *
     * @var int
     */
    const HTTP_SEE_OTHER = 303;

    /**
     * HTTP status code 304: Not Modified.
     *
     * @var int
     */
    const HTTP_NOT_MODIFIED = 304;

    /**
     * HTTP status code 305: Use Proxy.
     *
     * @var int
     */
    const HTTP_USE_PROXY = 305;
    
    /**
     * HTTP status code 400: Bad Request.
     *
     * @var int
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * HTTP status code 401: Unauthorized.
     *
     * @var int
     */
    const HTTP_UNAUTHORIZED = 401;

    /**
     * HTTP status code 402: Payment Required.
     *
     * @var int
     */
    const HTTP_PAYMENT_REQUIRED = 402;

    /**
     * HTTP status code 403: Forbidden.
     *
     * @var int
     */
    const HTTP_FORBIDDEN = 403;

    /**
     * HTTP status code 404: Not Found.
     *
     * @var int
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * HTTP status code 405: Method Not Allowed.
     *
     * @var int
     */
    const HTTP_BAD_METHOD = 405;

    /**
     * HTTP status code 406: Not Acceptable.
     *
     * @var int
     */
    const HTTP_NOT_ACCEPTABLE = 406;

    /**
     * HTTP status code 407: Proxy Authentication Required.
     *
     * @var int
     */
    const HTTP_PROXY_AUTH = 407;

    /**
     * HTTP status code 408: Request Time-Out.
     *
     * @var int
     */
    const HTTP_CLIENT_TIMEOUT = 408;

    /**
     * HTTP status code 409: Conflict.
     *
     * @var int
     */
    const HTTP_CONFLICT = 409;

    /**
     * HTTP status code 410: Gone.
     *
     * @var int
     */
    const HTTP_GONE = 410;

    /**
     * HTTP status code 411: Length Required.
     *
     * @var int
     */
    const HTTP_LENGTH_REQUIRED = 411;

    /**
     * HTTP status code 412: Precondition Failed.
     *
     * @var int
     */
    const HTTP_PRECON_FAILED = 412;

    /**
     * HTTP status code 413: Request Entity Too Large.
     *
     * @var int
     */
    const HTTP_ENTITY_TOO_LARGE = 413;

    /**
     * HTTP status code 414: Request-URI Too Large.
     *
     * @var int
     */
    const HTTP_REQ_TOO_LONG = 414;

    /**
     * HTTP status code 415: Unsupported Media Type.
     *
     * @var int
     */
    const HTTP_UNSUPPORTED_TYPE = 415;

    /**
     * HTTP status code 500: Internal Server Error.
     *
     * @var int
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * HTTP status code 501: Not Implemented.
     *
     * @var int
     */
    const HTTP_NOT_IMPLEMENTED = 501;

    /**
     * HTTP status code 502: Bad Gateway.
     *
     * @var int
     */
    const HTTP_BAD_GATEWAY = 502;

    /**
     * HTTP status code 503: Service Unavailable.
     *
     * @var int
     */
    const HTTP_UNAVAILABLE = 503;

    /**
     * HTTP status code 504: Gateway Timeout.
     *
     * @var int
     */
    const HTTP_GATEWAY_TIMEOUT = 504;

    /**
     * HTTP status code 505: HTTP Version Not Supported.
     *
     * @var int
     */
    const HTTP_VERSION = 505;

    /**
     * an HTTP response code.
     *
     * @var int
     */
    protected $responseCode;

    /**
     * the response headers.
     *
     * @var array
     */
    protected $responseHeaders = array();

    /**
     * the response body.
     *
     * @var mixed
     */
    protected $responseBody;
    
    /**
     * Set the HTTP response code for this request.
     *
     * @param int $responseCode the response code.
     */
    public function setResponseCode($responseCode)
    {
        if (!is_numeric($responseCode)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($responseCode) ? get_class($responseCode) : gettype($responseCode))
            ));
        }
        
        $this->responseCode = (int) $responseCode;
    }
    
    /**
     * Returns the HTTP response code for this request.
     *
     * @return int the response code.
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    /**
     * Set the response body for this request.
     *
     * @param string $responseBody the response body.
     */
    public function setResponseBody($responseBody)
    {
        if (!is_string($responseBody)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($responseBody) ? get_class($responseBody) : gettype($responseBody))
            ));
        }
        
        $this->responseBody = $responseBody;
    }
    
    /**
     * Returns the response body for this request.
     *
     * @return string the response body.
     */
    public function getResponseBody()
    {
        return $this->responseBody;   
    }
    
    /**
     * Set the response headers for this request.
     *
     * @param array|\Traversable $headers headers that will be added to the response headers.
     */
    public function setResponseHeaders($headers)
    {    
        // parse the given headers. 
        $headers = $this->parseResponseHeaders($headers);
        
        // lowercase all keys in the array.
        $headers = Arrays::normalize($headers);
        if (is_array($this->responseHeaders)) {
            $headers = array_merge_recursive($this->responseHeaders, $headers);
        }
        
        $this->responseHeaders = $headers;
    }
    
    /**
     * Returns the response headers for this request.
     *
     * @return array the response headers.
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
    
    private function parseResponseHeaders($headers)
    {
        // array to hold header parts.
        $result = array();
        // create an array containing headers.
        if (is_string($headers)) {
            // store each line as a header.
            $headers = explode("\r\n", $headers);
        } else if($headers instanceof \Traversable) {
            // copy the iterator into an array.
            if ($headers instanceof \Traversable) {
                if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
                    $headers = iterator_to_array($headers);
                } else {
                    $headers = Arrays::iteratorToArray($headers);
                }
            }
        }

        // parse each header line.
        $lines = (array) $headers;
        foreach ($lines as $line) {
            if ($line && strpos($line, ':') !== false) {
                // separate line into key and value.
                list($key, $value) = explode(': ', $line, 2);
                if (isset($result[$key])) {
                    // allow the existence of duplicate keys.
                    if (!is_array($result[$key])) {
                        $result[$key] = array($result[$key]);
                    }
                    // add value to existing array.
                    $result[$key][] = $value;
                } else {
                    // add value.
                    $result[$key] = $value;
                }
            }
        } 
        return $result;
    }    
}
