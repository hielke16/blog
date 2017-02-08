<?php

namespace Webwijs\Http\Localization;

use Webwijs\Http\Localization\Detector\DetectorInterface;
use Webwijs\Http\Localization\Detector\AcceptLanguage;
use Webwijs\Http\RequestInterface;

/**
 * The Localization class provides the capability to determine which browser locales are available by examining 
 * the headers that were sent with the HTTP request.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.0.0
 */
class Localization
{
    /**
     * The language detector.
     *
     * @var DetectorInterface
     */
    private $detector = null;

    /**
     * Construct a new Localization.
     */
    public function __construct()
    {
        $this->setDetector(new AcceptLanguage());
    }

    /**
     * Add the specified language detector.
     *
     * @param DetectorInterface $detector the language detector to add.
     */
    public function addDetector(DetectorInterface $detector)
    {
        if ($this->detector === null) {
            $this->detector = $detector;
            return;
        }
        
        $this->detector->setNextDetector($detector);
    }
    
    /**
     * Set the specified language detector. All previously set detectors are removed.
     *
     * @param DetectorInterface $detector the language detector to set.
     */
    public function setDetector(DetectorInterface $detector)
    {        
        $this->detector = $detector;
    }
    
    /**
     * Returns the underlying language detector.
     *
     * @return DetectorInterface|null the language detector, or null if no detectors are available.
     */
    public function getDetector()
    {
        return $this->detector;
    }
    
    /**
     * Remove the underlying language detector.
     *
     * @return void
     */
    public function clearDetector()
    {
        return $this->detector = null;
    }
    
    /**
     * Returns true if a language detector is set.
     *
     * @return bool true if a langauge detector is set, otherwise false.
     */
    public function hasDetector()
    {
        return ($this->detector !== null);
    }
    
    /**
     * Returns a collection of {@LocaleInterface} instances for the specified HTTP request.
     *
     * @param RequestInterface $request the HTTP request.
     * @return array a collection of {@link LocaleInterface} instances.
     */
    public function detect(RequestInterface $request)
    {
        $locales = array();
        if ($this->hasDetector()) {
            $locales = $this->getDetector()->detect($request);
        }
        
        return $locales;
    }
}
