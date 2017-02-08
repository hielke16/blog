<?php

namespace Webwijs\Admin\Filter;

class UserInterface
{
    public static function enableDatepicker()
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('input.datepicker').datepicker({ dateFormat: 'yy-mm-dd', monthNames: ['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'], dayNamesMin: ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'] });
        });
        </script>
        <?php
    }
    
    public static function enableTabs()
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $( "#tabs" ).tabs();
        });
        </script>
        <?php
    }
}
