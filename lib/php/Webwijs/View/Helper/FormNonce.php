<?php

namespace Webwijs\View\Helper;

class FormNonce extends FormElement
{
    public function formNonce($name, $value, $attribs, $options)
    {
        return wp_nonce_field($options['nonce'], '_wpnonce', true, false);
    }
}
