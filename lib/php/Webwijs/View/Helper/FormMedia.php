<?php

namespace Webwijs\View\Helper;

class FormMedia extends FormElement
{
    public function formMedia($name, $value, $attribs = array(), $options = array())
    {
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');

        $attribs['class'] = 'regular-text';
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';

        $output = $this->view->formHidden($name, $value, $attribs, $options);

        $preview = '';
        if ($value) {
            $preview = wp_get_attachment_image($value, 'thumbnail', true);
        }

        $thumbnailSize = $this->getThumbnailSize();
        ob_start();
        ?>
        <button type="button" id="<?php echo $attribs['id'] ?>-button"><span>Bladeren</span></button>

        <div id="<?php echo $attribs['id'] ?>-preview">
            <?php echo $preview ?>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#<?php echo $attribs['id'] ?>-button').click(function() {
                window.send_to_editor = function(html) {
                    var matches = html.match(/wp-image-(\d+)/);
                    if (matches) {
                        $('#<?php echo $attribs['id'] ?>').val(matches[1]);
                        var imgurl = $('img',html).attr('src');
                        $('#<?php echo $attribs['id'] ?>-preview').html($('<img>').attr({'src': imgurl }).css({'maxWidth': '<?php echo $thumbnailSize['width']?>px', 'maxHeight': '<?php echo $thumbnailSize['height']?>px' }));
                    }
                    tb_remove();
                }
                tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
                return false;
            });
        });
        </script>
        <?php
        $output .= ob_get_clean();
        return $output;
    }
    protected function getThumbnailSize()
    {
        global $_wp_additional_image_sizes;
        $sizeData = array();
        $sizeData['width'] = isset($_wp_additional_image_sizes['thumbnail']['width'])
                           ? intval($_wp_additional_image_sizes['thumbnail']['width'])
                           : get_option('thumbnail_size_w');
        $sizeData['height'] = isset($_wp_additional_image_sizes['thumbnail']['height'])
                           ? intval($_wp_additional_image_sizes['thumbnail']['height'])
                           : get_option('thumbnail_size_h');
        return $sizeData;
    }
}
