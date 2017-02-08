<?php

namespace Module\ContentBlock\Widget;
use Module\ContentBlock\Bootstrap;
use Webwijs\View;
use Webwijs\Http\Request;

class Slide extends AbstractImageWidget
{
    public function __construct()
    {
        $options = array('classname' => 'widget-featured-contentblock', 'description' => 'Een widget die kan worden gebruik als slide in een slider.');
        parent::__construct('slider-widget', 'Slide', $options);

        if (is_admin()) {
	        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	    }
    }

    /**
     * Displays the widget using values retrieved from the database.
     *
     * @param array $args an array containing (generic) arguments for all widgets.
     * @param array $instance array the values stored in the database.
     */
    public function widget($args, $instance)
    {
        $view = new View();
        $defaults = array(
            'attachment_id' => '',
            'classname'     => '',
            'title'         => '',
            'url_type'      => 'none',
            'url_text'      => '',
            'url_title'     => '',
            'url_link'      => '',
            'url_page'      => ''
        );
        $instance = array_merge($defaults, (array) $instance);
        if (!empty($instance['classname'])) {
            $args['before_widget'] = preg_replace('#class="#', 'class="' . $instance['classname'] . ' ', $args['before_widget'], 1);
        }
        echo $args['before_widget'];
        echo $view->partial('partials/widgets/slide.phtml', array_merge($args, $instance, array('url' => $this->getUrl($instance), 'title' => nl2br($instance['title']))));
        echo $args['after_widget'];
    }

    /**
     * The form that is displayed in wp-admin and is used to save the settings for this widget.
     *
     * @param array $instance the form values stored in the database.
     */
    public function form($instance)
    {
        $defaults = array(
            'attachment_id' => '',
            'classname'     => '',
            'title'         => '',
            'content'         => '',
            'url_type'      => 'none',
            'url_text'      => '',
            'url_title'     => '',
            'url_link'      => '',
            'url_page'      => ''
        );
        $instance = array_merge($defaults, (array) $instance);

        $images = get_posts(array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
        ));
        $view = new View();
    ?>
        <?php if (is_numeric($instance['attachment_id']) && ($url = wp_get_attachment_url($instance['attachment_id']))): ?>
        <div class="image-container">
            <img src="<?php echo esc_url($url) ?>" />
        </div>
        <?php endif ?>
        <p>
            <label for="<?php echo $this->get_field_id('attachment_id') ?>">Afbeelding:</label>
            <?php echo $view->dropdown($this->get_field_name('attachment_id'), array(
                'class' => 'widefat widget-image-dropdown-field',
                'selected' => $instance['attachment_id'],
                'options' => $this->asOptions($images),
            )); ?>
        </p>


        <p><label>Titel <small style="float: right; font-weight: bold;">(optioneel)</small><br />
            <?php echo $view->formText(
                $this->get_field_name('title'),
                $instance['title'],
                array(
                    'class' => 'widefat',
                    'rows'  => '2',
                )
            ) ?>
        </label></p>
        <p><label>Content<br />
        <?php echo $view->formTextarea(
            $this->get_field_name('content'),
            $instance['content'],
            array(
                'class' => 'widefat',
                'rows'  => '4',
            )
        ) ?>
    </label></p>

