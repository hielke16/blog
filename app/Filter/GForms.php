<?php

namespace Theme\Filter;

use RGFormsModel;
use GFCommon;

class GForms
{  

    /**
     * Append a Css class to a field if this field is rendered without a label.
     *
     * @param string $classes the CSS classes to be filtered, separated by empty spaces.
     * @param object $field the current field.
     * @param object $form the current form object being displayed.
     * @return string the CSS classes for the current field.
     */
    public static function fieldClasses($classes, $field, $form)
    {
        if(!empty($field['disableLabel'])) {
            $classes = sprintf("%s gfield_label_free", $classes);
        }
        return $classes;
    }

    /**
     * Removes the label from the field if it's label is disabled.
     *
     * @param string content the field content to be filtered.
     * @param object $field the current field.
     * @param object $form the curent form object being displayed.
     * @param int $lead_id when executed from the entry detail screen, $lead_id will be populated with the Entry ID. Otherwise, it will be 0.
     * @param int $form_id the current form id.
     * @return string the fields content.
     */
    public static function fieldContent($content, $field, $value, $lead_id, $form_id)
    {
        if(!is_admin() && !empty($field['disableLabel'])) {
            $content = preg_replace('#<label [^>]+>(.*?)</label>#s', '', $content, 1);
        }

        return $content;
    }
    
    /**
     * Create a new field setting under the standard tab.
     *
     * @param int $position specify the position that the setting should be displayed.
     * @param int $form_id the current form id.
     */
    public static function fieldSettings($position, $form_id)
    {
        if ($position == 0) {
    ?>
        <li class="disable_label_setting field_setting">
            <input type="checkbox" name="field_disable_label" id="field_disable_label" onclick="SetDisableLabel(this.checked);"/>
            <label for="field_disable_label" class="inline">
                Veld label verbergen
            </label>
        </li>  
    <?php
        }
        
        if ($position == 50) {
    ?>
        <li class="placeholder_setting field_setting">
            <label for="field_placeholder">
                Placeholder
                <?php gform_tooltip("form_field_placeholder") ?>
            </label>
            <input type="text" id="field_placeholder" class="fieldwidth-3" onkeyup="SetFieldProperty('placeholder', this.value)" size="35" />
        </li>
    <?php
        }
    }

    /**
     * Add new or modify existing form settings that display on the Form Settings screen.
     *
     * @param array $settings an array of settings for the Form Settings UI.
     * @param object $form the current form object being displayed.
     * @return array returns the settings array with new settings appended to it.
     */
    public static function formSettings($settings, $form)
    {
        $description = rgar($form, 'footerDescription');
        $footerChecked = (rgar($form, 'hideRequiredMessage')) ? 'checked="checked"' : '';
        $settings['Footer instellingen']['footer_required_message'] = '
        <tr>
            <th>Meldingen verbergen' . gform_tooltip("footer_required_message", "", true) . '</th>
            <td>
                <input type="checkbox" id="footer_required_message" name="footer_required_message" value="1" ' . $footerChecked . '/>
                <label for="footer_required_message">Verberg de standaard <strong><em>* is verplicht</em></strong> melding.</label>
            </td>
        </tr>';
        $settings['Footer instellingen']['footer_description'] = '
        <tr>
            <th>Footer beschrijving' . gform_tooltip("footer_description", "", true) . '</th>
            <td>
                <textarea id="footer_description" name="footer_description" class="fieldwidth-3 fieldheight-2">' . $description . '</textarea>
            </td>
        </tr>
        ';
        return $settings;
    }
    
    /**
     * Modify the form object before saving on the Form Settings page.
     *
     * @param object $form the current form object being displayed.
     * @return object the current form object with new fields and values appended to it.
     */
    public static function formSave($form)
    {
        $form['hideRequiredMessage'] = rgpost('footer_required_message');
        $form['footerDescription'] = rgpost('footer_description');
        return $form;
    }

    /**
     * Create or edit tooltips using this method.
     *
     * Creates a new tooltip for the textarea. Tooltips usually contain information that describe the functionality of a specific field.
     *
     * @param array $tooltips associative array with the existing tooltips.
     * @return array associative array containing all the tooltips for this form.
     * @link http://www.gravityhelp.com/documentation/page/Gform_tooltips
     */
    public static function setTooltips($tooltips)
    {
        $tooltips['footer_description'] = "<h6>Kanttekening</h6> Je kunt dit gebruiken om extra tekst onderaan het formulier te tonen.";
        $tooltips['footer_required_message'] = "<h6>Verberg melding</h6> Je kunt dit gebruiken om de tekst <strong>* is verplicht</strong> te verbergen.";
        $tooltips['form_field_placeholder'] = "<h6>Placeholder</h6> Een placeholder wordt in een veld getoond en zal verdwijnen zodra er tekst in het veld geplaatst word.";
        
        return $tooltips;
    }
    
