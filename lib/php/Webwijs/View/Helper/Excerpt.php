<?php

namespace Webwijs\View\Helper;

class Excerpt
{
    public function excerpt( $length = 18, $post = null ) {
        if (is_null($post)) {
            $post = $GLOBALS['post'];
        }
        $text = $raw_excerpt = trim($post->post_excerpt);
        if ( '' == $text ) {
            $text = $post->post_content;
            $text = strip_shortcodes($text);

            $text = preg_replace('/<h(\d)>.*?<\/h\\1>\s*/', '', $text);
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = str_replace('&nbsp;', ' ', $text);
            $text = strip_tags($text);
        }
        $excerpt_length = apply_filters('excerpt_length', $length);
        $excerpt_more = apply_filters('excerpt_more', '&hellip;');
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = preg_replace('/[,.:!#$%^&*]$/', '', $text);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        $text = apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
        $text = apply_filters('the_excerpt', $text);
        return $text;
    }
}
