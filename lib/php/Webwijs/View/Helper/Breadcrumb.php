<?php

namespace Webwijs\View\Helper;

use Webwijs\Post;

class Breadcrumb
{
    public function breadcrumb($args = null)
    {
        $defaults = array(
            'separator' => ' <span class="sep">&rsaquo;</span> ',
            'show_home' => __('Home', 'webwijs'),
        );
        $args = wp_parse_args($args, $defaults);
        $trail = $this->_getTrailItems($args);
        ob_start();
        ?>
        <nav class="breadcrumb breadcrumbs" itemprop="breadcrumb">
            <div class="breadcrumb-trail">
                <?php echo implode($args['separator'], $trail) ?>
            </div>
        </nav>
        <?php
        return ob_get_clean();
    }
    protected function _getTrailItems($args)
    {
        $trail = array();
        if      (is_front_page()) { $trail = $this->_getTrailItemsFront($args);     }
        elseif  (is_home())       { $trail = $this->_getTrailItemsHome($args);      }
        elseif  (is_singular())   { $trail = $this->_getTrailItemsSingular($args);  }
        elseif  (is_date())       { $trail = $this->_getTrailItemsDate($args);      }
        elseif  (is_archive())    { $trail = $this->_getTrailItemsArchive($args);   }
        elseif  (is_search())     { $trail = $this->_getTrailItemsSearch($args);    }
        elseif  (is_404())        { $trail = $this->_getTrailItems404($args);       }

        if ($args['show_home']) {
            array_unshift($trail, $this->_getTrailItemHtml($args['show_home'], rtrim(home_url(), '/') . '/'));
        }
        return $trail;
    }
    protected function _getTrailItemsFront($args)
    {
        $trail = array();
        return $trail;
    }
    protected function _getTrailItemsHome($args)
    {
        $trail = array();
        if (get_option('show_on_front') == 'page') {
            $id = get_option('page_for_posts');
            if ($id) {
                $page = get_page($id);
                if ($page) {
                    $ancestors = Post::getPostAncestors($page);
                    $trail = $this->_getTrailLinks($ancestors);
                }
            }
        }
        return $trail;
    }
    protected function _getTrailItemsSingular($args)
    {
        $ancestors = array_reverse(Post::getPostAncestors());
        $trail = $this->_getTrailLinks($ancestors);
        $trail[] = $this->_getTrailItemHtml(get_the_title());
        return $trail;
    }
    protected function _getTrailItemsDate($args)
    {
        $trail = array();
        $postPageId = Post::getCustomPostPageId('post');
        if ($postPageId) {
            $postPage = get_post($postPageId);
            $ancestors = array_merge(
                array_reverse(Post::getPostAncestors($postPage)),
                array($postPageId)
            );
        }
        $trail = $this->_getTrailLinks($ancestors);

        if (is_day())       { $title = sprintf(__('Archief voor dag: <span>%s</span>'), get_the_date());        }
        elseif (is_month()) { $title = sprintf(__('Archief voor maand: <span>%s</span>'), get_the_date('F Y')); }
        elseif (is_year())  { $title = sprintf(__('Archief voor jaar: <span>%s</span>'), get_the_date('Y'));    }
        else                { $title = __('Archief'); }

        $trail[] = $this->_getTrailItemHtml($title);
        return $trail;
    }
    protected function _getTrailItemsArchive($args)
    {
        $trail = array();
        $term = get_queried_object();
        $ancestors = array_reverse(Post::getTaxonomyAncestors($term->taxonomy));
        $trail = $this->_getTrailLinks($ancestors);
        $trail[] = $this->_getTrailItemHtml(single_term_title('', false));
        return $trail;
    }
    protected function _getTrailItemsSearch($args)
    {
        $trail = array();
        $trail[] = $this->_getTrailItemHtml(sprintf(__('Zoekresultaten voor &quot;%1$s&quot;', 'webwijs'), esc_attr(get_search_query())));
        return $trail;
    }
    protected function _getTrailItems404($args)
    {
        $trail = array();
        $trail[] = $this->_getTrailItemHtml(__( '404 Niet gevonden', 'webwijs'));
        return $trail;
    }
    protected function _getTrailLinks($ids)
    {
        $trail = array();
        $homeId = false;
        if (get_option('show_on_front') == 'page') {
            $homeId = get_option('page_on_front');
        }
        foreach ((array) $ids as $id) {
            if (!empty($id) && ($id != $homeId)) {
                $trail[] = $this->_getTrailLink($id);
            }
        }
        return $trail;
    }
    protected function _getTrailLink($id)
    {
        return $this->_getTrailItemHtml(get_the_title($id), get_permalink($id));
    }
    protected function _getTrailItemHtml($title, $link = null)
    {
        if (empty($link)) {
            return sprintf('<span class="trail-end" title="%s">%s</span>', esc_attr($title), $title);
        }
        return sprintf('<a href="%s" title="%s">%s</a>', $link, esc_attr($title), $title);
    }
}
