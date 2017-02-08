<?php

namespace Theme\Admin\Controller\Form;

/**
 * The GroupBuilderInterface facilitates in the creation of {@link Group} instances.
 * A {@link Group} instance is a specific collection type which contains zero or more form elements.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
interface GroupBuilderInterface
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
     * Build a new {@link DisplayGroup} instance.
     *
     * @return DisplayGroup a display group containing the elements of this builder.
     */
    public function build();
}
