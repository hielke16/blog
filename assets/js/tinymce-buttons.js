(function() {
    tinymce.create('tinymce.plugins.custom_buttons', {
        init : function(ed, url) {

        },
        createControl : function(n, cm) {
            var t = this;
            if (n == 'custom_buttons') {

                var c = cm.createListBox('shortcodes', {title : 'Shortcodes', onselect : function(v) {
                    cm.editor.selection.setContent('[' + v + ']' + cm.editor.selection.getContent() + '[/' + v + ']');
                    return false;
                }});
                if (c) {
                    c.add('Eén derde kolom', 'one_third');
                    c.add('Lees meer kolom', 'read_more');
                }
                return c;
            }
        },
    });
    tinymce.PluginManager.add('custom_buttons', tinymce.plugins.custom_buttons);
})();
