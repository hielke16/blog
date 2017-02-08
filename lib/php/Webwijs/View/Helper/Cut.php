<?php

namespace Webwijs\View\Helper;

class Cut
{
    public $defaults = array(
        'length' => 200,
        'hellip' => '&hellip;',
        'wordcut' => false,
        'escape' => true,
        'nl2br' => false
    );
    function cut($string, $options = null)
    {
        $options = array_merge($this->defaults, (array) $options);
        $string = preg_replace('/<br[^>]*>/', ' ', $string);
        $string = strip_tags($string);
        if (strlen($string) > $options['length']) {
            $string = substr($string, 0, $options['length'] + 1);
            if ($options['wordcut']) {
                if (preg_match('/^(.+)\W/', $string, $matches)) {
                    $string = $matches[1];
                    if (preg_match('/[,"\':\\/]$/', $string)) {
                        $string = substr($string, 0, -1);
                    }
                }
            }
            $string = trim($this->_formatText($string, $options));
            $string .= $options['hellip'];
        }
        elseif ($options['escape']) {
            $string = $this->_formatText($string, $options);
        }
        return $string;
    }
    protected function _formatText($string, $options)
    {
        if ($options['escape']) {
            $string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        }
        if ($options['nl2br']) {
            $string = nl2br($string);
        }
        return $string;
    }
}
