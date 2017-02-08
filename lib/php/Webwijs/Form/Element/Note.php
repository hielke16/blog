<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

class Note extends Element
{
    /**
     * Default form view helper to use for rendering.
     *
     * @var string
     */
    public $helper = 'note';
    
    /**
     * Ignore flag (used when retrieving values at form level)
     *
     * @var bool
     */
    protected $ignore = true;


    /**
     * Return true because a note does not require validation.
     *
     * @return bool returns true.
     */
    public function isValid()
    {
        return true;
    }
}
