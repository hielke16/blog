<?php

namespace Theme\Admin\Action;

class YoastSeo
{
    public static function lessIntrusive() 
    {
        global $wp_meta_boxes;
        foreach (array_keys($wp_meta_boxes) as $postType) {
            if (isset($wp_meta_boxes[$postType]['normal']['high']['wpseo_meta'])) {
                $wp_meta_boxes[$postType]['advanced']['low']['wpseo_meta'] = $wp_meta_boxes[$postType]['normal']['high']['wpseo_meta'];
                $wp_meta_boxes[$postType]['advanced']['low']['wpseo_meta']['title'] = 'Zoekmachineoptimalisatie';
                unset($wp_meta_boxes[$postType]['normal']['high']['wpseo_meta']);
            }
        }
    }
}
