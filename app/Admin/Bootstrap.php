<?php

namespace Theme\Admin;

use Webwijs\AbstractBootstrap;
use Webwijs\Admin\Metabox\PageLayout;
use Webwijs\Admin\Metabox\Excerpt;
use Webwijs\Admin\Metabox\Multibox;
use Theme\Admin\Controller\SettingsController;
use Theme\Admin\Controller\SidebarsController;
use Webwijs\Admin\Metabox\Text;

class Bootstrap extends AbstractBootstrap
{
    protected function _initSettings()
    {
        add_action('admin_head', function(){
          wp_enqueue_style('bootstrap', get_template_directory_uri(). '/assets/lib/css/bootstrap.css');
        });
        $builder = SettingsController::getBuilder();
        $builder->group('page', __('Pagina koppelingen'));
        $builder->group('hide', 'Verberg elementen');
        $builder->group('form', 'Formulieren')
                ->add('theme_form_newsletter', 'formSelect', array('label' => __('Nieuwsbriefformulier')));

        $builder->group('advanced', __('Geavanceerd'))
                ->add('theme_advanced_flat_url', 'checkbox', array('label' => __('Platte URL\'s')))
                ->add('theme_advanced_varnish', 'checkbox', array('label' => __('Varnish inschakelen')))
                ->add('theme_advanced_redis_cache', 'checkbox', array('label' => __('Redis Cache inschakelen')))
                ->add('theme_advanced_scss_compiler', 'compilerSelect', array('label' => __('SCSS compiler modus')))
                ->add('theme_advanced_compile_scss_now', 'compile', array('label' => __('Compileer SCSS nu')));

        $builder->group('company', __('Contactgegevens'))
                ->add('theme_company_email', 'text', array('label' => __('E-mailadres')))
                ->add('theme_company_phone', 'text', array('label' => __('Telefoonnummer (algemeen)')))
                ->add('theme_company_helpdesk', 'text', array('label' => __('Telefoonnummer (helpdesk)')))
                ->add('theme_company_commerce_number', 'text', array('label' => __('KVK-nummer')))
                ->add('theme_company_tax_number', 'text', array('label' => __('BTW-nummer')))
                ->add('theme_company_iban_number', 'text', array('label' => __('IBAN')))
                ->add('theme_company_address', 'text', array('type' => 'textarea', 'label' => __('Vestigingsplaats')));

        new SettingsController();
    }

    protected function _initSidebars()
    {
        $sidebarsController = new SidebarsController();
    }

    protected function _initRoles()
    {
        add_filter('editable_roles', array('Theme\Admin\Filter\User', 'editableRoles'));
    }

    protected function _initUsers()
    {
        Multibox::register('user', array('id' => 'userdata','title' => 'gebruikersinformatie', 'boxes' => array(
            array('class' => 'Webwijs\Admin\Metabox\Text' , 'settings' => array('id' => 'first_name', 'title' => 'voornaam')),
            array('class' => 'Webwijs\Admin\Metabox\Text' , 'settings' => array('id' => 'last_name', 'title' => 'achternaam'))
        )));
    }

    protected function _initEditor()
    {
        //add_filter('mce_external_plugins', array('Theme\Admin\Filter\TinyMCE', 'addButtonPlugin'));
        //add_filter('mce_buttons_2', array('Theme\Admin\Filter\TinyMCE', 'registerButtons'));

        add_filter('mce_buttons_2', array('Theme\Admin\Filter\TinyMCE', 'styleSelect'));
        add_filter('tiny_mce_before_init', array('Theme\Admin\Filter\TinyMCE', 'stylesDropdown'));

        add_action('admin_init', array('Theme\Admin\Filter\TinyMCE', 'enqueueStyles'));
        add_action('admin_head', array('Theme\Admin\Filter\TinyMCE', 'editorStyle'));
        add_filter('tiny_mce_before_init', array('Theme\Admin\Filter\TinyMCE', 'editorInit'));
    }

    protected function _initAdminLayout()
    {
        add_action('admin_head', array('Theme\Admin\Action\AdminLayout', 'menuLayout'));
        add_action('admin_menu', array('Theme\Admin\Action\AdminLayout', 'menuItems'));
        add_action('wp_dashboard_setup', array('Theme\Admin\Action\AdminLayout', 'removeWidgets'));
        add_action('add_meta_boxes', array('Theme\Admin\Action\AdminLayout', 'removeMetaBoxes'));
        add_action('add_meta_boxes', array('Theme\Admin\Action\YoastSeo', 'lessIntrusive'));
        add_action('add_meta_boxes', array('Theme\Admin\Action\AdminLayout', 'removeMetaBoxes'));
        add_filter('manage_posts_columns', array('Theme\Admin\Action\AdminLayout', 'listTableColumns'), 10, 2);
        add_filter('manage_pages_columns', array('Theme\Admin\Action\AdminLayout', 'listTableColumns'), 10, 2);
        foreach (get_post_types() as $post_type) {
            add_filter('get_user_option_closedpostboxes_' . $post_type, array('Theme\Admin\Action\AdminLayout', 'closedMetaBoxes'));
            add_filter('get_user_option_metaboxhidden_' . $post_type, array('Theme\Admin\Action\AdminLayout', 'hiddenMetaBoxes'));
        }
    }

    protected function _initPages()
    {
        Multibox::register('page', array('id' => 'settings', 'title' => 'Instellingen', 'boxes' => array(
            array('class' => 'Webwijs\Admin\Metabox\Visibility'),
            array('class' => 'Webwijs\Admin\Metabox\MenuOrder')
        )));

        PageLayout::register('page');
        Excerpt::register('page');
    }
}
