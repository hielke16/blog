<?php

namespace Webwijs\FacetSearch\Filter;

class Taxonomy extends AbstractFilter implements MultiFilterInterface
{
    public function apply()
    {
        if (!empty($this->value)) {
            $args = array();
            $args['tax_query']['relation'] = 'AND';
            $args['tax_query'][$this->filterOptions['taxonomy']] = array(
                'taxonomy' => $this->filterOptions['taxonomy'],
                'field' => 'slug',
                'terms' => (array) $this->value,
                'operator' => 'IN'
            );
            return $args;
        }
    }
    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        $options = array();
        $terms = $this->_getTerms();
        foreach ($terms as $term) {
            $options[$term->slug] = array('id' => $term->term_id, 'label' => $term->name, 'count' => $term->postcount);
        }
        return $options;
    }
    protected function _getTerms()
    {
        $wpdb = $GLOBALS['wpdb'];
        $postsQuery = new \WP_Query();
        $args = array();
        $caller = $this->getCaller();
        foreach ($caller->getArgs() as $filterName => $filterArgs) {
            if ($filterName != $this->name) {
                $args = $caller->mergeArgs($args, $filterArgs);
            }
        }
        $args['nopaging'] = true;
        $args['no_found_rows'] = true;
        $args['fields'] = 'ids';
        $args['orderby'] = 'none';
        $args['no_location_order'] = true;
        $postsQuery->query($args);
        $sql = <<<SQL
SELECT t.*, tt.*, count(tr.term_taxonomy_id) as postcount
FROM {$wpdb->terms}  AS t
INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id in ({$postsQuery->request})
WHERE tt.taxonomy = "{$this->filterOptions['taxonomy']}"
GROUP BY t.term_id
SQL;
        return $wpdb->get_results($sql);
    }
}
