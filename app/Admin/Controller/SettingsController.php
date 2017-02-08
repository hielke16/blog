<?php

namespace Theme\Admin\Controller;

use Theme\Admin\Controller\Form\FormBuilder;

use Webwijs\Admin\Controller;
use Webwijs\Http\Request;

class SettingsController extends Controller
{
    /**
     * A form builder.
     *
     * @var FormBuilderInterface|null
     */
    private static $builder = null;

    /**
     * A prefix by which the controller is identified.
     *
     * @var string
     */
    public $prefix = 'theme-settings';

    /**
     * Initialize the controller.
     */
    public function init()
    {
        add_menu_page('Thema instellingen', 'Thema instellingen', 'edit_posts', 'theme-settings-index',  array(&$this, 'render'), '', 64);
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-cookie', get_bloginfo('template_url').'/assets/lib/js/jquery.cookie.js', array('jquery'));

        $builder = self::getBuilder();
        $builder->group('page')
                ->add('theme_page_contact', 'postSelect', array('label' => __('Contactpagina')));

        $builder->group('hide')
                ->add('theme_hide_posts', 'checkbox', array('label' => __('Berichten')))
                ->add('theme_hide_comments', 'checkbox', array('label' => __('Reacties')))
                ->add('theme_hide_links', 'checkbox', array('label' => __('Koppelingen')))
                ->add('theme_hide_categories', 'checkbox', array('label' => __('Categorieën')))
                ->add('theme_hide_tags', 'checkbox', array('label' => __('Tags')))
                ->add('theme_hide_authors', 'checkbox', array('label' => __('WordPress auteurs')));
    }

    public function indexAction()
    {
        $form = self::getBuilder()->build();

        $request = new Request();
        if ($request->isPost() && wp_verify_nonce($request->getPost('_wpnonce'), 'form-theme-settings') && $form->isValid($_POST)) {
            $elements = $form->getElements();
            foreach ($form->getValues() as $name => $value) {
                update_option($name, $value);
            }
        }

        // populate form.
        $elements = $form->getElements();
        foreach ($elements as $element) {
            $name = $element->getName();
            $element->setValue(get_option($name));
        }

        $this->view->form = $form;
    }

    /**
     * Returns the {@link FormBuilderInterface} instance used by this controller.
     *
     * @return FormBuilderInterface a form builder.
     */
    public static function getBuilder()
    {
        if (self::$builder === null) {
            self::$builder = new FormBuilder('theme-settings-form');
        }

        return self::$builder;
    }
}
