<?php

namespace Webwijs\Net;

use Webwijs\Net\UrlConnection;
use Webwijs\Net\HttpResponse;
use Webwijs\Net\HttpRequest;
use Webwijs\Net\Exception\NetworkException;

class HttpCurlConnection extends UrlConnection
{
    /**
     * A collection of cURL options.
     *
     * @var array
     */
    private $options = array(
        CURLOPT_SSL_VERIFYPEER => true
    );

    /**
     * Returns the response by opening a new connection with the given request.
     *
     * @param HttpRequest $request the request used to create a connection.
     * @return HttpResponse the response that is returned from the connection.
     * @throws NetworkException if the cURL operation returned an error number.
     */
    protected function makeRequest(HttpRequest $request)
    {
        $curl = curl_init();

        $postBody = $request->getPostBody();
        if (!empty($postBody)) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $postBody);
        }

        $requestProperties = $request->getRequestProperties();
        if (is_array($requestProperties)) {
          $curlHeaders = $this->getHeaderFields($requestProperties);
          if (!empty($curlHeaders)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
          }
        }

        if (is_string($request->getReferer())) {
            curl_setopt($curl, CURLOPT_REFERER, $request->getReferer());
        }

        curl_setopt($curl, CURLOPT_URL, $request->getUrl());
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getRequestMethod());
        curl_setopt($curl, CURLOPT_USERAGENT, $request->getUserAgent());
        
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        
        // set a possible connection timeout.
        if (($connectionTimeout = $this->getConnectionTimeOut()) && $connectionTimeout > 0) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectionTimeout);
        }

        // set a collection of additional cURL options.
        if (is_array($this->options) && count($this->options) > 0) {
            foreach ($this->options as $option => $value) {
                curl_setopt($curl, $option, $value);
            }
        }
        
        // execute the curl session and store response.
        $result = curl_exec($curl);
        
        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            $errorCode = curl_errno($curl);

            curl_close($curl);
            throw new NetworkException($errorMessage, $errorCode);
        }        
        
        // use header size to separate the response header and body.
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // response code from curl session.
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // extract response header from result.
        $responseHeader = '';
        if ($content = substr($result, 0, $headerSize)) {
            $responseHeader = $content;
        }
        // extract response body from result.
        $responseBody = '';
        if ($content = substr($result, $headerSize)) {
            $responseBody = $content;
        }
        
        curl_close($curl);
        
        // create a http response object.
        $response = new HttpResponse();
        $response->setResponseHeaders($responseHeader);
        $response->setResponseBody($responseBody);
        $response->setResponseCode($responseCode);
        
        return $response;
    }

    /**
     * Set an option for the cURL transfer.
     *
     * @param int $option the CURLOPT_XXX option to set.
     * @param mixed $value the value to set for the specified cURL option.
     */
    public function setOption($option, $value)
    {
        if (!is_scalar($option)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects $option to be a scalar value; received "%s"',
                __METHOD__,
                (is_object($option) ? get_class($option) : gettype($option))
            ));
        }

        $this->options[$option] = $value;
    }

    /**
     * Set a collection consisting of key-value pairs for the cURL transfer.
     *
     * @param array|Traversable $options collection of CURLOPT_XXX options to set.
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * Returns true if a value exists for the specified cURL option.
     *
     * @param int $option the CURLOPT_XXX option whose presence will be tested.
     * @return bool true if a value exists for the specified option, false otherwise.
     */
    public function hasOption($option)
    {
        return (isset($this->options[$option]));
    }

    /**
     * Returns the value associated with the cURL option.
     *
     * @param int $options the CURLOPT_XXX option whose value to return.
     * @param mixed $default the default value to return if the specified option does not exist.
     * @return mixed the value associated with the specified cURL option.
     */
    public function getOption($option, $default = null)
    {
        return ($this->hasOption($option)) ? $this->options[$option] : $default;
    }

    /**
     * Removes all previously set cURL options. The HttpCurlConnection will have no additional
     * cURL options after this call returns.
     *
     * @return void
     */
    public function clearOptions()
    {
        $this->options = array();
    }

    /**
     * Removes if present the cURL option and it's value.
     *
     * @param int $option the CURLOPT_XXX option whose value to remove.
     * @return bool true if the cURL option exists and it's value has been removed, false otherwise.
     */
    public function removeOption($option)
    {   
        $isRemoved = false;
        if ($isRemoved = $this->hasOption($option)) {
            unset($this->options[$option]);
        }

        return $isRemoved;
    }

    /**
     * Removes one or more cURL options and their values.
     *
     * @param array|Traversable $options a numeric array containing names of cURL options that will be removed.
     * @return bool true if at least one cURL options was removed, false otherwise.
     */
    public function removeOptions($options)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        $isRemoved = false;
        foreach ($options as $option) {
            if ($this->removeOption($option)) {
                $isRemoved = true;
            }
        }

        return $isRemoved;
    }
}
