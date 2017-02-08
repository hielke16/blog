<?php

namespace Webwijs\Template;

use Webwijs\View;
use Webwijs\Util\Strings;
use Webwijs\System\AbstractFileSystem;

class Loader
{
    static public function load()
    {
        if (is_robots())    { do_action('do_robots');                   exit; }
        if (is_feed())      { do_feed();                                exit; }
        if (is_trackback()) { include ABSPATH . 'wp-trackback.php';     exit; }

        if     ( is_404()            && $template = get_404_template()            ) {}
        elseif ( is_search()         && $template = get_search_template()         ) {}
        elseif ( is_tax()            && $template = get_taxonomy_template()       ) {}
        elseif ( is_front_page()     && $template = get_front_page_template()     ) {}
        elseif ( is_attachment()     && $template = get_attachment_template()     ) {
            remove_filter('the_content', 'prepend_attachment');
        }
        elseif ( is_single()         && $template = get_single_template()         ) {}
        elseif ( is_page()           && $template = get_page_template()           ) {}
        elseif ( is_category()       && $template = get_category_template()       ) {}
        elseif ( is_tag()            && $template = get_tag_template()            ) {}
        elseif ( is_author()         && $template = get_author_template()         ) {}
        elseif ( is_date()           && $template = get_date_template()           ) {}
        elseif ( is_archive()        && $template = get_archive_template()        ) {}
        elseif ( is_paged()          && $template = get_paged_template()          ) {}
        else {
            $GLOBALS['wp_query']->set_404();
            status_header( 404 );
			nocache_headers();
            $template = get_404_template();
        }

        $template = apply_filters('template_include', $template);
        $view = new View();
        $content = $view->render($template);


        $layout = locate_template(array(apply_filters('theme_layout', 'layout.php', $template)));
        if ($layout) {
            $layoutView = new View();
            $layoutView->content = $content;
            $content = $layoutView->render($layout);
        }

        echo apply_filters('theme_html_output', $content);

        exit;
    }

    public function locate($templates, $directories = array())
    {
        $fileSystem = AbstractFileSystem::getFileSystem();

        $templates = (array) $templates;
        foreach ($templates as $template) {
            if (!$fileSystem->isAbsolute($template)) {
                foreach ($directories as $baseDirectory) {
                    $filename = Strings::addTrailing($baseDirectory, DIRECTORY_SEPARATOR) . ltrim($template, DIRECTORY_SEPARATOR);
                    if (file_exists($filename)) {
                        return $filename;
                    }
                }
            } else if (file_exists($template)) {
                return $template;
            }
        }
        return '';
    }
}
