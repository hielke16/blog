<?php

namespace Webwijs\Form;

use Webwijs\Form;

class GravityLayout extends Form
{
    public $defaultElementDecorators = array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'ginput_container'))),
        array('Description', array('tag' => 'div', 'attribs' => array('class' => 'gfield_description'))),
        array('Errors', array('tag' => false, 'itemTag' => 'div', 'itemAttribs' => array('class' => 'gfield_description validation_message'))),
        array('Label', array('attribs' => array('class' => 'gfield_label'))),
        array('HtmlTag', array('tag' => 'li', 'attribs' => array('class' => 'gfield')))
    );
    public $defaultGroupDecorators = array(
        'Elements',
        array('HtmlTag', array('tag' => 'ul', 'attribs' => array('class' => 'gform_fields left_label description_below'))),
        array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'gform_body'))),
        array('Description', array('tag' => 'p')),
    );
    public $submitGroupDecorators = array(
        'Elements',
        'Description',
        array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'gform_footer left_label'))),
    );
    public $decorators = array(
        'Elements',
        'Groups',
        'Form',
        array('HtmlTag', array('tag' => 'div', 'attribs' => array('class' => 'gform_wrapper')))
    );
}
