<?php

namespace Webwijs\Admin\Metabox;

use Webwijs\Admin\AbstractMetabox;

class Multibox extends AbstractMetabox
{
    /**
     * metadata from which metaboxes can be instantiated.
     *
     * @var array
     */
    private $boxes = array();

    /**
     * metaboxes that have been instantiated.
     *
     * @var array|null
     */
    private $registeredBoxes = null;
    
    /**
     * Initialize the multibox by creating new metaboxes from the provided metadata.
     *
     * @see Multibox::getCreatedBoxes()
     */
    public function init()
    {
        // register metaboxes from the provided metadata.
        $this->registerMetaboxes();
    }
    
    /**
     * Display a form or other html elements which can be used associate meta data with 
     * a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     */
    public function display($post)
    {
        // delegate displaying of html to each metabox.
        foreach ($this->getCreatedBoxes() as $createdBox) {
    ?>
    
            <h4><?php echo esc_attr($createdBox->getTitle()) ?></h4>
            <div>
                <?php $createdBox->display($post) ?>
            </div>
    <?php
        }
    }
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function save($postId)
    {
        // delegate saving of data to each metabox.
        foreach ($this->getCreatedBoxes() as $createdBox) {
            $createdBox->save($postId);
        }
    }
    
    /**
     * Register metaboxes from the provided metadata.
     *
     * @return array array containing metaboxes.
     * @throws \InvalidArgumentException throws exception if a metabox doesn't provide a
     *                                  ID by which it can be identified.
     * @throws \LogicException throws exception if a metabox with the given ID already
     *                        exists within the multibox.
     */
    protected function registerMetaboxes()
    {
        // an array to hold all new metaboxes.
        $this->registeredBoxes = array();
        foreach ($this->boxes as $settings) {
            // post type for which the metabox will be registered.
            $postType = $this->getPostType();
            // instantiate a new metabox.
            $newBox = $this->createBox($settings);
            if (is_object($newBox)) {
                // get settings from the newly created metabox.
                $settings = $newBox->getSettings();
                // a metabox is required to have an ID field.
                if (!is_string($settings['id']) || strlen($settings['id']) == 0) {
                    throw new \InvalidArgumentException(sprintf(
                        '%s requires that %s has an ID by which it can identified.', 
                        __METHOD__,
                        get_class($newBox)
                    ));
                } 
                
                /*
                 * compare ID field of new metabox with that of existing metaboxes 
                 * and throw a logic exception if a duplicate ID field is found.
                 */
                foreach ($this->registeredBoxes as $createdBox) {
                    if ($createdBox->getId() == $newBox->getId()) {
                        throw new \LogicException(sprintf(
                            'Duplicate metabox found, %s requires an unique ID for it can be registered.', 
                            get_class($newBox)
                        ));
                    }
                }
                
                // add new metabox to array.
                $this->registeredBoxes[] = $newBox;
            }
        }
    }
    
    /**
     * Returns a new metabox from the given settings.
     *
     * @param array|Traversable $options an array or Traversable containing settings 
     *                                   used to register the metabox.
     * @return AbstractMetabox a new metabox with the given settings.
     */
    protected function createBox($options)
    {
        // post type for which the metaboxes will be registered.
        $postType = $this->getPostType();
        // settings associated with the metabox.
        $settings = (isset($options['settings'])) ? $options['settings'] : null;

        // create metabox from the given classname.
        $className = $options['class'];
        if (class_exists($className)) {
            // instantiate new metabox.
            return new $className($postType, $settings);
        }
    }
    
    /**
     * Returns an array with metaboxes registered for this multibox.
     *
     * @return array an array containing zero or more metaboxes.
     */
    public function getCreatedBoxes()
    {
        // register new metaboxes if required.
        if (is_null($this->registeredBoxes) && !empty($this->boxes)) {
            $this->registerMetaboxes();
        }
    
        return $this->registeredBoxes;
    }
    
    /**
     * Set the metadata used to instantiate metaboxes.
     *
     * @param array $boxes array containing meta data to create a metabox.
     */
    public function setBoxes(array $boxes)
    {
        $this->boxes = $boxes;
    }
    
    /**
     * Returns the metadata from which the multibox is able to instantiate metaboxes.
     *
     * @return array|null the metadata used to instantiate multiboxes.
     */
    public function getBoxes()
    {
        return $this->boxes;
    }
}