        <div class="url-container">
            <div class="form-container">
                <label class="url-type-label">URL instellingen</label>
                <?php echo $view->formRadio(
                    $this->get_field_name('url_type'),
                    $instance['url_type'],
                    array(),
                    array(
                        'none'          => 'Geen link tonen',
                        'external-url'  => 'Link naar een externe pagina',
                        'existing-page' => 'Link naar een bestaande pagina'
                    )
                ) ?>
            </div>
            <div class="content-container <?php echo (!$this->matchesAny($instance['url_type'], array('external-url', 'existing-page'))) ? 'hidden': '' ?>">
                <span class="url-field <?php echo (!$this->matchesAny($instance['url_type'], array('external-url'))) ? 'hidden': '' ?>" data-url-type="external-url">
                    <p><label>Geef de volledige URL op<br />
                        <?php echo $view->formText(
                            $this->get_field_name('url_link'),
                            $instance['url_link'],
                            array('class' => 'widefat')
                        ) ?>
                    </label></p>
                </span>
                <span class="url-field <?php echo (!$this->matchesAny($instance['url_type'], array('existing-page'))) ? 'hidden': '' ?>" data-url-type="existing-page">
                    <p><label>Selecteer een pagina<br />
                        <?php echo $view->dropdownPosts(array(
                            'post_types' => array_diff(get_post_types(array('public' => true)), array('attachment')),
                            'name' => $this->get_field_name('url_page'),
                            'class' => 'widefat',
                            'selected' => $instance['url_page'],
                            'show_option_none' => false
                        )) ?>
                    </label></p>
                </span>
                <p><label>Tekst<br />
                    <?php echo $view->formText(
                        $this->get_field_name('url_text'),
                        $instance['url_text'],
                        array('class' => 'widefat')
                    ) ?>
                </label></p>
                <p><label>Titel <small style="float: right; font-weight: bold;">(optioneel)</small><br />
                    <?php echo $view->formText(
                        $this->get_field_name('url_title'),
                        $instance['url_title'],
                        array('class' => 'widefat')
                    ) ?>
                </label></p>
            </div>
        </div>

