<?php

namespace Theme\Admin\Controller\Form;

use Webwijs\Form;

/**
 * The FormBuilder is a concrete implementation of the {@link FormBuilderInterface}.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
class FormBuilder extends AbstractBuilder implements FormBuilderInterface
{
    /**
     * The form decorators.
     *
     * @var array
     */
    private $formDecorators = array(
        'Elements',
        'Groups',
    );

    /**
     * The display group decorators.
     *
     * @var array
     */
    private $groupDecorators = array(
        'Elements',
        array('HtmlTag', array('tag' => 'table', 'attribs' => array('class' => 'form-table')))
    );

    /**
     * The element decorators.
     *
     * @var array
     */
    private $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'p')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'th')),
        array('HtmlTag', array('tag' => 'tr'))
    );

    /**
     * The name of the group.
     *
     * @var string
     */
    private $name = '';

    /**
     * A collection of {@link GroupBuilderInterface} instances.
     *
     * @var array
     */
    private $groups = array();

    /**
     * A collection of {@link Element} instances.
     *
     * @var array
     */
    private $elements = array();

    /**
     * Construct a new FormBuilder.
     *
     * @param string $name the name of the form.
     * @param array $formDecorators (optional) the decorators which render this form
     * @param array $groupDecorators (optional) the decorators which render the display groups.
     * @param array $elementDecorators (optional) the decorators which render the elements.
     */
    public function __construct($name, $formDecorators = null, $groupDecorators = null, $elementDecorators = null)
    {
        $this->formDecorators    = (is_array($formDecorators)) ? $formDecorators : $this->formDecorators;
        $this->groupDecorators   = (is_array($groupDecorators)) ? $groupDecorators : $this->groupDecorators;
        $this->elementDecorators = (is_array($elementDecorators)) ? $elementDecorators : $this->elementDecorators;
    }

    /**
     * Set the name of the form.
     *
     * @param string $name the name of the form.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function setName($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
	    }

        $this->name = $name;
    }


    /**
     * Returns the name of the form.
     *
     * @return string the name of the form.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified group name is not a string.
     */
    public function group($name, $title = null)
    {
      if($title == null){
        $title = $name;
      }
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
	    }

        if (!isset($this->groups[$name])) {
            $this->groups[$name] = new GroupBuilder($name, $this->groupDecorators, $this->elementDecorators);
            $this->groups[$name]->setTitle($title);
        }

        return $this->groups[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function add($name, $type = 'text', array $options = array())
    {
        // add default decorators.
        if (!isset($options['decorators'])) {
            $options['decorators'] = $this->elementDecorators;
        }

        $this->elements[$name] = $this->createElement($name, $type, $options);
        return $this;
    }

    /**
     * Reset the builder to it's original state.
     */
    public function reset()
    {
        $this->elements = array();
        $this->groups   = array();
    }

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $form = new Form($this->name, array('decorators' => $this->formDecorators));
        foreach ($this->groups as $group) {
            $form->addDisplayGroup($group->build());
        }
        foreach ($this->elements as $element) {
            $form->addElement($element);
        }

        return $form;
    }
}
