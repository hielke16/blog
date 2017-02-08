(function($, window, document, undefined) {
    $('#widgets-right').on('change', '.url-container input[type=radio]', function(e) {
        var $container = $(this).closest('.url-container').find('.content-container');
        if ($container.length) {
            var $fields = $container.find('.url-field');
            var $active = $fields.filter('[data-url-type="'+ $(this).val() +'"]');

            if ($active.length === 0) {
                $container.slideUp(400, function() {
                    $fields.addClass('hidden');
                });
            } else if ($container.is(':visible')) {
                $container.slideUp(400, function() {
                    $fields.addClass('hidden');
                    
                    $active.removeClass('hidden');
                    $container.slideDown(400);
                });
            } else {
                $active.removeClass('hidden');
                $container.slideDown(400);
            }
        }
    });
})(jQuery, this, this.document);
