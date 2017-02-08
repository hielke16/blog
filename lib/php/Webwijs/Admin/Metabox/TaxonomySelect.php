<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class TaxonomySelect extends AbstractMetabox
{    
    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => 'taxononomy_select',
        'title'    => 'Categorie',
        'context'  => 'side',
        'priority' => 'low',
    );
    
    /**
     * Additional options used to display the metabox.
     *
     * @var array|null
     */
    public $options = null;
    
    /**
     * Method which will be called once the metabox has been created.
     *
     * @return void
     */
    public function init()
    {
        // post type for which the metabox is registered.
        $postType = $this->getPostType();
        // options used to build dropdown fields for each taxonomy.
        $defaults = array(
            'taxonomies' => get_object_taxonomies($postType),
            'show_option_none' => '--',
            'name' => 'taxonomy',
        );
        $this->options = array_merge($defaults, (array) $this->options);
        
        // remove metaboxes associated with the given taxonomies.
        foreach ($this->options['taxonomies'] as $taxonomy) {
            remove_meta_box($taxonomy . 'div', $postType, 'normal');
        }
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {        
    ?>
        <?php foreach ($this->options['taxonomies'] as $taxonomy): ?>
        <?php $selected = $this->_getCurrent($post, $taxonomy) ?>
        <p>
            <?php if (count($this->options['taxonomies']) > 1): ?>
            <?php echo esc_attr(get_taxonomy($taxonomy)->labels->name) ?><br />
            <?php endif ?>
            <select class="widefat" name="tax_input[<?php echo $taxonomy ?>][]">
                <?php if ($this->options['show_option_none']): ?>
                    <option value=""><?php echo $this->options['show_option_none'] ?></option>
                <?php endif ?>
                <?php foreach (get_terms($taxonomy, array('hide_empty' => false)) as $term): ?>
                    <option value="<?php echo $term->term_id?>" <?php selected($term->term_id, $selected) ?>><?php echo $term->name ?></option>
                <?php endforeach ?>
            </select></label>
        </p>
        <?php endforeach ?>
    <?php
    }
    
    /**
     * Allow built-in functions of WordPress to save the taxonomy.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {}
    
    /**
     * Return the term ID of the first term from the given taxonomy and post.
     *
     * @param object $post the post object.
     * @param string $taxonomy the taxonomy for which to retrieve terms.
     * @return int the term ID of the first term.
     */
    protected function _getCurrent($post, $taxonomy)
    {
        $current = 0;
        $terms = get_the_terms($post->ID, $taxonomy);
        if (is_array($terms) && !empty($terms)) {
            $first = reset($terms);
            $current = $first->term_id;
        }
        return $current;
    }
    
    /**
     * Set one or more options for this metabox.
     *
     * @param array $options array containing options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
