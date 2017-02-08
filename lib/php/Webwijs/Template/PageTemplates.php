<?php

namespace Webwijs\Template;

use SplFileInfo;
use Webwijs\Util\Arrays;

/**
 * Page Templates
 *
 * Registers page templates to use with the Wordpress page template system
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class PageTemplates
{

    /**
     * Contains an array of page templates. 
     * The keys contain the file path and the value contains the name of the template.
     * @var array $pageTemplates associative array which contains the template path and names
     */
    protected static $pageTemplates = array();

    /**
     * Sets a page template.
     * The name of the template is extracted from the template it's comments.
     * Optional a name prefix can be added for displaying the name on the backend.
     * @param SplFileInfo $template   the template
     * @param string      $namePrefix the name prefix
     */
    public static function setPageTemplate(SplFileInfo $template, $namePrefix = null)
    {
        if (preg_match( '|Template Name:(.*)$|mi', file_get_contents($template->getPathName()), $header)){
           
            $name = '';
            if($namePrefix){
                $name .= $namePrefix;
            }
            $name .= _cleanup_header_comment($header[1]);

            self::$pageTemplates[self::getPath($template)] = $name;    
        }
    }

    /**
     * Function to call when applying the templates to the 
     * @param  array $templates array containing the already applied page templates
     * @return array $templates array containing the already applied page templates with the new templates added
     * @see  wp-includes/class-wp-theme.php filter 'theme_page_templates'
     */
    public static function applyTemplates($templates)
    {
        return Arrays::addAll($templates, self::$pageTemplates);
    }

    /**
     * Replace the full path with the relative path from the Wordpress template directory
     * @param  SplFileInfo $file the file to extract the full path from
     * @return string the relative path to the file
     */
    private static function getPath(SplFileInfo $file)
    {
        return str_replace(get_template_directory(), '', $file->getPathName());
    }
    
}