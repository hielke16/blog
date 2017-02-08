<?php

namespace Webwijs\View\Helper;

class DropdownPosts
{
    public function dropdownPosts($args = null)
    {
        $defaults = array(
            'post_types' => 'public',
            'name' => 'page_id',
            'selected' => 0,
            'class' => '',
            'field' => 'ID',
            'show_option_none' => __('--Selecteer')
            //'child_of' => 0,
            //'depth' => 0
        );
        $args = array_merge($defaults, (array) $args);
        if ($args['post_types'] == 'public') {
            $args['post_types'] = array_diff(get_post_types(array('public' => true)), array('attachment'));
        }

        $postTypeObjects = array();
        foreach ((array) $args['post_types'] as $postType) {
            $postTypeObjects[] = $postTypeObject = get_post_type_object($postType);
        }
        ob_start();
        ?>
        <select name="<?php echo $args['name'] ?>" class="<?php echo $args['class'] ?>">
            <?php if ($args['show_option_none']): ?>
            <option value=""><?php echo $args['show_option_none'] ?></option>
            <?php endif ?>
            <?php foreach ($postTypeObjects as $postTypeObject): ?>
            <?php echo $this->dropdown($postTypeObject, $args) ?>
            <?php endforeach ?>
        </select>
        <?php
        return ob_get_clean();
    }
    public function dropdown($postTypeObject, $args)
    {
        $output = '';
        $posts = get_posts(array('post_type' => $postTypeObject->name, 'orderby' => 'menu_order title', 'nopaging' => true));
        if (!empty($posts)) {
            if ($postTypeObject->hierarchical) {
                $posts = $this->makeHierarchical($posts);
            }
            $options = $this->dropdownPostsLevel($posts, $args);

            if (!empty($options)) {
                if (count($args['post_types']) > 1) {
                    $output .= '<optgroup label="' . esc_attr($postTypeObject->labels->name) . '">' . $options . '</optgroup>';
                }
                else {
                    $output .= $options;
                }
            }
        }
        return $output;
    }
    public function dropdownPostsLevel($posts, $args, $level = 0)
    {
        $output = '';
        foreach ($posts as $post) {
            $value = $post->{$args['field']};
            $selected = $args['selected'] == $value ? 'selected="selected"' : '';
            $output .= '<option value="' . $value . '" '. $selected . '>' . str_repeat('&nbsp;', $level * 3) . esc_attr($post->post_title) . '</option>';
            if (!empty($post->children)) {
                $output .= $this->dropdownPostsLevel($post->children, $args, $level + 1);
            }
        }
        return $output;
    }
    public function makeHierarchical($pages, $parentId = null) {
	    $pageList = array();
	    foreach ( (array) $pages as $page ) {
		    if ($page->post_parent == $parentId ) {
		        $page->children = $this->makeHierarchical($pages, $page->ID);
		        $pageList[] = $page;
		    }
	    }
	    if (empty($pageList) && is_null($parentId)) {
	        $pageList = $pages;
	    }
	    return $pageList;
    }
}
