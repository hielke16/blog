<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Application;
use Webwijs\Admin\AbstractMetabox;
use Webwijs\Model\Sidebar;

class PageLayout extends AbstractMetabox
{
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'page_layout',
        'title'    => 'Pagina indeling',
        'context'  => 'normal',
        'priority' => 'high',
    );

    /**
     * Method which will be called once the metabox has been created
     * and can be overridden by a concrete implementation of the metabox.
     *
     * @return void
     */
    public function init()
    {
        remove_meta_box('pageparentdiv', 'page', 'side');
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {

        apply_filters('before_page_layout_display', array());

		$this->script();
		$this->style();
        $service = Application::getServiceManager()->get('PageLayout');
        $layouts = $service->getLayouts();
        $sidebarAreas = $service->getSidebars();
        
        $sidebars = array();
        $table = Application::getModelManager()->getTable('Sidebar');
        if (!empty($table)) {
            $sidebars = $table->findAll();
        }
        
        $postType = !empty($post->post_type) ? $post->post_type : $_GET['post_type'];
        if ($postType == 'page') {
            $templates = get_page_templates();
        }
        else {
            $templates = array('single-' . $postType . '.php');
        }
        $currentTemplate = get_post_meta($post->ID, '_wp_page_template', true);

        $currentLayout = get_post_meta($post->ID, '_page_layout', true);
        empty($currentLayout) && $currentLayout = $service->getDefaultLayout();



        ?>
        <div class="page-layouts-container">

            <div class="page-templates">
                <?php if ($postType == 'page' && count($templates) > 0 || count($templates) > 1): ?>
                <h4>Sjabloon</h4>
                <fieldset>
                    <select name="page_template" class="page-template">
                        <option value="default" data-layouts="<?php echo esc_attr(json_encode($service->getTemplateLayouts('page.php'))) ?>">Standaard template</option>
                        <?php foreach ($templates as $templateName => $template): ?>
                        <option value="<?php echo esc_attr($template) ?>" data-layouts="<?php echo esc_attr(json_encode($service->getTemplateLayouts($template))) ?>" <?php echo selected($template, $currentTemplate) ?>><?php echo esc_html($templateName) ?></option>
                        <?php endforeach ?>
                    </select>
                </fieldset>
                <?php else: ?>
                    <input type="hidden" class="page-template" name="page_template_default" data-layouts="<?php echo esc_attr(json_encode($service->getTemplateLayouts($templates[0]))) ?>">
                <?php endif ?>
            </div>


            <div class="layouts">
                <h4>Layouts</h4>
                <fieldset>
					<?php foreach ($layouts as $layout): ?>
					<label class="layout layout-<?php echo $layout->code ?>" title="<?php echo esc_attr($layout->name) ?>">
						<input type="radio" name="page_layout" data-sidebars="<?php echo esc_attr(json_encode(array_keys($layout->sidebars))) ?>" value="<?php echo $layout->code ?>" <?php echo checked($layout->code, $currentLayout) ?> />
						<img src="<?php echo get_bloginfo('stylesheet_directory') . '/' . $layout->icon ?>" alt="<?php echo esc_attr($layout->name) ?>"  />
					</label>
					<?php endforeach ?>
                </fieldset>
            </div>

            <div class="sidebars">
                <h4>Sidebars</h4>
                <fieldset>
					<?php foreach ($sidebarAreas as $sidebarAreaCode => $sidebarAreaName): ?>
                    <?php $currentSidebar = get_post_meta($post->ID, '_sidebar_' . $sidebarAreaCode, true) ?>
                    <?php empty($currentSidebar) && $currentSidebar = $service->getDefaultSidebar($sidebarAreaCode, $post) ?>
					<p class="sidebar sidebar-<?php echo $sidebarAreaCode ?>">
						<label><?php echo esc_html($sidebarAreaName) ?><br />
							<select name="sidebar_<?php echo $sidebarAreaCode ?>">
								<option value="empty">-- Leeg</option>
								<?php foreach ($sidebars as $sidebar): ?>
								<option value="<?php echo $sidebar->id ?>" <?php echo selected($sidebar->id, $currentSidebar) ?>><?php echo esc_html($sidebar->name) ?></option>
								<?php endforeach ?>
								<option value="new">-- Maak een nieuwe sidebar</option>
							</select>
						</label>
						<input class="new-sidebar" type="text" size="30" name="sidebar_<?php echo $sidebarAreaCode ?>_new" placeholder="Naam voor de nieuwe sidebar" />
					</p>
					<?php endforeach ?>
					<p class="description">
						<a href="admin.php?page=theme-sidebars" target="_blank">Sidebars beheren</a> | <a href="widgets.php" target="_blank">Widgets beheren</a>
					</p>
                </fieldset>
            </div>
        </div>
        <?php

         apply_filters('after_page_layout_display', array());
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        update_post_meta($postId, '_page_layout', @$_POST['page_layout']);

        $service = Application::getServiceManager()->get('PageLayout');
        foreach ($service->getSidebars() as $sidebarAreaCode => $sidebarAreaName) {
            $sidebarId = @$_POST['sidebar_' . $sidebarAreaCode];
            if ($sidebarId == 'new') {
                $name =  @$_POST['sidebar_' . $sidebarAreaCode . '_new'];
                if (!empty($name)) {
                    $sidebar = new Sidebar();
                    $sidebar->name = $name;
                    $sidebar->save();
                    $sidebarId = $sidebar->id;
                }
                else {
                    $sidebarId = $service->getDefaultSidebar($sidebarAreaCode);
                }
            }
            update_post_meta($postId, '_sidebar_' . $sidebarAreaCode, $sidebarId);
        }
    }
    protected function script()
    {
		?>
		<script>
            jQuery(function($) {
                var $container = $('.page-layouts-container');

                $container.find('.page-templates').on('change', '.page-template', function () {
                    if ($(this).is('select')) {
                        var $selectedOption = $(this).find('option:selected');
                    }
                    else {
                        var $selectedOption = $(this);
                    }
                    var layouts = $.parseJSON($selectedOption.attr('data-layouts'));
                    $container.find('.layout').hide();
                    if (layouts.length) {
                        var hasActiveLayout = false;
                        $.each(layouts, function (index, layoutCode) {
                            $container.find('.layout-' + layoutCode).show();
                            hasActiveLayout = hasActiveLayout || $container.find('.layout-' + layoutCode + ' input:checked').length > 0
                        });
                        if (!hasActiveLayout) {
                            $container.find('.layout-' + layouts[0] + ' input').attr('checked', 'checked').trigger('click');
                        }
                        $container.find('.layouts').slideDown();
                    }
                    else {
						$container.find('.layouts').slideUp();
						$container.find('.sidebars').slideUp();
					}
                })
                $container.find('.page-templates .page-template').trigger('change');

                $container.find('.layouts').on('click', 'input', function () {
                    var sidebars = $.parseJSON($(this).attr('data-sidebars'));

                    if (sidebars.length) {
						$container.find('.sidebar').hide();
                        $.each(sidebars, function (index, sidebarAreaCode) {
                            $container.find('.sidebar-' + sidebarAreaCode).show();
                        })
                        $container.find('.sidebars').slideDown();
                    }
                    else {
						$container.find('.sidebars').slideUp();
					}
                });
                $container.find('.layouts input:checked').trigger('click');

                $container.find('.sidebars').on('change', 'select', function () {
                    var $newInput = $(this).parents('p.sidebar').find('.new-sidebar');
                    if ($(this).val() == 'new') {
                        $newInput.show();
                    }
                    else {
                        $newInput.hide();
                    }
                });
                $container.find('.sidebars select').trigger('change');
            });
        </script>

		<?php
	}
    protected function style()
    {
        ?>
        <style type="text/css">
			.page-layouts-container {
				padding: 1em 0;
			}
			.page-layouts-container h4 {
				margin: 0
			}
            .page-layouts-container fieldset {
                padding: 0.9em 0.9em;
                border: 1px solid #ddd;
                background-color: #fff;
                border-radius: 3px;
                margin-bottom: 2em;
                position: relative;
            }
            .page-layouts-container .layouts label { margin-right: 30px }
            .page-layouts-container .layouts img { margin: 0 5px }
            .page-layouts-container img, .page-layouts-container input,  .page-layouts-container select, .page-layouts-container label {
				vertical-align: middle
			}

        </style>
        <?php
    }
}
