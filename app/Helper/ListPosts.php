<?php 
namespace Theme\Helper;

class ListPosts
{
	public function listPosts($args = array())
	{
		$defaults = array(
		    'posts_per_page'   => -1,
		    'offset'           => 0,
		    'category'         => '',
		    'category_name'    => '',
		    'orderby'          => 'date',
		    'order'            => 'DESC',
		    'include'          => '',
		    'exclude'          => '',
		    'meta_key'         => '',
		    'meta_value'       => '',
		    'post_type'        => 'post',
		    'post_mime_type'   => '',
		    'post_parent'      => '',
		    'author'       	   => '',
		    'author_name'      => '',
		    'post_status'      => 'publish',
		    'suppress_filters' => true,
		    'partial'          => 'list'

		);
		$args = array_merge($defaults,$args);
		$posts_array = get_posts( $args );
		$partial = "partials//". $args['post_type'] ."/" .$args['partial'] .".phtml";
		return $this->view->partial($partial,array('posts' => $posts_array));
	}
}