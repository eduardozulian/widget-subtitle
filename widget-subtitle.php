<?php
/*
Plugin Name: Widget Subtitle
Plugin URI: http://github.com/eduardozulian/widget-subtitle
Description: Add a subtitle input field to all widgets.
Version: 1.0
Author: Eduardo Zulian
Author URI: http://flutuante.com.br
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: widget-subtitle
Domain Path: /languages
*/
 
if ( ! class_exists( 'Widget_Subtitle' ) ) :

class Widget_Subtitle {
	
	/**
	 * PHP5 constructor that calls specific hooks within WordPress
	 */
	function __construct( ) {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'in_widget_form', array( $this, 'in_widget_form'), 10, 3 ); 
		add_filter( 'widget_update_callback', array( $this, 'widget_update_callback' ), 10, 4 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) );
	}

	/**
	 * Load the plugin's translated strings
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'widget-subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

	/**
     * Add a subtitle input field into the form
     */
	function in_widget_form( $widget, $return, $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'widget_subtitle' => '' ) );
		$return = null;
		?>

		<p>
			<label><?php _e( 'Subtitle:', 'widget-subtitle' ); ?></label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>" name="<?php echo $widget->get_field_name( 'widget_subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['widget_subtitle'] ) ); ?>"/>
		</p>

	<?php
	}

	/**
     * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
     */
	function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {

		$instance['widget_subtitle'] = $new_instance['widget_subtitle'];
		return $instance;

	}
	
	/**
     * Gets called from within the dynamic_sidebar function which displays a widget container.
     * This filter gets called for each widget instance in the sidebar.
     */
	function dynamic_sidebar_params( $params ) {

		global $wp_registered_sidebars, $wp_registered_widgets;

		$widget_id = $params[0]['widget_id'];
		$widget = $wp_registered_widgets[$widget_id];
	
		// Get instance settings
		if ( array_key_exists( 'callback', $widget ) ) {

			$instance = get_option( $widget['callback'][0]->option_name );
		
			// Check if there's an instance of the widget
			if ( array_key_exists( $params[1]['number'], $instance ) ) {

				$instance = $instance[$params[1]['number']];
			
				// Add the subtitle at the end of 'after_title' param
				if ( ! empty( $instance['widget_subtitle'] ) ) {
					$subtitle = '<span class="widget-subtitle">'. $instance['widget_subtitle']. '</span>';
					$params[0]['after_title'] = $params[0]['after_title'] . $subtitle;
				}

			}

		}
			
		return $params;

	}

}

endif; // if ( ! class_exists( 'Widget_Subtitle' ) )

$widget_subtitle = new Widget_Subtitle();