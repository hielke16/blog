<?php

namespace Webwijs\FacetSearch\Filter;

class Paginate extends AbstractFilter
{
    public function apply()
    {
        $page = (int) $this->getValue();
        if (!empty($page)) {
            return array('paged' => $page);
        }
    }
    public function getNextLink()
    {
        $page = (int) $this->getValue();
        return $this->getPageLink($page + 1);
    }
    public function getPageLink($page)
    {
        $params = $this->getCaller()->getParams();
        $params[$this->name] = $page;
        return get_permalink() . '?' . $this->getCaller()->buildQuery($params);
    }
    public function getNextPage()
    {
        $page = (int) $this->getValue();
        return $page + 1;
    }
}
