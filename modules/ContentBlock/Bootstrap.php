<?php
namespace Module\ContentBlock;

use Webwijs\AbstractBootstrap;
use Webwijs\Module\ModuleInfo;
use Webwijs\Module\ModuleAwareInterface;

use Webwijs\Module\AutoloadableInterface;
use Webwijs\Shortcode\ViewHelper;

use Module\ContentBlock\Cpt\ContentBlock as ContentBlockCpt;
use Module\ContentBlock\Admin\Bootstrap as AdminBootstrap;

/**
 * The ContentBlock bootstrap
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 */
class Bootstrap extends AbstractBootstrap implements ModuleAwareInterface, AutoloadableInterface
{
    /**
     * A collection of socials plugins.
     *
     * @var ListInterface
     */
    private static $plugins = null;
    private static $module = null;

    /**
     * Register this module with the Standard PHP Library (SPL).
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Webwijs\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Module\ContentBlock' => __DIR__,
                ),
            ),
        );
    }

    /**
     * Register custom post type with WordPress.
     */
    protected function _initCpt()
    {
        ContentBlockCpt::register();
    }

    /**
     * Register shortcodes with WordPress.
     */
    protected function _initShortcode()
    {
        $helper = new ViewHelper();
        add_shortcode('content-block', array($helper, 'blockShortcode'));
    }

    protected function _initWidget()
    {
        register_widget('Module\ContentBlock\Widget\ContentBlock');
        register_widget('Module\ContentBlock\Widget\FeaturedContentBlock');
        register_widget('Module\ContentBlock\Widget\Slide');
        /**
         * Comment this line out when you don't need slick
         * Note: you still need a slick-theme.scss file to start styling it.
         */
        self::loadSlick();
    }

    public function setModule(ModuleInfo $module)
    {
        self::$module = $module;
    }

    public function loadSlick(){
      if (get_option('theme_advanced_enable_slick')) {
        add_action('wp_enqueue_scripts', function(){
          $module = $this->getModule();
          wp_enqueue_script('slick-js', $module->getUrl(). '/assets/slick/slick.js', ['jquery']);
          wp_enqueue_style('slick', $module->getUrl(). '/assets/slick/slick.css');
        });
      }
    }
    /**
     * Returns the {@link ModuleInfo} instance for this module.
     *
     * @return ModuleInfo object containing information about this module.
     */
    public static function getModule()
    {
        return self::$module;
    }
    /**
     * Initializes the admin bootstrap for the backend.
     * @return void
     */
    public function _initAdmin()
    {
        $adminBootstrap = new AdminBootstrap();
        add_action('admin_menu', array(&$adminBootstrap, 'init'));
    }

}
