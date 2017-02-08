<?php

namespace Webwijs\Admin;

use ReflectionClass;

/**
 * An abstract base class from which concrete metaboxes can inherit so
 * that they can be registered to a specific post type.
 *
 * @author Sjokki de Wit, Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
abstract class AbstractMetabox
{
    /**
     * array containing metabox ID's for post types.
     *
     * @var array
     */
    private static $metaboxIds = array();

    /**
     * settings used by the metabox.
     *
     * @var array
     */
    protected $settings = array(
        'id'       => '',
        'title'    => '',
        'context'  => 'side',
        'priority' => 'low',
    );
    
    /**
     * the post type to which this metabox belongs.
     *
     * @var string.
     */
    protected $postType;
    
    /**
     * Allows a metabox to introspect itself.
     *
     * @var ReflectionClass
     */
    protected $reflection;
    
    /**
     * Create a new metabox for a post type with the given settings.
     *
     * @param string $postType the post type for which the metabox will be created.
     * @param array|Traversable|null $settings an array or Traversable containing settings used to register the metabox, 
     *                                        or null in which case the default settings of the metabox will be used.
     */
    public function __construct($postType, $settings = null)
    {
        // store name of the post type.
        $this->postType = $postType;
        // set setting for the metabox.
        if (!is_null($settings)) {
            $this->setSettings($settings);
        }
        
        // allow a concrete implementation to do it's work.
        $this->init();
    }
    
    /**
     * Method which will be called once the metabox has been created
     * and can be overridden by a concrete implementation of the metabox.
     *
     * @return void
     */
    public function init()
    {}
    
    /**
     * Display a form or other html elements which can be used associate meta 
     * data with a particular post.
     *
     * @param WP_Post $post the post object which is currently being displayed.
     * @link http://codex.wordpress.org/Class_Reference/WP_Post
     * @link https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    public abstract function display($post);
    
    /**
     * Allows the meta data entered on the admin page to be saved with a
     * particular post.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public abstract function save($postId);
    
    /**
     * Methods that calls the {@see AbstractMetabox::save($postId)} method 
     * when the post is being saved.
     *
     * @param int $postId the ID of the post that the user is editing.
     */
    public function doSave($postId)
    {
        if ($this->canSave($postId)) {
            $this->save($postId);
        }
    }
    
    /**
     * Returns true if the meta data can and should be saved, false otherwise.
     *
     * @param int $postId the ID of the post that the user is editing.
     * @return bool returns true if and only if the user can save a post and
     *              the metabox belongs to the post that is being saved.
     */
    protected function canSave($postId)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (!current_user_can('edit_post', $postId)) {
            return false;
        }
        if (get_post_type($postId) != $this->postType) {
            return false;
        }
        return true;
    }
    
    /**
     * Returns an array containing settings used by the metabox.
     *
     * @return array array containing settings used by the metabox.
     */
    public function getSettings()
    {
        return $this->settings;
    }    
    
    /**
     * Returns the title for this metabox, if no title is set an empty
     * string will be returned.
     *
     * @return string the title for this metabox.
     */
    public function getTitle()
    {
        $settings = $this->getSettings();
        return $settings['title'];
    }
    
    /**
     * Returns the context for this metabox, default context is 'side'.
     *
     * @return string the context for this metabox.
     */
    public function getContext()
    {
        $settings = $this->getSettings();
        return $settings['context'];
    }
    
    /**
     * Returns the post type for which this metabox is registered.
     *
     * @return string the post type for this metabox.
     */
    public function getPostType()
    {
        return $this->postType;
    }
    
    /**
     * Returns the id for this metabox, if no id is set an empty string
     * will be returned.
     *
     * @return string the id for this metabox.
     */
    public function getId()
    {
        $settings = $this->getSettings();
        return $settings['id'];
    }
    
    /**
     * Returns the priority for this metabox, default priority is 'low'.
     *
     * @return string the priority for this metabox.
     */
    public function getPriority()
    {    
        $settings = $this->getSettings();
        return $settings['priority'];
    }
    
    /**
     * Set settings for this metabox which include but are not
     * limited to it's id, context and priority.
     * 
     * @param array|\Traversable $settings one or more settings.
     */
    protected function setSettings($settings)
    {
        if (!is_array($settings) && !($settings instanceof Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object of settings; received "%s"',
                __METHOD__,
                (is_object($settings) ? get_class($settings) : gettype($settings))
            ));
        }
        
        // allow the element to introspect on itself.
        $reflection = $this->reflection;
        if (!($reflection instanceof ReflectionClass)) {
            $this->reflection = $reflection = new ReflectionClass($this);
        }
        
        /*
         * iterate over the array of settings and invoke the appropriate
         * method if possible, otherwise set it as property of the element.
         */
        foreach ($settings as $key => $value) {
            $forbidden = array('settings');
            if (in_array(strtolower($key), $forbidden)) {
                continue;
            }
        
            // create method name for the given setting.
            $methodName = $this->getMethodName($key);
        
            // find a (possible) mutator method for the given setting.
            $mutatorMethod = sprintf('set%s', $methodName);
            if ($reflection->hasMethod($mutatorMethod)) {
                $method = $reflection->getMethod($mutatorMethod);
                // only invoke method if it has a public access level.
                if ($method->isPublic() && $method->getNumberOfParameters() == 1) {
                    // let method store the value.
                    $method->invoke($this, $value);
                } else {
                    // set value as property.
                    $this->{$key} = $value;
                }
            } else if (isset($this->settings[$key])) {
                // override an existing setting.
                $this->settings[$key] = $value;
            } else {
                // set value as property.
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * Normalize the given name so that whitespace, underscores or hyphens
     * are removed and capitalize the subsequent character following any of
     * these characters.
     *
     * @param string $name the name that will be normalized.
     * @return string a normalized string.
     */
    private function getMethodName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
        
        // array containing parts of the original name.
        $parts = explode('_', str_replace(array(' ', '-'), '_', $name));
        
        /*
         * capitalize first character of each newly formed word and glue
         * all words together making one single word.
         */
        $normalized = '';
        if (is_array($parts)) {
            $normalized = implode('', array_map('ucfirst', $parts));
        }
        
        return $normalized;
    }
    
    /**
     * Returns a field name that is unique to this metabox by prepending the
     * ID field of the metabox to the given name.
     *
     * @param string $name the name for which a field name will be returned.
     * @return string a unique field name for this metabox.
     * @see AbstractMetabox::normalize($name);
     */
    protected function getName($name = '')
    {   
        // normalize the given name.
        $normalizedName = $this->normalize($name);
        if (is_string($normalizedName) && strlen($normalizedName) > 0) {
            // return name with the ID field of the metabox prepended.
            return sprintf('_%s_%s', $this->getId(), $normalizedName);
        }

        // return metabox id as name.
        return sprintf('_%s', $this->getID()); 
    }
    
    /**
     * Returns a member of the $_POST superglobal.
     *
     * The given key will be normalized and prefixed with the ID field of 
     * the metabox so that the key reflects the field name generated by 
     * the {@link AbstractMetabox::getName($name)} method.
     *
     * @param string $key the name of the member.
     * @param mixed $default the default value if the
     *                       key does not exist.
     * @return mixed the value associated with the key.
     * @see AbstractMetabox::normalize($name);
     */
    protected function getPostValue($key, $default = null)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($key) ? get_class($key) : gettype($key))
            ));
        }
        
        $key = $this->getName($key);
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }
    
    /**
     * Retrieve post meta field for a post.
     *
     * The given meta key will be normalized and prefixed with the ID field of 
     * the metabox so that the meta key is unique for this metabox.
     *
     * @param int $post_id the post ID.
     * @param string $key optional. the meta key to retrieve. by default, returns data 
     *                    for all keys.
     * @param bool $single whether to return a single value.
     * @return mixed will be an array if $single is false. will be value of meta data 
     *               field if $single is true.
     * @link https://codex.wordpress.org/Function_Reference/get_post_meta
     * @see get_post_meta($post_id, $key, $single);
     */
    protected function addPostMeta($postId, $metaKey = '', $metaValue, $unique = false)
    {
        // prepend ID field of this metabox to the metakey.
        $metaKey = $this->getName($metaKey);
        // delete post meta through WordPress.
        return get_post_meta($postId, $metaKey, $metaValue);
    }
    
    /**
     * Update post meta field based on post ID.
     *
     * The given meta key will be normalized and prefixed with the ID field of 
     * the metabox so that the meta key is unique for this metabox.
     *
     * @param int $post_id the post ID.
     * @param string $meta_key the metadata key.
	 * @param mixed $meta_value the metadata value. Must be serializable if non-scalar.
	 * @param mixed $prev_value optional. Previous value to check before removing.
	 * @return bool true on success, false on failure.
     * @link https://codex.wordpress.org/Function_Reference/update_post_meta
	 * @see update_post_meta($post_id, $meta_key, $meta_value, $prev_value)
     */
    protected function updatePostMeta($postId, $metaKey, $metaValue, $prevValue = '')
    {
        // prepend ID field of this metabox to the metakey.
        $metaKey = $this->getName($metaKey);
        // update post meta through WordPress.
        return update_post_meta($postId, $metaKey, $metaValue, $prevValue);
    }
    
    /**
     * Remove metadata matching criteria from a post.
     * 
     * The given meta key will be normalized and prefixed with the ID field of 
     * the metabox so that the meta key is unique for this metabox.
     *
     * @param int $post_id post ID
     * @param string $meta_key Metadata name.
     * @param mixed $meta_value Optional. Metadata value. Must be serializable if non-scalar.
     * @return bool true on success, false on failure.
     * @link https://codex.wordpress.org/Function_Reference/delete_post_meta
     * @see delete_post_meta($post_id, $meta_key, $meta_value)
     */
    protected function deletePostMeta($postId, $metaKey, $metaValue = '')
    {
        // prepend ID field of this metabox to the metakey.
        $metaKey = $this->getName($metaKey);
        // delete post meta through WordPress.
        return delete_post_meta($postId, $metaKey, $metaValue);
    }
    
    /**
     * Retrieve post meta field for a post.
     *
     * The given meta key will be normalized and prefixed with the ID field of 
     * the metabox so that the meta key is unique for this metabox.
     *
     * @param int $post_id the post ID.
     * @param string $key optional. the meta key to retrieve. by default, returns data 
     *                    for all keys.
     * @param bool $single whether to return a single value.
     * @return mixed will be an array if $single is false. will be value of meta data 
     *               field if $single is true.
     * @link https://codex.wordpress.org/Function_Reference/get_post_meta
     * @see get_post_meta($post_id, $key, $single);
     */
    protected function getPostMeta($postId, $metaKey = '', $single = false)
    {
        // prepend ID field of this metabox to the metakey.
        $metaKey = $this->getName($metaKey);
        // delete post meta through WordPress.
        return get_post_meta($postId, $metaKey, $single);
    }
    
    /**
     * Normalizes the given name by making the name lowercase and replacing 
     * whitespace, hyphens are replaced with an underscore. Special characters
     * are removed so a human-readable name can be returned.
     *
     * @param string $name the name that will be normalized.
     * @return string a normalized string.
     */
    private function normalize($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
        
        // lowercase name and replace whitespace and hypens.
        $name = str_replace(array(' ', '-'), '_', strtolower($name));
        // remove special characters from the name.
        $normalized = preg_replace('/[^A-Za-z0-9_\[\]]/', '', $name);

        return $normalized;
    }

    /**
     * Returns true if the given string starts with the specified prefix, false otherwise.
     *
     * @param string $haystack the string to search in.
     * @param string $needle the prefix.
     * @return bool returns true if the character sequence represented by the argument is a 
     *              prefix of the character sequence represented by this string; false otherwise.
     */
    private function startsWith($haystack, $needle, $caseSensitive = true)
    {
        if($caseSensitive) {
            return strpos($haystack, $needle, 0) === 0;
        }
        return stripos($haystack, $needle, 0) === 0;
    } 
    
    /**
     * Register metabox to a post type with the specified settings.
     *
     * @param string $postType the post type to which the metabox will be registered.
     * @param array|Traversable $settings an array or Traversable containing settings 
     *                                   used to register the metabox.
     * @throws \InvalidArgumentException throws exception if a metabox doesn't provide a
     *                                  ID by which it can be identified.
     * @throws \LogicException throws exception if a metabox with the given ID already
     *                        exists for the given post type.
     * @link http://www.php.net/manual/en/language.oop5.late-static-bindings.php
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * @link http://codex.wordpress.org/Plugin_API/Action_Reference/save_post
     */
    public static function register($postTypes, $settings = null)
    {    
        // create an array from (possible) string.
        $postTypes = (array) $postTypes;
        // create metabox for each post type.
        foreach ($postTypes as $postType) {
            // instantiate metabox using late static binding.
            $metabox = new static($postType, $settings);
            // get settings from the newly created metabox.
            $settings = $metabox->getSettings();
            // a metabox is required to have an ID field.
            if (!is_string($settings['id']) || strlen($settings['id']) == 0) {
                throw new \InvalidArgumentException(sprintf(
                    '%s requires that %s has an ID by which it can identified.', 
                    __METHOD__,
                    get_class($metabox)
                ));
            } else if (self::isRegistered($postType, $settings['id'])) {
                throw new \LogicException(sprintf(
                    'Duplicate metabox found for %s. A metabox should have a unique ID for it can be registered.', 
                    get_class($metabox)
                )); 
            }

            // add metabox to the administrative interface.
            add_meta_box($settings['id'], $settings['title'], array(&$metabox, 'display'), $postType, $settings['context'], $settings['priority']);
            // allows metabox to save when a post is being saved.
            add_action('save_post', array(&$metabox, 'doSave'));
            // register new metabox preventing the existence of duplicate metaboxes.
            self::$metaboxIds[$postType][] = $settings['id'];
        }
    }
    
    /**
     * Returns true if a metabox is already registered with the given ID, false otherwise.
     *
     * The given ID will only be checked with metaboxes that were registered using the 
     * {@link AbstractMetabox::register($types, $settings)} method.
     *
     * @param string $postType the post type for which the metabox will be created.
     * @param string $id the ID for a metabox.
     * @return bool returns true if a metabox with the given ID is already registered,
     *              false otherwise.
     */
    public static function isRegistered($postType, $id)
    {
        $isRegistered = false;
        if (isset(self::$metaboxIds[$postType]) && is_array(self::$metaboxIds[$postType])) {
            $isRegistered = in_array($id, self::$metaboxIds[$postType]);
        }
        return $isRegistered;
    }
}