        <p><label>CSS-class voor container <small style="float: right; font-weight: bold;">(optioneel)</small><br />
            <?php echo $view->formText(
                $this->get_field_name('classname'),
                $instance['classname'],
                array('class' => 'widefat')
            ) ?>
        </label></p>
    <?php
    }

    /**
     * Filter and normalize the form values before they are updated.
     *
     * @param array $new_instance the values entered in the form.
     * @param array $old_instance the previous form values stored in the database.
     * @return array the filtered form values that will replace the old values.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['attachment_id'] = (isset($new_instance['attachment_id']) && is_numeric($new_instance['attachment_id'])) ? (int) $new_instance['attachment_id'] : '';
        $instance['classname'] = (isset($new_instance['classname'])) ? strip_tags($new_instance['classname']) : null;
        $instance['title']     = (isset($new_instance['title'])) ? strip_tags($new_instance['title']) : null;
        $instance['content']     = (isset($new_instance['content'])) ? strip_tags($new_instance['content']) : null;
        $instance['url_type']  = (isset($new_instance['url_type'])) ? strip_tags($new_instance['url_type']) : null;
        $instance['url_text']  = ($instance['url_type'] != 'none' && isset($new_instance['url_text'])) ? strip_tags($new_instance['url_text']) : '';
        $instance['url_title'] = ($instance['url_type'] != 'none' && isset($new_instance['url_title'])) ? strip_tags($new_instance['url_title']) : '';
        $instance['url_link']  = ($instance['url_type'] != 'none' && isset($new_instance['url_link'])) ? strip_tags($new_instance['url_link']) : '';
        $instance['url_page']  = ($instance['url_type'] != 'none' && isset($new_instance['url_page']) && is_numeric($new_instance['url_page'])) ? (int) $new_instance['url_page'] : '';

        return $instance;
    }

    /**
     * returns true if the value matches any of the given values.
     *
     * @param mixed $value the value to be tested.
     * @param array|Traversable $values the values to match.
     * @param bool $strict if true strict comparison will be performed.
     * @return bool true if the given value matches with at least one of the values, false otherwise.
     */
    private function matchesAny($value, $values, $strict = true)
    {
        if (!is_array($values) && !($values instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($values) ? get_class($values) : gettype($values))
            ));
        }

        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        }

        return in_array($value, $values, (bool) $strict);
    }

    /**
     * Returns an url that is created from values stored in the database.
     *
     * @param array $instance the form values stored in the database.
     * @return string a string representation of an url, or empty string.
     * @see self::createUrl($text, $attr);
     */
    private function getUrl($instance)
    {
        if ($instance['url_type'] == 'none') {
            return '';
        }

        $url = $instance['url_link'];
        if ($instance['url_type'] == 'existing-page') {
            $url = get_permalink($instance['url_page']);
        }
        //return $this->createUrl($instance['url_text'], array('title' => $instance['url_title'], 'href' => $url, 'class' => 'readmore collapsed'));

        return array('title' => $instance['url_title'], 'text' => $instance['url_text'], 'href' => $url, 'class' => 'readmore collapsed');
    }

    /**
     * Creates a hyperlink or anchor from the given text and attributes.
     *
     * @param string $text the test to be displayed by the anchor element.
     * @param array|null $attr (optional) attributes that belong to an anchor element.
     * @return string an anchor element containing the given text and attributes.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    private function createUrl($text, $attr = null)
    {
	    if (!is_string($text)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($text) ? get_class($text) : gettype($text))
            ));
	    }

        $defaults = array(
            'href' => '#',
            'target' => '_blank',
        );
        $attr = array_merge($defaults, (array) $attr);

        $siteUrl   = site_url();
        $hyperlink = esc_url($attr['href']);
        if ((strpos($hyperlink, $siteUrl)) !== false) {
            unset($attr['href']);
            unset($attr['target']);
        } else {
            unset($attr['href']);
        }

        array_walk($attr, function (&$value, $key) {
            $value = sprintf('%s="%s"', $key, esc_attr($value));
        });
        $attr['href'] = sprintf('href="%s"', $hyperlink);

        return sprintf('<a %s><span><span>%s</span></span></a>', implode(' ', $attr), $text);
    }


    /**
     * Converts the given collection of WP_Post ojects into an array that can be
     * used to populate a dropdown form.
     *
     * @param array|\Traversable $posts a collection of posts.
     * @return array an array consisting of (dropdown) options.
     * @throws \InvalidArgumentException if the given argument is not an array or an instance of Traversable.
     */
    protected function asOptions($posts)
    {
        if (!is_array($posts) && !($posts instanceof Traversable)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($posts) ? get_class($posts) : gettype($posts))
            ));
        }

        $options = array();
        foreach ($posts as $post) {
            $options[$post->ID] = $post->post_title;
        }
        return $options;
    }

    /**
     * Handles an asynchronous HTTP request and finds the image url associated
     * with the given attachment id.
     *
     * The attachment id is retrieved from the POST or GET superglobal depending
     * on the HTTP request method used with the AJAX request.
     *
     * @return void
     * @link http://codex.wordpress.org/AJAX_in_Plugins#Ajax_on_the_Administration_Side
     */
    public function getAttachmentUrl()
    {
        $request = new Request();
        switch ($request->getMethod()) {
            case 'POST':
                $attachmentId = $request->getPost('attachment_id', 0);
                break;
            case 'GET':
            default:
                $attachmentId = $request->getQuery('attachment_id', 0);
                break;
        }
        if ($url = wp_get_attachment_url($attachmentId)) {
            echo esc_url($url);
        }
        exit();
    }

    /**
     * Enqueue necessary scripts into the admin page.
     *
     * @param string $hook identifies a page, which can be used to target a specific admin page.
     * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
     */
    public function enqueueScripts($hook)
    {
        if ('widgets.php' != $hook) {
            return;
        }
        $module = Bootstrap::getModule();
        wp_enqueue_script('jquery.widget-contentblock', $module->getUrl() . '/assets/js/jquery.widget-contentblock.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-load-image-url', get_bloginfo('stylesheet_directory') . '/assets/lib/js/admin/jquery.load-image-url.js', array('jquery'), false, true);
        wp_enqueue_style('admin-lib-style', get_bloginfo('stylesheet_directory') . '/assets/lib/css/admin.css');
        wp_enqueue_style('admin-style', get_bloginfo('stylesheet_directory') . '/assets/lib/css/admin.css');
    }
}