    /**
     * Injects JavaScript into the form editor page.
     */
    public static function editorScript()
    {
    ?>
        <script type='text/javascript'>
            /*
             * iterate over the field settings and add
             * the new field to all of them.
             */
            for (var name in fieldSettings) {
                var field = '.disable_label_setting, .placeholder_setting';
                if(fieldSettings[name].length > 0) {
                    field = ', '.concat(field);
                }
                
                // add field to all settings.
                fieldSettings[name] += field;
            }

            //binding to the load field settings event to initialize the checkbox
            jQuery(document).bind('gform_load_field_settings', function(event, field, form){
                // disable label field.
                var isChecked = (field['disableLabel'] == true);
                jQuery('#field_disable_label').attr('checked', isChecked);
                if(isChecked) {
                    jQuery('.label_setting').hide();
                }
                
                // disable placeholder field.
                jQuery("#field_placeholder").val(field['placeholder']); 
            });
            
            function SetDisableLabel(isChecked){
                SetFieldProperty('disableLabel', isChecked);
                if(isChecked) {
                    jQuery('.label_setting').slideUp();
                } else {
                    jQuery('.label_setting').slideDown();
                }
            }
        </script>
    <?php
    }
    
    /**
     * Show footer description right next to the forms submit button.
     *
     * Change the data below the submit button using the textarea created in the 'formSettings()' method.
     * All newlines will be replaced by HTML line breaks.
     *
     * Prolong the execution of this filter by setting the priority to '100' or higher. We need to be sure that the text
     * entered by the user is the last thing added to the footer.
     *
     * @param $button the string containing the <input> tag to be filtered.
     * @param $form the current form.
     * @link http://www.gravityhelp.com/documentation/page/Gform_submit_button
     */
    public static function addFooterDescription($button, $form) {
        $footer = $button;
        if(isset($form['footerDescription']) && strlen($form['footerDescription']) > 0) {
            $footer .= '<span class="gform_footer_description">' . nl2br(strip_tags($form['footerDescription'])) .'</span>';
        }
        return $footer;
    }

    public static function submitButton($button_input, $form)
    {
        if (preg_match('/<input[^>]*value=\'([^\']*)\'/', $button_input, $match)) {
            $button_input = preg_replace('/<input/', '<button', $button_input);
            $button_input = preg_replace('/\/>/', '>', $button_input);
            $button_input .= '<span><span>' . $match[1] . '</span></span></button>';
        }
        $required_message = null;
        if (isset($form['hideRequiredMessage']) && $form['hideRequiredMessage'] != '1') {
            $required_message .= '<div class="required_message">' . __('* is verplicht') . '</div>';
        }
        if(!GFCommon::has_pages($form)) {
            $button_input .= $required_message;
            return $button_input;
        }
    }
    public static function spinnerUrl($loader)
    {
        return get_bloginfo('stylesheet_directory') . '/assets/images/loader.gif';
    }

