<?php
namespace Theme;

use Webwijs\Application;
use Webwijs\Post;
use Webwijs\Loader\AutoloaderFactory;
use Webwijs\Loader\ClassLoader;
use Webwijs\Shortcode\ViewHelper;
use Webwijs\AbstractBootstrap;
use Webwijs\CSS\MainCompiler as CSSCompiler;
use Theme\Cpt\User;
use Theme\Cpt\Blog;

require_once dirname(__DIR__) . '/lib/php/Webwijs/AbstractBootstrap.php';
require_once dirname(__DIR__) . '/lib/php/Webwijs/Application.php';

class Bootstrap extends AbstractBootstrap
{
    public function init()
    {
        $application = new Application();
        $application->init();
        parent::init();
    }

    protected function _initAutoloaderConfig()
    {
        AutoloaderFactory::factory(array(
            'Webwijs\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Theme' => __DIR__,
                ),
            ),
        ));
    }

    protected function _initCpt()
    {
        User::register();
        Blog::register();
    }

    /**
     * Add static resources to the class loader.
     */
    protected function _initResourceloaders()
    {
        ClassLoader::addStaticResources(array(
            'viewhelper'  => 'Theme\Helper',
            'formelement' => 'Theme\Admin\Controller\Form\Element',
        ));
    }

    protected function _initModels()
    {
        Application::getModelManager()
            ->addModel('Sidebar', 'Webwijs\Model\Sidebar');
    }

    protected function _initLayouts()
    {
        Application::getServiceManager()->get('PageLayout')
            ->addLayout('two-columns-right', array(
                'name' => 'Sidebar rechts',
                'icon' => 'assets/lib/images/layouts/two-columns-right.png',
                'sidebars' => array('top-widgets' => 'Top', 'footer-widgets' => 'Footer', 'col-right' => 'Rechts'),
            ))
            ->addLayout('two-columns-left', array(
                'name' => 'Sidebar links',
                'icon' => 'assets/lib/images/layouts/two-columns-left.png',
                'sidebars' => array('top-widgets' => 'Top', 'footer-widgets' => 'Footer', 'col-left' => 'Links'),
            ))
            ->addLayout('one-column', array(
                'name' => 'Eén kolom',
                'icon' => 'assets/lib/images/layouts/one-column.png',
                'sidebars' => array('top-widgets' => 'Top', 'footer-widgets' => 'Footer'),
            ))
            ->addLayout('three-columns', array(
                'name' => 'Sidebar links en rechts',
                'icon' => 'assets/lib/images/layouts/three-columns.png',
                'sidebars' => array('content-primary' => 'Eerste rij', 'footer-widgets' => 'Footer', 'col-left' => 'Links', 'col-right' => 'Rechts'),
            ))
             ->addLayout('home', array(
                'name' => 'Home',
                'icon' => 'assets/lib/images/layouts/one-column.png',
                'sidebars' => array('home-blocks' => 'bloken'),
            ))
            ->setDefaultLayout('two-columns-right');

        $options = array(
            'before_widget' => '<div class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        );

        $sidebars = Application::getModelManager()->getTable('Sidebar')->findAll();
        if(is_array($sidebars)) {
            foreach ($sidebars as $sidebar) {
                register_sidebar(array_merge($options, array('name' => $sidebar->name, 'id' => $sidebar->code)));
            }
        }
        add_filter('theme_default_sidebar', array('Theme\Filter\PageLayout', 'getDefaultSidebar'), 10, 3);
    }

    protected function _initMenus()
    {
        register_nav_menu('main', 'Hoofdmenu');
        register_nav_menu('footer', 'Footermenu');

        add_filter('wp_nav_menu_objects', array('Theme\Filter\Menu', 'itemAncestors'));
        add_filter('show_admin_bar', '__return_false');
        add_filter('sitemap_post_types', array('Theme\Filter\Sitemap', 'PostTypes'));
    }

    protected function _initFilters()
    {
        add_filter('slideshow_content', function($content) {
            $lines = preg_split( '/\r\n|\r|\n/', $content);
            if(is_array($lines) && !empty($lines)) {
                $content = '';
                foreach($lines as $index => $line) {
                    $content .= sprintf('<span class="t%s">%s</span>', $index, $line);
                }
            }

            if($priority = has_filter('the_content', 'wpautop')) {
                remove_filter('the_content', 'wpautop');
                $content = apply_filters('the_content', $content);
                add_filter('the_content', 'wpautop', $priority);
            }
            return $content;
        });
    }

    protected function _initWidgets()
    {
        remove_action('init', 'wp_widgets_init', 1);
        add_action('init', array('Theme\Action\Widgets', 'init'));

        register_widget('Webwijs\Widget\SectionNav');

        add_action('widgets_init', function() {
            unregister_widget('WP_Widget_Recent_Posts');
        });
    }

    protected function _initCSSCompiler()
    {
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            self::compileSCSS(true);
        } else {
            $compilerMode = get_option('theme_advanced_scss_compiler');
            switch ($compilerMode) {
                case '':
                    self::compileSCSS();
                    return true;
                    break;
                case 'forced':
                    self::compileSCSS(true);
                    return true;
                    break;
                case 'disabled':
                    return false;
                    break;
                default:
                    self::compileSCSS();
                    return true;
            }
        }

    }

    public static function compileSCSS($force = false)
    {
            $cssCompiler = new CSSCompiler('SCSS');
            $cssCompiler->compile($force);
    }

    protected function _initAjax()
    {
        add_action('wp_ajax_compile_scss', array('Theme\Admin\Ajax', 'compile'));
    }

    protected function _initMultipleThumbnails()
    {
        if (class_exists('MultiPostThumbnails')) {
            $types = array('branche', 'page', 'post', 'project');
            foreach($types as $type) {
                new \MultiPostThumbnails(array(
                    'label' => __('Header afbeelding', 'theme'),
                    'id' => 'header-image',
                    'post_type' => $type
                    )
                );
            }
        }
    }

    protected function _initImages()
    {
        add_theme_support('post-thumbnails');
        add_image_size('thumbnail', 168, 108, true);
        add_image_size('homepage-header', 1920, 560, true);
        add_image_size('default-header', 1920, 262, true);

        add_filter('post_thumbnail_html', array('Webwijs\Filter\Placeholder', 'getPlaceholder'), 10, 5);
    }

    protected function _initShortCodes()
    {
        $helper = new ViewHelper;
        add_shortcode('sitemap', array($helper, 'sitemap'));
        add_shortcode('button', array($helper, 'button'));

        add_filter('shortcode_html', array('Webwijs\Filter\HTML', 'shortcode'));
    }

    protected function _initContentWidth()
    {
        //important for the admin editor and resizing objects and images
        $GLOBALS['content_width'] = 460;
    }

    protected function _initSearch()
    {
        add_filter('posts_search', array('Theme\Filter\Search', 'filter'), 10, 2);
    }

    protected function _initXmlSitemap()
    {
        add_filter('option_sm_options', array('Theme\Filter\Sitemap', 'xmlSitemapExclude'));
    }

    protected function _initHeader()
    {
        remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
        remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
        remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
        remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
        remove_action( 'wp_head', 'index_rel_link' ); // index link
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
        remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
        remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    }

    // protected function _initForms()
    // {
    //     add_action("gform_editor_js", array('Theme\Filter\GForms', 'editorScript'));
    //     add_filter('gform_field_standard_settings', array('Theme\Filter\GForms', 'fieldSettings'), 10, 2);
    //     add_filter('gform_form_settings', array('Theme\Filter\GForms', 'formSettings'), 10, 2);
    //     add_filter('gform_pre_form_settings_save', array('Theme\Filter\GForms', 'formSave'));
    //     add_filter('gform_tooltips', array('Theme\Filter\GForms', 'setTooltips'));
    //     add_filter('gform_submit_button', array('Theme\Filter\GForms', 'submitButton'), 10, 2);
    //     add_filter('gform_submit_button', array('Theme\Filter\GForms', 'addFooterDescription'), 100, 2);
    //     add_filter('gform_field_input', array('Theme\Filter\GForms', 'placeholder'), 10, 5);
    //     add_filter('gform_field_content', array('Theme\Filter\GForms', 'fieldContent'), 10, 5);
    //     add_filter('gform_field_css_class', array('Theme\Filter\GForms', 'fieldClasses'), 10, 3);
    // }

    protected function _initMinHtml()
    {
        add_filter('theme_html_output', array('Webwijs\Filter\Minify', 'html'));
    }
	protected function _initTranslations()
    {
        $languageDir = dirname(__FILE__) . '/i18n';
        $locale = get_locale();
        if (file_exists($languageDir . '/gravityforms.' . $locale . '.mo')) {
            self::_loadTextDomain('gravityforms', $languageDir . '/gravityforms.' . $locale . '.mo');
        }
        add_action('init', function() {
            $locale = get_locale();
            $languageDir = dirname(__FILE__) . '/i18n';
            if ($locale != 'nl_NL') {
                Bootstrap::_loadTextDomain('default', $languageDir . '/theme.' . $locale . '.mo');
            }
        });
    }
    /* bypass load_textdomain to avoid filters */
    public static function _loadTextDomain($domain, $mofile)
    {
        global $l10n;
        $mo = new \MO();
        if (!file_exists($mofile) || !$mo->import_from_file($mofile)) {
            return false;
        }
        if (isset($l10n[$domain]) && !empty($l10n[$domain]->entries)) {
            $l10n[$domain]->merge_with($mo);
        }
        else {
            $l10n[$domain] = $mo;
        }
    }
}
