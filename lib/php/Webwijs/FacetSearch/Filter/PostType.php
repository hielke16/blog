<?php

namespace Webwijs\FacetSearch\Filter;

class PostType extends AbstractFilter implements MultiFilterInterface
{
    public function apply()
    {
        $args = array();
        $types = (array) $this->getValue();
        if (!empty($types)) {
            foreach ($types as $type) {
                if (isset($this->filterOptions['postTypes'][$type])) {
                    $args['post_type'][] = $this->filterOptions['postTypes'][$type]['type'];
                }
            }
        }
        else {
            foreach ($this->filterOptions['postTypes'] as $type) {
                $args['post_type'][] = $type['type'];
            }
        }
        return $args;
    }
    
    public function getOptions()
    {
        $options = array();
        foreach ($this->filterOptions['postTypes'] as $name => $properties) {
            $options[$name] = array('label' => $properties['label'], 'count' => 1);
        }
        return $options;
    }
}
