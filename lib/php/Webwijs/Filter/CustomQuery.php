<?php

namespace Webwijs\Filter;

class CustomQuery
{
    public static function filter($clauses, $query)
    {
        $qv = $query->query_vars;
        if (isset($qv['custom_orderby'])) {
            $clauses['orderby'] = $qv['custom_orderby'];
        }
        if (isset($qv['custom_where'])) {
            $customWhere = $qv['custom_where'];

            if (is_array($customWhere)) {
                $customWhere = '(' . implode(') AND (', $customWhere) . ')';
            }
            $clauses['where'] .= ' AND (' . $customWhere . ')';
        }
        if (isset($qv['custom_select'])) {
            $clauses['select'] .= ', ' . $qv['custom_select'];
        }
        return $clauses;
    }
}
