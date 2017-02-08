<?php

namespace Webwijs_Admin_Filter;

class Datepicker
{
    public static function enableDatepicker()
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('input.datepicker').datepicker({ dateFormat: 'yy-mm-dd', monthNames: ['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'], dayNamesMin: ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'] });
        });
        </script>
        <?php
    }
}
