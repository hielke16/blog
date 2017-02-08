(function($) {
    jQuery.validator.addMethod("postcode", function(value, element) {
      return this.optional(element) || /^[0-9]{4} ?[a-z]{2}$/i.test(jQuery.trim(value));
    }, "This is not a valid postcode");

    jQuery.validator.addMethod("phone", function(value, element) {
      return this.optional(element) || /^[0-9][^a-z]+$/.test(jQuery.trim(value));
    }, "This is not a valid phonenumber");

    jQuery.validator.addMethod("dateselect", function(value, element) {
        return /^[0-9]+$/.test(jQuery.trim(value));
    }, "Select a date");
    jQuery.validator.addMethod("timeselect", function(value, element) {
        return /^[0-9]+$/.test(jQuery.trim(value));
    }, "Select a time");
})(jQuery);
