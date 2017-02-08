<?php

namespace Webwijs\View\Helper;

class FormCompile extends FormElement
{
    public function formCompile($name, $value, $attribs, $options)
    {
        $value = get_bloginfo('stylesheet_directory') . '/app/Admin/Ajax/compile-scss.php';
        return '<button class="button-secondary" type="button" name="compile-scss" id="compile-scss" value="'.$value.'"><span><span>Compileer!</span></span></button>';
    }
}
