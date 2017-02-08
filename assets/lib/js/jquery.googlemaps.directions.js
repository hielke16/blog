(function($){
    var defaults = {};

    var GMapsDirections = function(options) {
        var opts, map, company_address;
        var directionsArray = [];
        var directionsService = new google.maps.DirectionsService();
        var geocoder = new google.maps.Geocoder();
        var mapOptions = {
            zoom: 16,
            scrollwheel: false,
            center: new google.maps.LatLng(52.9573424, 5.9348538),
            mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        /*
         * Constructor where default values are set and other methods are called from.
         * If the autoSlideshow is set to 'true' we start the carousel.
         */
        var init = function(options) {
            // combine default settings with given options.
            opts = $.extend({}, defaults, options);

            // Converting address into location.
            geocoder.geocode( { 'address': opts.address}, function(results, status) {
                // Create new Google Map.
                map = new google.maps.Map(document.getElementById('map'), mapOptions);

                if (status == google.maps.GeocoderStatus.OK) {
                    // Store this location
                    company_address = results[0].geometry.location;
                    // Position map to center marker.
                    map.setCenter(company_address);
                    // Create new marker.
                    var marker = new google.maps.Marker({
                        map: map,
                        title: opts.name,
                        position: company_address,
                        icon: opts.icon
                    });
                }
            });
        };

        var calcDirections = function(origin, destination) {
            var rendererOptions = {
                map: map
            }
            var directionsRequest = {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING
            };

            var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
            directionsService.route(directionsRequest, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    // Hide searchform.
                    $('#directions-service').slideUp('slow');
                    // Show reset button.
                    $('.reset').fadeIn('slow');

                    // Use DirectionsRenderer() to draw route on the map.
                    directionsDisplay.setDirections(response);
                    // Show directions in directions panel
                    directionsDisplay.setPanel(document.getElementById("directions-panel"));
                }
            });

            // Add DirectionsRenderer() to directionsArray.
            directionsArray.push(directionsDisplay)
        }

        var resetDirections = function() {
            // Remove everything related to DirectionsRenderer.
            for(i = 0; i < directionsArray.length; i++) {
                directionsArray[i].setMap(null);
                directionsArray[i].setPanel(null);
            }
        }

        /*
         * onform submit
         */
        $('#directionsform').submit(function() {
            // Get the given address from the form.
            var street = $(this).find('#address:eq(0)').val();
            // Get the given place from the form.
            var place = $(this).find('#place:eq(0)').val();

            // Combine street with place.
            var address = street + ',' + place;

            // Calculate directions
            calcDirections(address, company_address);

            // Reset entire form
            $(this).each (function() {
                this.reset();
            });


            // prevent default action
            return false;
        });

        $('.reset a').click(function() {
            // show searchform
            $('#directions-service').fadeIn();
            // hide reset button.
            $('.reset').hide();

            // Reset map and directions panel.
            resetDirections();

            // prevent default action
            return false;
        })

        // Explicitely call the constructor.
        init(options);
    }

    $.fn.GMapsDirections = function(options) {
        this.each(function () {
            GMapsDirections(options);
        });
    }
})(jQuery)

