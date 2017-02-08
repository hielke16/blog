<?php

namespace Theme\Admin\Controller;

use Webwijs\Application;
use Webwijs\Admin\Controller\AbstractController;
use Webwijs\Model\Sidebar;
use Theme\Admin\Form\SidebarForm;
use Theme\Admin\ListTable\Sidebars;

class SidebarsController extends AbstractController
{
    public $prefix = 'theme-sidebars';

    public function init()
    {
        add_submenu_page(
            'theme-settings-index',
            'Sidebars',
            'Sidebars',
            'edit_posts',
            'theme-sidebars',
            array(&$this, 'render')
        );
    }
     public function indexAction()
    {
        $list = $this->view->list = new Sidebars(array('singular' => 'sidebar', 'plural' => 'sidebars', 'ajax' => false, 'screen' => 'theme-sidebars'));
        $list->view = $this->view;
        $list->prepare_items();
        $this->view->pageLayoutService = Application::getServiceManager()->get('PageLayout');
        $this->view->sidebars = array();
        
        $table = Application::getModelManager()->getTable('Sidebar');
        if (!empty($table)) {
            $this->view->sidebars = $table->findAll();
        }
    }
    public function addAction()
    {
        $sidebar = new Sidebar();
        $form = $this->view->form = $this->_prepareForm($sidebar);
        $form->setNonce('theme-sidebars-add');
        if ($form->verifyNonce() && $form->isValid($_POST)) {
            $this->_exportFormValues($form, $sidebar);
            $sidebar->save();
            $this->view->redirectWithMessage('De sidebar is toegevoegd', 'success', 'admin.php?page=theme-sidebars');
        }
    }
    public function editAction()
    {
        if (!empty($_GET['sidebar_id'])) {
            $table = Application::getModelManager()->getTable('Sidebar');
            if (!empty($table)) {
                $sidebar = $table->find((int) $_GET['sidebar_id']);
                if ($sidebar) {
                    $form = $this->view->form = $this->_prepareForm($sidebar);
                    $form->setNonce('theme-sidebars-edit');
                    if ($form->verifyNonce() && $form->isValid($_POST)) {
                        $this->_exportFormValues($form, $sidebar);
                        $sidebar->save();
                        $this->view->redirectWithMessage('De sidebar is opgeslagen', 'success', 'admin.php?page=theme-sidebars');
                    }
                }
            }
        }
    }

    public function deleteAction()
    {
        if (!empty($_POST['sidebars']) && is_array($_POST['sidebars'])) {
            $ids = $_POST['sidebars'];
        }
        elseif (!empty($_GET['sidebar_id'])) {
            $ids = array($_GET['sidebar_id']);
        }

        if (!empty($ids)) {
            $table = Application::getModelManager()->getTable('Sidebar');
            if (!empty($table)) {
                $sidebars = $this->view->sidebars = $table->findBy(array('id' => $ids));
                if (!empty($sidebars)) {
                    if (!empty($_POST['confirm'])) {
                        foreach ($sidebars as $sidebar) {
                            $sidebar->delete();
                        }
                        $this->view->redirectWithMessage('De sidebars zijn verwijderd', 'success', 'admin.php?page=theme-sidebars');
                    }
                }
            }
        }
    }
    public function defaultsAction()
    {
        if (!empty($_POST['default'])) {
            $pageLayoutService = Application::getServiceManager()->get('PageLayout');
            foreach ((array) $_POST['default'] as $sidebarAreaCode => $sidebarId) {
                $pageLayoutService->setDefaultSidebar($sidebarAreaCode, $sidebarId);
            }
        }
        $this->view->redirectWithMessage('De standaard sidebars zijn ingesteld', 'success', 'admin.php?page=theme-sidebars');
    }
    protected function _prepareForm($model)
    {
        $form = new SidebarForm();
        $form->setDefaults($model->getData());
        return $form;
    }
    protected function _exportFormValues($form, $model)
    {
        $model->setData($form->getValues());
    }
}
