<?php

namespace Webwijs\FacetSearch\Filter;

class Keyword extends AbstractFilter
{
    public function apply()
    {
        if (!empty($this->value)) {
            return array('s' => $this->value);
        }
    }
}
