<?php

namespace Webwijs\FacetSearch;

use Webwijs\Loader\ClassLoader;

abstract class AbstractFacetSearch
{
    static protected $_instance;
    protected $_filters = array();
    protected $_query;
    public $_args;
    protected $_params;
    public $defaults;

    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
    }

    public function apply($params, $page = 1)
    {
        if (is_string($params)) {
            $str = $params;
            parse_str($str, $params);
            // $params = $this->parseQuery($params);
        }
        
        $this->_params = $params;
        $this->defaults['paged'] = $page;
        $this->_args['defaults'] = $this->defaults;
        if (isset($params['paged'])) {
            $this->_args['defaults'] = $params['paged'];
        }
        foreach ($this->getFilters() as $name => $filter) {
            $value = isset($params[$name]) ? $params[$name] : null;
            $this->_args[$name] = $filter->setValue($value)->apply();
        }
        $args = array();
        foreach ($this->_args as $filterName => $filterArgs) {
            $args = $this->mergeArgs($args, $filterArgs);
        }
        $this->_query = new \WP_Query();
        $this->_query->query($args);
    }
    public function addFilter($type, $name, $options = null)
    {
        $class = ClassLoader::loadStatic('facetsearchfilter', ucfirst($type));
        if (!empty($class)) {
            $this->_filters[$name] = new $class($name, $options);
        }
        else {
            trigger_error('Filter not found ' . $type);
        }
    }
    public function getParam($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }
    public function getParams()
    {
        return $this->_params;
    }

    public function getArgs()
    {
        return $this->_args;
    }
    public function getFilters()
    {
        return $this->_filters;
    }
    public function getFilter($name)
    {
        return $this->_filters[$name];
    }
    public function setDefault($name, $value)
    {
        $this->defaults[$name] = $value;
        return $this;
    }
    public function queryPosts()
    {
        unset($GLOBALS['wp_query']);
        $GLOBALS['wp_query'] = clone $this->_query;
    }
    public function mergeArgs(&$a, &$b)
    {
        if (!is_array($a) && !is_array($b)) { return array(); }
        if (!is_array($a)) { return $b; }
        if (!is_array($b)) { return $a; }

        $merged = $a;
        foreach ($b as $key => &$value) {
            if (is_int($key)) {
                $merged[] = $value;
            }
            elseif (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeArgs($merged[$key], $value);
            }
            else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
    public function parseQuery($str)
    {
        $result = array();
        $pairs = explode('&', $str);

        foreach ($pairs as $pair) {
            if (!empty($pair)) {
                @list($name, $value) = explode('=', $pair, 2);
                if (!empty($value)) {
                    if (isset($result[$name])) {
                        if(is_array($result[$name])) {
                            $result[$name][] = $value;
                        }
                        else {
                            $result[$name] = array($result[$name], $value);
                        }
                    }
                    else {
                        $result[$name] = $value;
                    }
                }
            }
        }
        return $result;
    }
    public function buildQuery($params, $escape = true)
    {
        $pairs = array();
        foreach ((array) $params as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $valueItem) {
                    $pairs[] = $name . '=' . $valueItem;
                }
            }
            elseif (!empty($value)) {
                $pairs[] = $name . '=' . (string) $value;
            }
        }
        $seperator = $escape ? '&amp;' : '&';
        return implode($seperator, $pairs);
    }
    public function getWpQuery()
    {
        return $this->_query;
    }
}
