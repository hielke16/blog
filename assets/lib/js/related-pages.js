jQuery(document).ready(function($) {
	var element = $("#webwijs-page-teasers");

	var list = element.find("ol");
	var button = element.find("input");
	var select = element.find("select");

    list.sortable();
	list.disableSelection();

	function extendItem(item) {
		var a = $("<a></a>").addClass("item-delete submitdelete deletion").text('verwijderen');
	
		a.click(function() {
			item.remove();
		});

		a.appendTo(item.append(" "));
	};

	list.find("li").each(function() { extendItem($(this)); });

	button.click(function() {
		$("option:selected", select).each(function () {
			var option = $(this);
			
			var item = $("<li></li>")
				.appendTo(list);

			var input = $("<input>")
				.attr("type", "checkbox")
				.attr("checked", true)
				.attr("name", "webwijs-related-pages[]")
				.attr("value", option.val())
				.appendTo(item);

			var span = $("<span></span>")
				.text(option.text())
				.appendTo(item.append(" "));

			extendItem(item);
		});
	});
});
