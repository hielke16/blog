<?php

namespace Webwijs;

use Webwijs\Autoloader as OldAutoloader;
use Webwijs\Loader\AutoloaderFactory;
use Webwijs\Loader\AutoloaderAdapter;
use Webwijs\Loader\ClassLoader;
use Webwijs\Loader\ClassLoaderAdapter;
use Webwijs\Cache\CacheManager;
use Webwijs\Cache\Loader\RedisLoader;
use Webwijs\Cache\Loader\XCacheLoader;
use Webwijs\Cache\Loader\MemcachedLoader;
use Webwijs\Cache\Loader\FileCacheLoader;
use Webwijs\Module\ModuleOptions;
use Webwijs\Module\ModuleManager;

class Application
{
    public static $serviceManager;

    public static $modelManager;

    private $cacheManager;

    public function init()
    {
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, '_init') === 0) {
                $this->$method();
            }
        }
    }

    protected function _initAutoload()
    {
        $autoloadFile = dirname(__DIR__) . '/vendor/autoload.php';
        if(file_exists($autoloadFile)){
            require_once $autoloadFile;
        } else {
            throw new \Exception(sprintf('Autoload file at path "%s" not found, did you run composer.json in the lib/php folder?', $autoloadFile));
        }

    }

    protected function _initAutoloaderConfig()
    {
        require_once __DIR__ . '/Loader/AutoloaderFactory.php';

        AutoloaderFactory::factory(array(
            'Webwijs\Loader\StandardAutoloader' => array(
                'autoregister_webwijs' => true
            ),
        ));
    }

    /**
     * Add static resources to the class loader.
     */
    protected function _initResourceloaders()
    {
        ClassLoader::addStaticResources(array(
            'viewhelper'        => 'Webwijs\View\Helper',
            'formdecorator'     => 'Webwijs\Form\Decorator',
            'formelement'       => 'Webwijs\Form\Element',
            'validator'         => 'Webwijs\Validate',
            'facetsearch'       => 'Webwijs\FacetSearch',
            'facetsearchfilter' => 'Webwijs\FacetSearch\Filter',
            'model'             => 'Webwijs\Model',
            'modeltable'        => 'Webwijs\Model\Table',
            'service'           => 'Webwijs\Service',
        ));
    }

    protected function _initSetup()
    {
        add_action('after_switch_theme', array('Webwijs\Action\MySQL', 'setupRelatedPosts'));
    }

    /**
     * Starts the session
     * @return void
     */
    protected function _initSession()
    {
      if(empty(get_option('theme_advanced_varnish'))){
        session_start();
      }
    }

    protected function _initUrls()
    {
        if (get_option('theme_advanced_flat_url')) {
            add_action('post_type_link', array('Webwijs\Action\FlatUrl', 'createCustomPermalink'), 10, 100);
            add_action('page_link', array('Webwijs\Action\FlatUrl', 'createPagePermalink'), 10, 100);
            add_action('parse_request', array('Webwijs\Action\FlatUrl', 'parseRequest'));
        }
        else {
            add_filter('term_link', array('Webwijs\Action\NestedUrl', 'createTermLink'), 10, 3);
            add_action('parse_request', array('Webwijs\Action\NestedUrl', 'parseRequest'));
            add_filter('get_pagenum_link', array('Webwijs\Action\NestedUrl', 'pagenumLink'));
            add_filter('redirect_canonical', array('Webwijs\Action\NestedUrl', 'redirectCanonical'), 10, 2);
            add_filter('year_link', array('Webwijs\Action\NestedUrl', 'yearLink'), 10, 2);
            add_filter('month_link', array('Webwijs\Action\NestedUrl', 'monthLink'), 10, 3);
        }
    }

    protected function _initErrorPage()
    {
        add_action('wp', array('Webwijs\Action\NotFound', 'query404'), 10, 1);
        add_filter('redirect_canonical', array('Webwijs\Action\NotFound', 'stopRedirect'), 10, 2);
        add_filter('404_template', array('Webwijs\Action\NotFound', 'locateTemplate'), 10, 1);
    }

    protected function _initSeo()
    {
        add_filter('wpseo_sitemap_exclude_taxonomy', array('Webwijs\Filter\Seo', 'excludeTaxonomy'), 10, 2);
    }

    /**
     * Initiates the module manager for loading the modules
     */
    protected function _initModules()
    {
        add_action('module_loaded', array('Webwijs\Module\Action\ModuleLoaded', 'addResources'));
        add_action('module_loaded', array('Webwijs\Module\Action\ModuleLoaded', 'setModule'), 10, 2);
        add_action('module_loaded', array('Webwijs\Module\Action\ModuleLoaded', 'autoloadModule'), 10, 2);
        add_action('module_loaded', array('Webwijs\Module\Action\ModuleLoaded', 'loadModulePageTemplates'), 99);
        add_action('module_loaded', array('Webwijs\Module\Action\ModuleLoaded', 'initModule'), 99, 2);

        $moduleOptions = new ModuleOptions();
        $moduleOptions->setCacheProvider($this->getCacheProvider());

        $moduleManager = new ModuleManager($moduleOptions);
        $moduleManager->loadModules(get_template_directory().'/modules');

    }

    /**
     * Returns the cache provider to use for caching the module routes
     * @return CacheProvider the cache driver to use
     */
    public function getCacheProvider()
    {
        if($this->cacheManager === null){
            $this->setCacheProvider();
        }
        return $this->cacheManager->getCacheProvider();
    }

    /**
     * Sets the cache provider.
     */
    public function setCacheProvider()
    {
        $this->cacheManager = new CacheManager();
        $this->cacheManager->addLoaders(array(
            new FileCacheLoader(),
            new MemcachedLoader(),
            new XCacheLoader()
        ));

        if (!empty(get_option('theme_advanced_redis_cache'))) {
            $this->cacheManager->addLoaders(array( new RedisLoader() ));
        }
    }

    /**
     * Adds custom page templates to the wordpress page templates
     * @return void
     */
    protected function _initPageTemplates()
    {
        add_filter('theme_page_templates', array('Webwijs\Template\PageTemplates', 'applyTemplates'));
    }

    /**
     * Adds the template loader to the template redirect action
     * @return void
     */
    protected function _initTemplateLoader()
    {
        add_action('template_redirect', array('Webwijs\Template\Loader', 'load'), 1000);
    }

    /**
     * Adds the custom Webwijs query to the post clauses filter
     * @return void
     */
    protected function _initWpQuery()
    {
        add_filter('posts_clauses', array('Webwijs\Filter\CustomQuery', 'filter'), 10, 2);
    }

    /**
     * This method remains to support code that still relies on the now deprecated
     * {@link Webwijs\Autoloader} class.
     *
     * @param Webwijs\Autoloader autoloader to resolve class names to file paths.
     * @deprecated 1.1.0 use the autoloader factory instead.
     */
    public static function pushAutoloader($autoloader)
    {
        if ($autoloader instanceof OldAutoloader) {
            // append prefixes to the StandardAutoloader.
            AutoloaderFactory::factory(array(
                'Webwijs\Loader\StandardAutoloader' => new AutoloaderAdapter($autoloader)
            ));

            // expand the stack of static resources.
            ClassLoader::addStaticResources(new ClassLoaderAdapter($autoloader));
        }
    }

    public static function getServiceManager()
    {
        if (is_null(self::$serviceManager)) {
            self::$serviceManager = new ServiceManager();
        }
        return self::$serviceManager;
    }
    public static function getModelManager()
    {
        if (is_null(self::$modelManager)) {
            self::$modelManager = new ModelManager();
        }
        return self::$modelManager;
    }
}
