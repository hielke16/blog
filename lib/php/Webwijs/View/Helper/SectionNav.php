<?php

namespace Webwijs\View\Helper;

use Webwijs\Post;
use Webwijs\View;
use Webwijs\Util\Arrays;

class SectionNav
{
     /**
     * Displays a submenu depending on the current page
     *
     * @param array $args Associative array of options
     * @param object $post A Wordpress post object
     * @return string
     */
    public function sectionNav($args = null, $post = null)
    {
        $defaults = array(
            'post_types' => array_diff(get_post_types(array('public' => true)), array('attachment')),
            'taxonomies' => array('category'),
            'min_level' => 0,
            'max_level' => 2,
            'query_args' => null,
            'filter_inactive_level' => 1,
            'template' => 'partials/section-nav/nav.phtml',
        );
        $args = array_merge($defaults, (array) $args);
        is_null($post) && $post = $GLOBALS['post'];

        $tree = $this->getPostsTree($post, $args);
        $this->filterInActiveBranches($tree, $post, $args);
        $this->setTreeCssClassNames($tree, $post);
        $tree = apply_filters('section_nav_tree', $tree);

        if (!empty($tree)) {
            $view = new View();
            if ($view->locateTemplate($args['template'])) {
                return $view->partial($args['template'], array('tree' => $tree));
            }
            return $this->render($tree);
        }
    }
    /*
    * Renders a tree of pages
    *
    * @param array $tree, a tree of Wordpress posts
    * @return string, the output
    */
    protected function render($tree, $level = 0)
    {
        ob_start();
        ?>
        <?php if ($level == 0): ?>
        <nav class="section-nav-menu">
        <?php endif ?>
            <ul>
                <?php foreach ($tree as $post): ?>
                <li class="<?php echo implode(' ', $post->class_names) ?>">

                    <?php if (isset($post->term_id)): ?>
                    <a href="<?php echo get_term_link($post) ?>"><span><?php echo apply_filters('the_title', $post->name)?></span></a>
                    <?php else: ?>
                    <a href="<?php echo get_permalink($post->ID) ?>"><span><?php echo apply_filters('the_title', $post->post_title)?></span></a>
                    <?php endif ?>

                    <?php if (!empty($post->children)): ?>
                    <?php echo $this->render($post->children, $level + 1); ?>
                    <?php endif ?>
                </li>
                <?php endforeach ?>
            </ul>
        <?php if ($level == 0): ?>
        </nav>
        <?php endif ?>

        <?php
        return ob_get_clean();
    }
    /**
    * Gets a tree of pages for the section nav
    *
    * @param object $post The Wordpress post on which the tre should be got
    * @param array $args Associative array of options
    * @return array, array of post objects
    */
    protected function getPostsTree($post, $args)
    {
        $tree = array();
        $topLevelPost = $this->getTopLevelPost($post, $args);
        if (!empty($topLevelPost)) {
            $this->setPostChildren($topLevelPost, $args, max(0, $args['min_level'] - 1) + 1);
            if ($args['min_level'] == 0) {
                $tree = array($topLevelPost);
            }
            elseif (!empty($topLevelPost->children)) {
                $tree = $topLevelPost->children;
            }
        }
        return $tree;
    }


    /**
    * Recursively sets the children on a post
    *
    * @param object $post The Wordpress post on which the children should be set
    * @param array $args Associative array of options
    * @return void
    */
    protected function setPostChildren($post, $args, $currentLevel = 1)
    {
        if ($args['max_level'] >= $currentLevel) {
            $post->children = $this->getChildren($post, $args);
            if ($args['max_level'] > $currentLevel) {
                foreach ($post->children as &$child) {
                    $this->setPostChildren($child, $args, $currentLevel + 1);
                }
            }
        }
    }
    /**
    * Recursively hide branches that are not children or parents of the current post
    *
    * @param array $tree, a tree of post object
    * @param object $currentPost, the current post
    * return void
    */
    protected function filterInActiveBranches($tree, $currentPost, $args, $level = 0)
    {
        $ancestors = Post::getPostAncestors($currentPost);
        foreach ($tree as $key => &$post) {
            if (($args['filter_inactive_level'] <= $level) && ($post->ID != $currentPost->ID) && !in_array($post->ID, $ancestors)) {
                unset($post->children);
            }
            elseif (!empty($post->children)) {
                $this->filterInActiveBranches($post->children, $currentPost, $args, $level + 1);
            }
        }
    }
    /**
    * Recursively sets css class names on a tree
    *
    * @param array $tree, a tree of post object
    * @param object $currentPost, the current post
    * return void
    */
    protected function setTreeCssClassNames($tree, $currentPost)
    {
        // convert associative array to a numeric one.
        if (Arrays::isAssoc($tree)) {
            $tree = array_values($tree);
        }
        
        $ancestors = Post::getPostAncestors($currentPost);
        foreach ($tree as $index => &$post) {
            $classNames = array();
            $classNames[] = (($index % 2) == 0) ? 'odd-item' : 'even-item';
            
            if (isset($post->term_id)) {
                $classNames[] = $post->taxonomy . '-item';
                $classNames[] = $post->taxonomy . '-item-' . $post->term_id;
                if (is_tax() || is_tag() || is_category()) {
                    $currentTerm = get_queried_object();
                    if ($currentTerm->term_id == $post->term_id) {
                        $classNames[] = 'current-post-item';
                    }
                }
            }
            else {
                $classNames[] = $post->post_type . '-item';
                $classNames[] = $post->post_type . '-item-' . $post->ID;
                if ($post->ID == $currentPost->ID) {
                    $classNames[] = 'current-post-item';
                } else if (in_array($post->ID, $ancestors)) {
                    $classNames[] = 'current-post-ancestor';
                }
            }
            
            $post->class_names = $classNames;
            if (!empty($post->children)) {
                $this->setTreeCssClassNames($post->children, $currentPost);
            }
        }
    }

