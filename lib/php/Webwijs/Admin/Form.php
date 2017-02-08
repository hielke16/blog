<?php

namespace Webwijs\Admin;

use Webwijs\Form as DefaultForm;

class Form extends DefaultForm
{
    public $defaultElementDecorators = array(
        'ViewHelper',
        array('Description', array('tag' => 'span')),
        array('Errors', array('attribs' => array('style' => 'color: #990000', 'class' => 'error'))),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'th', 'attribs' => array('scope' => 'row'))),
        array('HtmlTag', array('tag' => 'tr'))
    );
    public $defaultGroupDecorators = array(
        'Elements',
        array('HtmlTag', array('tag' => 'tbody')),
        array('HtmlTag', array('tag' => 'table', 'attribs' => array('class' => 'form-table'))),
        array('Description', array('tag' => 'p')),
    );
}
