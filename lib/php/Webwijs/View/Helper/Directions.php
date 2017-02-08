<?php

namespace Webwijs\View\Helper;

use ReflectionClass;

class Directions
{
	public function __construct()
	{
		// Get all methods for this object using the ReflectionClass
		$reflection = new ReflectionClass($this);
		$methods = $reflection->getMethods();

		// Iterate over methods and invoke method starting with '_init'.
		foreach($methods as $method) {
			if (strpos($method->name, '_init') === 0) {
				$method->setAccessible(true);
				$method->invoke($this);
            }
		}
	}

    public function directions($atts=array())
    {
        $defaults = array(
            'address' => get_option('theme_company_address'),
            'name' => get_option('blogname'),
            //'icon' => get_bloginfo('stylesheet_directory') . '/assets/images/marker.png',
            'width' => '100%',
            'height' => '300px'
        );
        $atts = $this->atts = wp_parse_args($atts, $defaults);
		$this->render($atts['address'], $atts['name'], @$atts['icon']);
    }

    private function render($address, $blogname, $icon)
    {
    ?>
        <div class="directions-container">
		<div id="map" class="map" style="width: <?php echo $this->atts['width']?>; height: <?php echo $this->atts['height']?>"></div>
        <div id="directions" class="directions">
            <div id="directions-service">
                <div class="header">
                <p>
                    <strong><?php echo __('Plan uw route') ?></strong>
                    <br />
                    <?php echo __('Ik vertrek vanaf') ?>
                </p>
                </div>
                <form method="post" action="<?php echo get_permalink() ?>" id="directionsform">
                    <dl>
                        <dt><label for="address">Adres</label></dt>
                        <dd><input type="text" id="address" name="address" class="regular-text" size="40" /></dd>
                        <dt><label for="place">Plaatsnaam</label></dt>
                        <dd><input type="text" id="place" name="place" class="regular-text" size="40"/></dd>
                        <dt></dt>
                        <dd><button type="submit"><span><span>Routebeschrijving</span></span></button></dd>
                    </dl>
                </form>
            </div>
            <div class="reset" style="display: none"><h3>Routebeschrijving</h3><a href="#" title="<?php echo __('Nieuwe route berekenen') ?>"><?php echo __('Bereken een nieuwe route') ?></a></div>
            <div id="directions-panel"></div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#map').GMapsDirections({address: '<?php echo $address ?>', name: '<?php echo $blogname ?>', icon: '<?php echo $icon ?>'});
            });
        </script>
        </div>
    <?
    }

	private function _initAssets()
	{
		wp_enqueue_script('googleapis', 'http://maps.googleapis.com/maps/api/js?sensor=false&language=nl');
		wp_enqueue_script('jquery.googlemaps', get_bloginfo('stylesheet_directory') . '/assets/lib/js/jquery.googlemaps.directions.js', array('jquery', 'googleapis'));
        //wp_enqueue_style('googlemaps', get_bloginfo('stylesheet_directory') . '/assets/lib/css/googlemaps.css', array('main'));
	}
}
