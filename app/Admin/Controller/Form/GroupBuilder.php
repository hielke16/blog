<?php

namespace Theme\Admin\Controller\Form;

use Webwijs\Form\DisplayGroup;

/**
 * The GroupBuilder is a concrete implementation of the {@link GroupBuilderInterface}.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
class GroupBuilder extends AbstractBuilder implements GroupBuilderInterface
{
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
     private $title = '';

    /**
     * A collection of {@link Element} instances.
     *
     * @var array
     */
    private $elements = array();

    /**
     * Construct a new GroupBuilder.
     *
     * @param string $name the name of the group.
     * @param array $groupDecorators (optional) the decorators which render this display group.
     * @param array $elementDecorators (optional) the decorators which render the elements.
     */
    public function __construct($name, $groupDecorators = null, $elementDecorators = null)
    {
        $this->setName($name);
        $this->groupDecorators   = (is_array($groupDecorators)) ? $groupDecorators : $this->groupDecorators;
        $this->elementDecorators = (is_array($elementDecorators)) ? $elementDecorators : $this->elementDecorators;
    }

    /**
     * Set the name of the display group.
     *
     * @param string $name the name of display group.
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
     * Returns the name of the display group.
     *
     * @return string the name of the display group.
     */
    public function getName()
    {
        return $this->name;
    }  /**
       * Set the name of the display group.
       *
       * @param string $name the name of display group.
       * @throws InvalidArgumentException if the specified argument is not a string.
       */
      public function setTitle($title)
      {
  	    if (!is_string($title)) {
              throw new \InvalidArgumentException(sprintf(
                  '%s: expects a string argument; received "%s" instead.',
                  __METHOD__,
                  (is_object($title)) ? get_class($title) : gettype($title)
              ));
  	    }

          $this->title = $title;
      }

      /**
       * Returns the name of the display group.
       *
       * @return string the name of the display group.
       */
      public function getTitle()
      {
          return $this->title;
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
    }

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $group = new DisplayGroup($this->name, array('decorators' => $this->groupDecorators, 'title' => $this->getTitle()));
        foreach ($this->elements as $element) {
            $group->addElement($element);
        }

        return $group;
    }
}
