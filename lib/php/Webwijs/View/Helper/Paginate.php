<?php

namespace Webwijs\View\Helper;

class Paginate
{
    public $type = 'posts';
    public $defaults;

    public function __construct($options=array())
    {
        $defaults = array(
            'title' => '',
            'nextpage' => '&raquo;',
            'previouspage' => '&laquo;',
            'before' => '<nav>',
            'after' => '</nav>',
            'empty' => true,
            'range' => 3,
            'anchor' => 1,
            'gap' => 3
        );
        $this->options = array_merge($defaults, $options);
    }
    public function paginate($args = null, $type='posts')
    {
        $this->type = $type;
        if ($this->type === 'comments' && !get_option('page_comments'))
            return;

        $r = wp_parse_args($args, $this->options);
        extract($r, EXTR_SKIP);

        if (!isset($page) && !isset($pages)) {
            global $wp_query;

            if ($this->type === 'posts') {
                $page = get_query_var('paged');
                $posts_per_page = intval(get_query_var('posts_per_page'));
                $pages = intval(ceil($wp_query->found_posts / $posts_per_page));
            }
            else {
                $page = get_query_var('cpage');
                $comments_per_page = get_option('comments_per_page');
                $pages = get_comment_pages_count();

            }
            $page = !empty($page) ? intval($page) : 1;
        }


        $prevlink = ($this->type === 'posts')
            ? esc_url(get_pagenum_link($page - 1))
            : get_comments_pagenum_link($page - 1);
        $nextlink = ($this->type === 'posts')
            ? esc_url(get_pagenum_link($page + 1))
            : get_comments_pagenum_link($page + 1);

        $output = stripslashes($before);
        if ($pages > 1) {
            $output .= sprintf('<ol class="wp-paginate%s">', ($this->type === 'posts') ? '' : ' wp-paginate-comments');
            //$output .= sprintf('<li><span class="title">%s</span></li>', stripslashes($title));
            $ellipsis = "<li><span class='gap'>...</span></li>";

            if ($page > 1 && !empty($previouspage)) {
                $output .= sprintf('<li><a href="%s" class="prev">%s</a></li>', $prevlink, stripslashes($previouspage));
            }

            $min_links = $range * 2 + 1;
            $block_min = min($page - $range, $pages - $min_links);
            $block_high = max($page + $range, $min_links);
            $left_gap = (($block_min - $anchor - $gap) > 0) ? true : false;
            $right_gap = (($block_high + $anchor + $gap) < $pages) ? true : false;

            if ($left_gap && !$right_gap) {
                $output .= sprintf('%s%s%s',
                    $this->paginate_loop(1, $anchor),
                    $ellipsis,
                    $this->paginate_loop($block_min, $pages, $page)
                );
            }
            else if ($left_gap && $right_gap) {
                $output .= sprintf('%s%s%s%s%s',
                    $this->paginate_loop(1, $anchor),
                    $ellipsis,
                    $this->paginate_loop($block_min, $block_high, $page),
                    $ellipsis,
                    $this->paginate_loop(($pages - $anchor + 1), $pages)
                );
            }
            else if ($right_gap && !$left_gap) {
                $output .= sprintf('%s%s%s',
                    $this->paginate_loop(1, $block_high, $page),
                    $ellipsis,
                    $this->paginate_loop(($pages - $anchor + 1), $pages)
                );
            }
            else {
                $output .= $this->paginate_loop(1, $pages, $page);
            }

            if ($page < $pages && !empty($nextpage)) {
                $output .= sprintf('<li><a href="%s" class="next">%s</a></li>', $nextlink, stripslashes($nextpage));
            }
            $output .= "</ol>";
        }
        $output .= stripslashes($after);

        if ($pages > 1 || $empty) {
            return $output;
        }
    }
    function paginate_loop($start, $max, $page = 0) {
        $output = "";
        for ($i = $start; $i <= $max; $i++) {
            $p = ($this->type === 'posts') ? esc_url(get_pagenum_link($i)) : get_comments_pagenum_link($i);
            $output .= ($page == intval($i))
                ? "<li><span class=\"page current\">{$i}</span></li>"
                : "<li><a href=\"{$p}\" title=\"{$i}\" class='page'>{$i}</a></li>";
        }
        return $output;
    }
}
