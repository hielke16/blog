<?php

namespace Webwijs\Http\Localization\Detector;

use Webwijs\Http\RequestInterface;

/**
 * A detector operates on a HTTP request and used the request header to determine whether it can handle the request.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
interface DetectorInterface
{
    /**
     * Returns a collection of {@LocaleInterface} instances for the specified HTTP request.
     *
     * @param RequestInterface $request the HTTP request.
     * @return array a collection of {@link LocaleInterface} instances.
     */
    public function detect(RequestInterface $request);
    
    /**
     * Set the next detector.
     *
     * @param DetectorInterface $detector the next detector.
     */
    public function setNextDetector(DetectorInterface $detector);
}