    public static function confirmation($confirmation, $form = null, $lead = null, $ajax = null)
    {
        if (!empty($confirmation)) {
            $page = rtrim(get_permalink(), '/') . '-voltooid/';
            $extra = <<<HTML
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_trackPageview', '{$page}']);
</script>
HTML;
            $confirmation = str_replace('</div>', $extra . '</div>', $confirmation);
        }
        return $confirmation;
    }
    public static function placeholder ($field_content, $field, $value, $lead_id, $form_id)
    {
        $placeholder = rgget('placeholder', $field);
        if (!empty($placeholder)) {
            if ($value == $placeholder) {
                $value = '';
            }
            $id = $field["id"];
            $field_id = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";
            $form_id = IS_ADMIN && empty($form_id) ? rgget("id") : $form_id;

            $size = rgar($field, "size");
            $disabled_text = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
            $class_suffix = RG_CURRENT_VIEW == "entry" ? "_admin" : "";
            $class = $size . $class_suffix;
            $max_length = "";
            $html5_attributes = "";
            switch(RGFormsModel::get_input_type($field)){

                case "email":
                    if(!empty($post_link))
                        return $post_link;

                    $html_input_type = RGFormsModel::is_html5_enabled() ? "email" : "text";

                    if(IS_ADMIN && RG_CURRENT_VIEW != "entry"){
                        $single_style = rgget("emailConfirmEnabled", $field) ? "style='display:none;'" : "";
                        $confirm_style = rgget("emailConfirmEnabled", $field) ? "" : "style='display:none;'";
                        return "<div class='ginput_container ginput_single_email' {$single_style}><input name='input_{$id}' type='{$html_input_type}' class='" . esc_attr($class) . "' disabled='disabled' /></div><div class='ginput_complex ginput_container ginput_confirm_email' {$confirm_style} id='{$field_id}_container'><span id='{$field_id}_1_container' class='ginput_left'><input type='text' name='input_{$id}' id='{$field_id}' disabled='disabled' /><label for='{$field_id}'>" . apply_filters("gform_email_{$form_id}", apply_filters("gform_email",__("Enter Email", "gravityforms"), $form_id), $form_id) . "</label></span><span id='{$field_id}_2_container' class='ginput_right'><input type='text' name='input_{$id}_2' id='{$field_id}_2' disabled='disabled' /><label for='{$field_id}_2'>" . apply_filters("gform_email_confirm_{$form_id}", apply_filters("gform_email_confirm",__("Confirm Email", "gravityforms"), $form_id), $form_id) . "</label></span></div>";
                    }
                    else{
                        if(rgget("emailConfirmEnabled", $field) && RG_CURRENT_VIEW != "entry"){
                            $first_tabindex = GFCommon::get_tabindex();
                            $last_tabindex = GFCommon::get_tabindex();
                            return "<div class='ginput_complex ginput_container' id='{$field_id}_container'><span id='{$field_id}_1_container' class='ginput_left'><input type='{$html_input_type}' name='input_{$id}' id='{$field_id}' value='" . esc_attr($value) . "' {$first_tabindex} {$disabled_text}/><label for='{$field_id}'>" . apply_filters("gform_email_{$form_id}", apply_filters("gform_email",__("Enter Email", "gravityforms"), $form_id), $form_id) . "</label></span><span id='{$field_id}_2_container' class='ginput_right'><input type='{$html_input_type}' name='input_{$id}_2' id='{$field_id}_2' value='" . esc_attr(rgpost("input_" . $id ."_2")) . "' {$last_tabindex} {$disabled_text}/><label for='{$field_id}_2'>" . apply_filters("gform_email_confirm_{$form_id}", apply_filters("gform_email_confirm",__("Confirm Email", "gravityforms"), $form_id), $form_id) . "</label></span></div>";
                        }
                        else{
                            $tabindex = GFCommon::get_tabindex();
                            return sprintf("<div class='ginput_container'><input name='input_%d' id='%s' type='%s' value='%s' class='%s' $max_length $tabindex $html5_attributes %s placeholder='%s'/></div>", $id, $field_id, $html_input_type, esc_attr($value), esc_attr($class), $disabled_text, esc_attr($placeholder));
                        }
                    }

                break;
                case "text":
                    if(empty($html_input_type))
                        $html_input_type = "text";

                    if(rgget("enablePasswordInput", $field) && RG_CURRENT_VIEW != "entry")
                        $html_input_type = "password";

                    if(is_numeric(rgget("maxLength", $field)))
                        $max_length = "maxlength='{$field["maxLength"]}'";

                    if(!empty($post_link))
                        return $post_link;

                    $tabindex = GFCommon::get_tabindex();
                    return sprintf("<div class='ginput_container'><input name='input_%d' id='%s' type='%s' value='%s' class='%s' $max_length $tabindex $html5_attributes %s placeholder='%s'/></div>", $id, $field_id, $html_input_type, esc_attr($value), esc_attr($class), $disabled_text, esc_attr($placeholder));
                break;
                case "phone" :
                    if(!empty($post_link))
                        return $post_link;

                    $instruction = $field["phoneFormat"] == "standard" ? __("Phone format:", "gravityforms") . " (###)###-####" : "";
                    $instruction_div = rgget("failed_validation", $field) && !empty($instruction) ? "<div class='instruction validation_message'>$instruction</div>" : "";
                    $html_input_type = RGFormsModel::is_html5_enabled() ? "tel" : "text";

                    $tabindex = GFCommon::get_tabindex();
                    return sprintf("<div class='ginput_container'><input name='input_%d' id='%s' type='{$html_input_type}' value='%s' class='%s' $tabindex %s placeholder='%s'/>$instruction_div</div>", $id, $field_id, esc_attr($value), esc_attr($class), $disabled_text, esc_attr($placeholder));
                break;
                case "textarea":
                    $max_chars = "";
                    if(!IS_ADMIN && !empty($field["maxLength"]) && is_numeric($field["maxLength"]))
                        $max_chars = GFCommon::get_counter_script($form_id, $field_id, $field["maxLength"]);

                    $tabindex = GFCommon::get_tabindex();
                    return sprintf("<div class='ginput_container'><textarea name='input_%d' id='%s' class='textarea %s' $tabindex %s rows='10' cols='50' placeholder='%s'>%s</textarea></div>{$max_chars}", $id, $field_id, esc_attr($class), $disabled_text, esc_attr($placeholder), esc_html($value));
            }
        }
        return $field_content;
    }

}
