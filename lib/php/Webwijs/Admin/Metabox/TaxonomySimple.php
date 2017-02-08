<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class TaxonomySimple extends AbstractMetabox
{
    static public $id = 'taxononomy_simple';
    static public $title = 'Categorie';
    static public $context = 'side';
    static public $priority = 'low';
    public $options = null;
    public function init()
    {
        $defaults = array(
            'taxonomies' => get_object_taxonomies($this->postType),
            'show_option_none' => '--',
            'name' => 'taxonomy',
        );
        $this->options = array_merge($defaults, (array) $this->options);
    
        foreach ($this->options['taxonomies'] as $taxonomy) {
            remove_meta_box($taxonomy . 'div', $this->postType, 'normal');
        }
    }
    public function display($post)
    {        
        ?>
        <?php foreach ($this->options['taxonomies'] as $taxonomy): ?>
        <?php $selected = $this->_getCurrent($post, $taxonomy) ?>
        <p>
            <?php if (count($this->options['taxonomies']) > 1): ?>
            <?php echo esc_attr(get_taxonomy($taxonomy)->labels->name) ?><br />
            <?php endif ?>
            
            <?php foreach (get_terms($taxonomy, array('hide_empty' => false)) as $term): ?>
            <label><input type="checkbox" name="tax_input[<?php echo $taxonomy ?>][]" value="<?php echo $term->term_id ?>" <?php checked(in_array($term->term_id, $selected)) ?> /> <?php echo esc_attr($term->name) ?></label><br />
            <?php endforeach ?>
        </p>
        <?php endforeach ?>
        <?php
    }
    public function save($postId)
    {
    }
    protected function _getCurrent($post, $taxonomy)
    {
        $current = array();
        $terms = get_the_terms($post->ID, $taxonomy);
        if (is_array($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $current[] = $term->term_id;
            }
        }
        return $current;
    }
}
