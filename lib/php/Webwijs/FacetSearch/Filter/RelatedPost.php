<?php

namespace Webwijs\FacetSearch\Filter;

class RelatedPost extends AbstractFilter implements MultiFilterInterface
{
    public function apply()
    {
        if (!empty($this->value)) {
            $wpdb = $GLOBALS['wpdb'];
            $escapedNames = array_map('esc_sql', (array) $this->value);
            $inNames = '("' . implode('", "', $escapedNames) . '")';
            $relatedPostIdsSql = <<<SQL
            SELECT ID
            FROM {$wpdb->posts}
            WHERE post_name IN {$inNames}
SQL;
            $relatedPostIds = $wpdb->get_col($relatedPostIdsSql);
            if (!empty($relatedPostIds)) {

                $inIds = '(' . implode(', ', array_map('intval', $relatedPostIds)) . ')';

                $subQuery = <<<SQL
                SELECT
                    CASE WHEN r.post_a_id IN {$inIds} THEN r.post_b_id ELSE r.post_a_id END AS related_id
                FROM {$wpdb->prefix}related_posts AS r
                WHERE (r.post_a_id IN {$inIds} OR r.post_b_id IN {$inIds}) AND relation_key = %s
                ORDER BY sort_order ASC
SQL;

                $subQuery = $wpdb->prepare($subQuery, $this->filterOptions['key']);
                $args = array();
                $args['custom_where'][] = $wpdb->posts . '.ID IN (' . $subQuery . ')';
                return $args;
            }
        }
    }
    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        $options = array();
        $posts = $this->_getPosts();
        foreach ($posts as $post) {
            $options[$post->post_name] = array('id' => $post->ID, 'label' => $post->post_title, 'count' => $post->postcount);
        }
        return $options;
    }
    protected function _getPosts()
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
        SELECT p.ID, p.post_name, p.post_title, count(rp.post_a_id) AS postcount
        FROM {$wpdb->posts} AS p
        LEFT JOIN {$wpdb->prefix}related_posts AS rp
            ON (rp.post_a_id = p.ID OR rp.post_b_id = p.ID)
            AND (rp.relation_key = %s)
            AND (rp.post_a_id IN ({$postsQuery->request})
                OR rp.post_b_id IN ({$postsQuery->request})
            )
        WHERE p.post_type = %s AND p.post_status = 'publish'
        GROUP BY p.ID
        ORDER BY p.menu_order ASC
SQL;
        $sql = $wpdb->prepare($sql, $this->filterOptions['key'], $this->filterOptions['post_type']);
        return $wpdb->get_results($sql);
    }
}
