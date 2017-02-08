<?php

namespace Webwijs;

use Webwijs\Loader\ClassLoader;
use Webwijs\Util\Strings;

use Webwijs\View\Directories;

use Webwijs\View\Helper\Exception\HelperNotFoundException;

use Webwijs\Template\Loader as TemplateLoader;
use Webwijs\Template\Exception\TemplateNotFoundException;

class View
{
    protected $helpers = array();

    protected $defaultDirectories = array('', STYLESHEETPATH, TEMPLATEPATH);
    
    public $directories;

    public $viewDirectories;

    public $templateLoader;
    
    public function __construct()
    {
        $this->viewDirectories = new Directories($this->defaultDirectories);
        $this->templateLoader = new TemplateLoader();
    }

    public function render($script, $directories = array())
    {  
        if (!is_array($directories) && !($dirs instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($directories) ? get_class($directories) : gettype($directories))
            ));
        }

        $template = $this->locateTemplate($script, $directories);
        
        if (!empty($template)) {
            ob_start();
            include $template;
            return ob_get_clean();
        }

        throw new TemplateNotFoundException(
            sprintf('Template not found: %s, Template directories: %s', print_r($script, 1), print_r($this->directories->getDirectories(), 1))
        );
    }

    public function locateTemplate($script, $directories = array()){
        $directories = array_reverse(array_merge((array) $directories, $this->getDirectories()));
        return $this->templateLoader->locate($script, $directories);
    }

    public function getLayout()
    {
        return new View();
    }

    public function getHelper($name)
    {
        if (!isset($this->helpers[$name])) {
            $class = ClassLoader::loadStatic('viewhelper', ucfirst($name));
            if (!empty($class)) {
                $helper = new $class;
                $helper->view = $this;
                $this->helpers[$name] = $helper;
            }
            else {
                $this->helpers[$name] = false;
            }
        }
        return $this->helpers[$name];
    }

    public function getDirectories()
    {
        return $this->viewDirectories->getDirectories();
    }

    public function registerDirectories($directories)
    {
        $this->viewDirectories->registerDirectories($directories);
    }

    public function registerDirectory($directory)
    {
        $this->viewDirectories->registerDirectory($directory);
    }

    public function __call($name, $args)
    {
        $helper = $this->getHelper($name);
        if (!empty($helper)) {
            return call_user_func_array(
                array($helper, $name),
                $args
            );
        }
        else {
            throw new HelperNotFoundException(sprintf('Helper not found: %s', $name));
        }
    }
}
