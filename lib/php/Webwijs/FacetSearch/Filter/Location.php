<?php

namespace Webwijs\FacetSearch\Filter;

class Location extends AbstractFilter
{
    protected $_geocodingUrl = 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';
    public function apply()
    {
        $city = $this->getValue();
        $radius = $this->getRadius();
        if (!empty($city) && (int) $radius > 0) {
            $location = $this->_getLocationByAddress($city . ', Netherlands');
            if ($location) {
                $location['distance'] = $radius;
                return array('location' => $location);
            }
            else {
                $this->setError('De opgegeven locatie is niet gevonden');
            }
        }
    }
    public function getRadius()
    {
        $radius = $this->getCaller()->getParam($this->filterOptions['radius']);
        if (empty($radius)) {
            $radius = $this->filterOptions['defaultRadius'];
        }
        return $radius;
    }
    protected function _getLocationByAddress($address)
    {
        $content = @file_get_contents(sprintf($this->_geocodingUrl, urlencode($address)));
        if ($content) {
            $result = @json_decode($content);
            if ($result && $result->status == 'OK') {
                return array(
                    'latitude' => $result->results[0]->geometry->location->lat,
                    'longitude' => $result->results[0]->geometry->location->lng
                );
            }
        }
        return false;
    }
}
