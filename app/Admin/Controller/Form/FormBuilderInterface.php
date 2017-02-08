<?php

namespace Theme\Admin\Controller\Form;

/**
 * The FormBuilderInterface facilitates in the creation of a {@link Form} instance.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
interface FormBuilderInterface
{
    /**
     * Add a form element to the builder under the specified name.
     *
     * @param string $name the name of the form element.
     * @param string $type (optional) the element type.
     * @param array $options (optional) the element options.
     * @return BundleBuilderInterface allows this method call to be chained. 
     */
    public function add($name, $type = 'text', array $options = array());
    
    /**
     * Returns a {@link GroupBuilderInterface} instance for the specified name.
     *
     * @param string $name the name of the group.
     * @return GroupBuilderInterface a group builder for the specified group name.
     */
    public function group($name);
    
    /**
     * Build a new {@link Form} instance.
     *
     * @return Form a form containing the elements of this builder.
     */
    public function build();
}
