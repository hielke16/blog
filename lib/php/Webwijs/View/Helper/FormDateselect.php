<?php

namespace Webwijs\View\Helper;

class FormDateselect extends FormElement
{
    public function formDateselect($name, $value, $attribs, $options)
    {
        $ranges = array(
            'day' => array('values' => range(1, 31), 'labels' => range(1, 31)),
            'month' => array('values' => range(1, 12), 'labels' => array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December')),
            'year' => array('values' =>  range(date('Y') - 15, date('Y') - 100), 'labels' => range(date('Y') - 15, date('Y') - 100))
        );
        if (isset($attribs['ranges'])) {
            $ranges = array_merge($ranges, $attribs['ranges']);
            unset($attribs['ranges']);
        }
        
        $selects = array();
        $selects['day'] = array_combine(array_merge(array(''), $ranges['day']['values']), array_merge(array('--'), $ranges['day']['labels']));
        $selects['month'] = array_combine(array_merge(array(''), $ranges['month']['values']), array_merge(array('--'), $ranges['month']['labels']));
        $selects['year'] = array_combine(array_merge(array(''), $ranges['year']['values']), array_merge(array('--'), $ranges['year']['labels']));

        $output = '<span id="' . $name . '-input">';
        
        if (!is_array($value)) {
            if (!empty($value)) {
                $value = strtotime($value);
            }
            $value = array('day' => date('d', $value), 'month' => date('m', $value), 'year' => date('Y', $value));
        }
        
        foreach ($selects as $key => $options) {
            $output .= $this->view->formSelect($name . '[' . $key . ']', @$value[$key], array('id' => $name . '-' . $key), $options);
        }
        $output .= '</span>';
        return $output;
    }
}
