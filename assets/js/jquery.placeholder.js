jQuery(function($) {
    /**
     * Returns the Internet Explorer version currently used by the client,
     * or -1 if the client is using a different browser.
     *
     * @return int the Internet Explorer version.
     */
    function getInternetExplorerVersion() {
        var ieVersion = -1;
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null) {
                ieVersion = parseFloat( RegExp.$1 );
            }
        }
        return ieVersion;
    }

    if(!Modernizr.input.placeholder || (getInternetExplorerVersion() > -1)) {
        $('[placeholder]').focus(function() {
          var $input = $(this);
          if ($input.val() == $input.attr('placeholder')) {
            $input.val('');
            $input.removeClass('placeholder');
          }
        }).blur(function() {
          var $input = $(this);
          if ($input.val() == '' || $input.val() == $input.attr('placeholder')) {
            $input.addClass('placeholder');
            $input.val($input.attr('placeholder'));
          }
        }).blur();
    }
});
