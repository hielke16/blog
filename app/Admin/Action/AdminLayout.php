<?php

namespace Theme\Admin\Action;

class AdminLayout
{
    public static function menuItems()
    {
        global $menu, $submenu;
        if (get_option('theme_hide_posts')) {
            unset($menu[5]);
        }
        else {
            if (get_option('theme_hide_categories')) {
                foreach ($submenu['edit.php'] as $key => $item) {
                    if (strpos($item[2], 'taxonomy=category')) {
                        unset($submenu['edit.php'][$key]);
                    }
                }
            }
            if (get_option('theme_hide_tags')) {
                foreach ($submenu['edit.php'] as $key => $item) {
                    if (strpos($item[2], 'taxonomy=post_tag')) {
                        unset($submenu['edit.php'][$key]);
                    }
                }
            }
        }
        if (get_option('theme_hide_links')) {
            unset($menu[15]);
        }
        if (get_option('theme_hide_comments')) {
            unset($menu[25]);
        }
    }
    public static function menuLayout()
    {
        ?>
        <style type="text/css">
            <?php if (get_option('theme_hide_posts')): ?>
            #menu-posts, #wp-admin-bar-new-post { display: none }
            <?php endif ?>
            <?php if (get_option('theme_hide_links')): ?>
            #menu-links, #wp-admin-bar-new-link { display: none }
            <?php endif ?>
            <?php if (get_option('theme_hide_comments')): ?>
            #menu-comments { display: none }
            <?php endif ?>
        </style>
        <?php
    }
    public static function removeWidgets()
    {
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard', 'side');
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');

        if (get_option('theme_hide_comments')) {
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        }
    }
    public static function removeMetaBoxes()
    {
        global $wp_meta_boxes;
        remove_meta_box('postcustom', 'post', 'normal');
        remove_meta_box('postcustom', 'page', 'normal');
        if (get_option('theme_hide_comments')) {
            remove_meta_box('trackbacksdiv', 'post', 'normal');
            remove_meta_box('commentstatusdiv', 'post', 'normal');
            remove_meta_box('commentsdiv', 'post', 'normal');
            remove_meta_box('trackbacksdiv', 'page', 'normal');
            remove_meta_box('commentstatusdiv', 'page', 'normal');
            remove_meta_box('commentsdiv', 'page', 'normal');
        }
        if (get_option('theme_hide_categories')) {
            remove_meta_box('categorydiv', 'post', 'side');
        }

        if (get_option('theme_hide_tags')) {
            remove_meta_box('tagsdiv-post_tag', 'post', 'side');
        }
        if (get_option('theme_hide_authors')) {
            remove_meta_box('authordiv', 'post', 'normal');
            remove_meta_box('authordiv', 'page', 'normal');
        }
        foreach (array_keys($wp_meta_boxes) as $postType) {
            if (isset($wp_meta_boxes[$postType]['normal']['core']['revisionsdiv'])) {
                $wp_meta_boxes[$postType]['advanced']['low']['revisionsdiv'] = $wp_meta_boxes[$postType]['normal']['core']['revisionsdiv'];
                unset($wp_meta_boxes[$postType]['normal']['core']['revisionsdiv']);
            }
        }
    }
    public static function closedMetaBoxes($closed)
    {
        if (empty($closed)) {
            $closed = array();
        }
        $closed = array_merge($closed, array('revisionsdiv'));
        return $closed;
    }
    public static function hiddenMetaBoxes($hidden)
    {
        if (false === $hidden) {
            $hidden = array('revisionsdiv', 'slugdiv');
        }
        return $hidden;
    }
    public static function listTableColumns($columns, $post_type = 'post')
    {
        if (get_option('theme_hide_tags'))          { unset($columns['tags']); }
        if (get_option('theme_hide_categories'))    { unset($columns['categories']); }
        if (get_option('theme_hide_authors'))       { unset($columns['author']); }
        if (get_option('theme_hide_comments'))      { unset($columns['comments']); }
        return $columns;
    }
}
