(function($, window, document, undefined) {
    $('#widgets-right').on('change', '.widget-image-dropdown-field', function(e) {
        var $select = $(this);
        var $parent = $select.parents('.widget-content');

        var $container = $parent.find('.image-container').addClass('loading');
        if (0 === $container.length) {
            $container = $(document.createElement('div')).prependTo($parent).addClass('image-container loading');
        }
        
        if (typeof ajaxurl === 'string' && ajaxurl.length) {
            var data = {
                action: 'get_attachment_url',
                attachment_id: $select.val()
            };
            
            $.post(ajaxurl, data, function(src) {
                if (typeof src === 'string' && src.length) {
                    $(document.createElement('img')).attr("src", src).appendTo($container.empty());
                } else {
                    $container.remove();
                }
            }).done(function() {
                $container.removeClass('loading'); 
            });
        }
    });
})(jQuery, this, this.document);
