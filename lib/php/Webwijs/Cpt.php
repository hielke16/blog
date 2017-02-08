<?php

namespace Webwijs;

use Webwijs\Post;

class Cpt
{
    public $type;
    public $slug;
    public $labels;
    public $settings;
    protected static $instance;

    function __construct($options = null)
    {
        foreach ((array) $options as $key => $value) {
            if (isset($this->$key) && is_array($this->$key)) {
                $this->$key = array_merge($this->$key, (array) $value);
            }
            else {
                $this->$key = $value;
            }
        }

        $this->init();
        $settings = $this->settings;
        $settings['labels'] = $this->labels;
        register_post_type($this->type, $settings);

        if (empty($this->slug)) {
            $this->slug = $this->type;
        }

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, '_init') === 0) {
                $this->$method();
            }
        }
    }
    public function init()
    {

    }
    protected function _initPermalink() {
        if (!get_option('theme_advanced_flat_url')) {
            if (!empty($this->settings['public']) && !empty($this->settings['rewrite'])) {
                add_rewrite_rule($this->slug . '/(.*)$', 'index.php?' . $this->type . '=$matches[1]', 'top');
            }
            add_action('post_type_link', array(&$this, 'createPermalink'), 10, 100);
        }
    }

    public function createPermalink( $link, $post, $leavename, $sample )
    {
        if ( strtolower($this->type) != $post->post_type ) {
            return $link;
        }
        
        $draft_or_pending = false;
        if (isset($post->post_status) && in_array($post->post_status, array( 'draft', 'pending', 'auto-draft', 'future'))) {
            $draft_or_pending = true;
        }

        if (!$draft_or_pending || $sample) {
            $postName = ($leavename) ? "/%{$post->post_type}%" : "/{$post->post_name}";
                        
            if (!empty($this->settings['rewrite'])) {
                return site_url($this->slug . $postName);
            } elseif (!empty($post->post_parent)) {
                return rtrim(get_permalink($post->post_parent), '/') . $postName . '/';
            } elseif (Post::getCustomPostPageId($post->post_type)) {
                return rtrim(get_permalink(Post::getCustomPostPageId($post->post_type)), '/') . $postName . '/';
            } else {
                return site_url($postName);
            }
        }
        
        return $link;
    }
}
