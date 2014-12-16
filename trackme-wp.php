<?php 
/**
* Plugin Name: trackme-wp
* Plugin URI: https://github.com/actiumind/trackme-wp
* Description: A brief description of the plugin.
* Version: The plugin's version number. Example: 1.0.0
Description: Widget to display your latest Foursquare check-in on Google Maps.
Author: Fredy Alvarado, Jr.
Version: 1.0
*/

class my_foursquare_map extends WP_Widget {

	// constructor
	function my_foursquare_map() {
		parent::WP_Widget(false, $name = __('My FourSquare Map', 'my_foursquare_map') );
	}

	// widget form creation
	function form($instance) {	
		// Check values
		if( $instance) {
			 $rss_link = esc_attr($instance['rss_link']);
		} else {
			 $rss_link = '';
		}
?>
		<p>
        <label for="<?php echo $this->get_field_id('rss_link'); ?>"><?php _e('RSS Link', 'wp_widget_plugin'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('rss_link'); ?>" name="<?php echo $this->get_field_name('rss_link'); ?>" type="text" value="<?php echo $rss_link; ?>" />
        </p>
<?php
	}

	// widget update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['rss_link'] = strip_tags($new_instance['rss_link']);
		return $instance;
	}

	// widget display
	function widget($args, $instance) {
		extract( $args );
	   	
		$xml = apply_filters('rss_link', $instance['rss_link']);
		$xml = simplexml_load_file($xml);  
		$loc = $xml->channel->item;  
		
		foreach ($loc->children('http://www.georss.org/georss') as $geo) {		
?>		
		<div class="widget widget_categories widget-widget_categories">
			<div class=" widget-title">
	            <span class="ohw-name">The Wynn's</span> are currently at:
	        </div>
			<div id="map-canvas" style="width:266px;height:350px;"></div>
		</div>
		
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDur3ot_z9rlOZ-vKtrn3JWx8i7D1TPx_M&sensor=false"></script>
		<script type="text/javascript">
			function initialize() {
				var currentLocation = new google.maps.LatLng(<?php echo str_replace(' ',',',$geo); ?>);
				var mapOptions = {
					center: currentLocation,
					disableDefaultUI: true,
					zoomControl: true,
					mapTypeControl: true,
					zoom: 11,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			
				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				
				var contentString = '<p style="font-size:12px;">Gone With the Wynns</p>';
				
				var infowindow = new google.maps.InfoWindow({
				  content: contentString
				});
			
				var marker = new google.maps.Marker({
				  position: currentLocation,
				  map: map,
				  title:"Gone With The Wynns",
				  animation: google.maps.Animation.DROP
				});
				
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
				});
			}			
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
<?php
			//echo '<iframe width="200" height="200" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$geo.'&amp;aq=&amp;sll='.$geo.'&amp;sspn=0.011899,0.017509&amp;ie=UTF8&amp;ll='.$geo.'&amp;spn=0.011899,0.017509&amp;t=m&amp;z=10&amp;output=embed"></iframe>';
		}	
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("my_foursquare_map");'));


?>