    /**
    * Gets the ancestor of a post, dependent of a min_level set in $args
    *
    * @param object $post The wordpress post for which the ancestor should be found
    * @param array $args Associative array of options
    * @return mixed, object when an ancestor found, otherwise false
    */
    protected function getTopLevelPost($post, $args)
    {
        $ancestors = Post::getPostAncestors($post);
        if (empty($ancestors)) {
            return $this->getPost(array('page_id' => $post->ID), $args);
        }
        else {
            if (count($ancestors) >= max($args['min_level'], 1)) {
                return $this->getPost(array('page_id' => $ancestors[count($ancestors) - max(1, $args['min_level'])]), $args);
            }
        }
        return false;
    }

    /**
    * Gets the children of a post
    *
    * @param object $post The wordpress post for which the children should be got
    * @param array $args Associative array of options
    * @return array, array of post objects
    */
    protected function getChildren($post, $args)
    {
        $children = $this->getPosts(array('post_parent' => $post->ID), $args);
        foreach ($args['post_types'] as $post_type) {
            if (Post::getCustomPostPageId($post_type) == $post->ID) {
                $children = $this->mergeChildren($children, $this->getPosts(array('post_type' => $post_type, 'post_parent' => 0), $args));
            }
        }
        foreach ($args['taxonomies'] as $taxonomy) {
            if (Post::getTaxonomyPageId($taxonomy) == $post->ID) {
                $children = array_merge($children, get_terms($taxonomy));
            }
        }
        return $children;
    }
    /**
    * Merges two arrays of posts, sortt them according to the menu_order of each post
    *
    * @param array $children First array of children
    * @param array $merge Second array of children
    * @return array, array of post objects
    */
    protected function mergeChildren($children, $merge)
    {
        if (!empty($merge)) {
            $children = array_merge($children, $merge);
            usort($children, array($this, 'sortCallBack'));
        }
        return $children;
    }
    /**
    * Gets a wordpress post using WP_Query
    *
    * @param array $queryArgs Arguments to be used by WP_Query
    * @param array $args Associative array of options
    * @return mixed, object when a post is found, false otherwise
    */
    protected function getPost($queryArgs, $args)
    {
        $posts = $this->getPosts($queryArgs, $args);
        if (count($posts)) {
            return reset($posts);
        }
        return false;
    }
    /**
    * Gets wordpress posts using WP_Query
    *
    * @param array $queryArgs Arguments to be used by WP_Query
    * @param array $args Associative array of options
    * @return array, array of post objects
    */
    protected function getPosts($queryArgs, $args)
    {
        global $wpdb;
        $defaultQueryArgs = array(
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => $args['post_types'],
            'post_status' => 'publish',
            'nopaging' => true,
        );
        $queryArgs = array_merge($defaultQueryArgs, $queryArgs, (array) $args['query_args']);
        $wpQuery = new \WP_Query();

        $result = $wpQuery->query($queryArgs);
        return $result;
    }
    /**
    * Callback used by usort to sort an array of posts by menu_order
    *
    * @param object $a, Wordpress post
    * @param object $b, Wordpress post
    * return int
    */
    public function sortCallBack($a, $b)
    {
        if ($a->menu_order < $b->menu_order) {
            return 0;
        }
        elseif ($a->menu_order > $b->menu_order) {
            return 1;
        }
        return (strtotime($a->post_date) > strtotime($b->post_date)) ? 0 : 1;
    }
}
