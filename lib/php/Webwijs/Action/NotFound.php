<?php

namespace Webwijs\Action;

class NotFound
{
    /**
     * Query WordPress to find our custom 404-page and reapply the 404 flag afterwords.
     *
     * @param WP The WordPress environment setup class.
     * @see wp-includes/class-wp.php
     * @see WP::main($query_args)
     */
    public static function query404($wp)
    {
        $wp_query = $GLOBALS['wp_query'];
        if ($wp_query->is_main_query() && $wp_query->is_404()) {
            if (($postId = get_option('theme_page_404', '')) !== '') {
                $wp_query->query(array('page_id' => $postId));
                $wp_query->set_404();
                
                // setup globals again.
                $wp->register_globals();
            }
        }
    }
    
    /**
     * Prevent the canonical redirect filter from redirecting to our 404-page.
     * 
     * @param string $redirect_url the redirect URL.
     * @param string $requested_url the requested URL.
     * @return string|bool the url to redirect to, or false to stop the redirect.
     * @see wp-includes/default-filters.php
     * @see wp-includes/canonical.php
     * @see redirect_canonical($requested_url, $do_redirect)
     */
    public static function stopRedirect($redirect_url, $requested_url)
    {
        if (is_404()) {
            $redirect_url = false;      
        }   
        return $redirect_url;
    }
    
    /**
     * Returns the absolute path to a template.
     * 
     * @param string $template the 404 template.
     * @return string the template to load.
     * @see wp-includes/template.php
     * @see get_query_template($type, $templates)
     */
    public static function locateTemplate($template)
    {    
        $template_name = get_page_template_slug();
        if ($template_name && validate_file($template_name) === 0) {
            $page_template = locate_template(array($template_name));
            return ($page_template) ? $page_template : $template;
        }
        
        return $template;    
    }
}
