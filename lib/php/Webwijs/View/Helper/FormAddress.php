<?php

namespace Webwijs\View\Helper;

class FormAddress extends FormElement
{
    public function formAddress($name, $value, $attribs = array(), $options = array())
    {
        $attribs['type'] = 'hidden';
        $attribs['value'] = $this->escape($value);
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';

        return '<div class="address-container">
                  <input placeholder="Voer een adres in..." type="text" class="regular-text geocoding" data-target="'.$attribs['name'].'" autocomplete="off"/>
                  <br/><span class="status"><p></p></span>
                  <span class="coordinates"><strong>Huidige coordinaten:</strong>'.$attribs['value'].'</span>
                  <div class="geocoding-results widefat"></div>
                </div>
                <input '.$this->_renderAttribs($attribs).'  />';
    }
}
