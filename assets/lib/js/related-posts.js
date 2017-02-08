jQuery(document).ready(function($) {
	var containers = $(".related-posts-wrapper");
    
    $.each(containers, function(key, element){
        
        var list = $(element).find("ol");
        var button = $(element).find(".button");
        var name = button.attr('name').split("-button").join("");
        var select = $(element).find("select");

        list.sortable();
        list.disableSelection();
        
        list.find("li").each(function() { extendItem($(this)); });        
        
        button.click(function() {
            console.log($("option:selected", select).length);
            $("option:selected", select).each(function () {
                var option = $(this);

                var item = $("<li></li>")
                    .appendTo(list);

                var input = $("<input>")
                    .attr("type", "checkbox")
                    .attr("checked", true)
                    .attr("name", name + "[]")
                    .attr("value", option.val())
                    .appendTo(item);

                var span = $("<span></span>")
                    .text(option.text())
                    .appendTo(item.append(" "));

                extendItem(item);
            });
        });
    });	

	function extendItem(item) {
		var a = $("<a></a>").addClass("item-delete submitdelete deletion").text('verwijderen');
	
		a.click(function() {
			item.remove();
		});

		a.appendTo(item.append(" "));
	};	
});
