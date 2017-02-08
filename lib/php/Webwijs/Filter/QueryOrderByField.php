<?php

namespace Webwijs\Filter;

class QueryOrderByField
{
    public function filter($clauses, $query)
    {
        if (isset($query->query_vars['order_by_field'])) {
            $clauses['orderby'] = $query->query_vars['order_by_field'];
        }
        return $clauses;
    }
}
