<?php

namespace Webwijs\Http\Localization\Detector;

/**
 * The AbstractDetector class provides a skeletal implementation of the DetectorInterface interface.
 *
 * @author Chris Harris 
 * @version 1.1.0
 * @since 1.0.0
 */
abstract class AbstractDetector implements DetectorInterface
{
    /**
     * The next detector.
     *
     * @var DetectorInterface
     */
    protected $detector = null;
    
    /**
     * {@inheritDoc}
     */
    public function setNextDetector(DetectorInterface $next)
    {
        if ($this->detector === null) {
            $this->detector = $detector;
            return;
        }
        
        $this->detector->setNextDetector($detector);
    }
}
