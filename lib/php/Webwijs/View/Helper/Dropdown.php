<?php

namespace Webwijs\View\Helper;

class Dropdown
{
    /**
     * Returns a select element containing all options provided to this helper.
     *
     * @param string $name the name of select element.
     * @param array $args optional arguments to create the select element.
     * @return string an html select element.
     */
    public function dropdown($name, $args = null)
    {
        $defaults = array(
            'class' => '',
            'selected' => null,
            'show_option_none' => __('--Selecteer'),
            'options' => array(),
        );
        $args = array_merge($defaults, (array) $args);
        
        $options = (is_string($args['options'])) ? $this->toArray($args['options']) : (array) $args['options'];

        ob_start();
    ?>
        <?php if (is_array($options)): ?>
        <select name="<?php echo $name ?>" class="<?php echo $args['class'] ?>">
            <?php if (is_string($args['show_option_none']) && strlen($args['show_option_none']) > 0): ?>
                <?php echo $this->renderOptions(array('' => $args['show_option_none']), $args) ?>
            <?php endif ?>
            <?php echo $this->renderOptions($options, $args) ?>
        </select>
        <?php endif ?>
    <?php
        return ob_get_clean();
    }
    
    /**
     * Renders the given array of options into a string of option tags.
     *
     * @param array|\Traversable a collection containing key-value pairs to render.
     * @param array $args optional arguments to render the given options.
     * @return string a string containing zero or more 'option' tags.
     * @see Dropdown::dropdown($name, $args)
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/option
     */
    private function renderOptions($options, array $args)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        
        $strings = array();
        foreach ($options as $value => $label) {
            if (is_array($label)) {
                $strings[] = $this->renderOptgroup($value, $label, $args);
                continue;
            } 
            
            $attribs = array();
            if (isset($args['selected']) && ($value == $args['selected'])) {
                $attribs['selected'] = 'selected';
            }
            
            if (is_scalar($value)) {
                $attribs['value'] = $value;
            }          
            
            $strings[] = sprintf('<option %s>%s</option>', $this->renderAttributes($attribs), $label);
        }
        return implode(' ', $strings);
    }
    
    /**
     * Renders the given array of options into an optgroup tag containing zero or more option tags.
     *
     * @param string $label a label for the option-group to render.
     * @param array|\Traversable a collection containing key-value pairs to render.
     * @param array $args optional arguments to render the given options.
     * @return string a string containing an 'optgroup' tag which can consist of
     *                zero or more 'option' tags.
     * @see Dropdown::renderOptions($options, $args)
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/optgroup
     */
    private function renderOptgroup($label, $options, array $args)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
    
        return sprintf('<optgroup label="%s">%s</optgroup>', $label, $this->renderOptions($options, $args));
    }
    
    /**
     * Renders the given array of attributes into an string of attributes.
     *
     * @param array $attribs a collection containing key-value pairs to render.
     * @return string a string consisting of white-space separated attributes.
     */
    private function renderAttributes(array $attribs)
    {
        $strings = array();
        foreach ($attribs as $key => $value) {
            if ($key = strtolower($key)) {
                $strings[] = sprintf('%s = %s', esc_attr($key), esc_attr($value));
            }
        }
        return implode(' ', $strings);
    }
    
    /**
     * Returns an array with options from the given string.
     *
     * Multiple options in the string should be delimited by commas, and
     * each option can consist of a key-value pair which is denoted by an 
     * equals sign (e.g. opt1=val1,opt2=val2,opt3=val3).
     *
     * @param string $str A string containing one or more options.
     * @return array returns an array with options, or an empty array.
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @see Dropdown::convertOptions($str
     */
    private function toArray($str)
    {
        if (!is_string($str)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        }
                
        // convert string to array.
        $options = array();	    
        if ($values = explode(',', $str)) {
            foreach ($values as $value) {
                $parts = explode('=', trim($value));
                if (is_array($parts) && count($parts) > 1) {
                    $options[$parts[0]] = $parts[1];
                } else {
                    $options[$parts[0]] = $parts[0];
                }
            }
        }
        return $options;
    }
}
