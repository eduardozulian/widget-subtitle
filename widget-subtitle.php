<?php
/*
Plugin Name: Widget Subtitle
Plugin URI: http://github.com/eduardozulian/widget-subtitle
Description: Add a subtitle input field to all widgets.
Version: 1.1
Author: Eduardo Zulian
Author URI: http://flutuante.com.br
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: widget-subtitle
Domain Path: /languages
*/

! defined( 'ABSPATH' ) and die();

if ( ! class_exists( 'Widget_Subtitle' ) ) :

final class Widget_Subtitle {

	/**
	 * The single instance of the class.
	 *
	 * @since	1.1
	 * @var		Widget_Subtitle
	 */
	private static $_instance = null;

	/**
	 * Plugin version
	 *
	 * @since	1.1
	 * @var 	string
	 */
	private $version = '1.1';

	/**
	 * Possible locations of the subtitle
	 *
	 * @since	1.1
	 * @var 	string
	 */
	private $locations = array();
	
	/**
	 * PHP5 constructor that calls specific hooks within WordPress
	 *
	 * @since	0.1
	 * @return	void
	 */
	function __construct( ) {
		self::$_instance = $this;

		$this->locations = array(
			'before-outside' => __('Before title', 'widget-subtitle') . ' - ' . __('Outside heading', 'widget-subtitle'), // before title, outside title element
			'before-inside' => __('Before title', 'widget-subtitle') . ' - ' . __('Inside heading', 'widget-subtitle'), // before title, inside title element
			'after-outside' => __('After title', 'widget-subtitle') . ' - ' . __('Outside heading', 'widget-subtitle'), // after title, outside title element
			'after-inside' => __('After title', 'widget-subtitle') . ' - ' . __('Inside heading', 'widget-subtitle'), // after title, inside title element
		);

		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	 * Main Genesis Widget Subtitle.
	 *
	 * Ensures only one instance of Widget Subtitle is loaded or can be loaded.
	 *
	 * @since	1.1
	 * @static
	 * @see		Widget_Subtitle()
	 * @return	Genesis Widget Column Classes - Main instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init function/action and register all used hooks
	 *
	 * @since	1.1
	 * @return	void
	 */
	public function init() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'in_widget_form', array( $this, 'in_widget_form'), 9, 3 ); 
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

		$instance = wp_parse_args( (array) $instance, array( 'widget_subtitle' => '', 'widget_subtitle_location' => '' ) );
		$return = null;
		?>

		<p>
			<label for="<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>"><?php _e( 'Subtitle', 'widget-subtitle' ); ?>:</label> 
			<input class="widefat" id="<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>" name="<?php echo $widget->get_field_name( 'widget_subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['widget_subtitle'] ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>"><?php _e('Subtitle location', 'widget-subtitle') ?>:</label> 
			<select name=<?php echo $widget->get_field_name( 'widget_subtitle_location' ); ?>" id="<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>">
			<?php 
				foreach ( $this->locations as $locationKey => $locationName ) {
					?>
					<option value="<?php echo $locationKey ?>" <?php selected( $instance['widget_subtitle_location'], $locationKey, true ) ?>><?php echo $locationName ?></option>
					<?php
				}
			?>
			</select> 
		</p>

        <script type="text/javascript">
			;(function($){ 
				// show/hide subtitla location if no value is 
				if ( '' == $('#<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>').val() ) {
					$('#<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>').parent().hide();
				}
				$(document).on('keyup', '#<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>', function() {
					if ( '' != $(this).val() ) {
						$('#<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>').parent().slideDown('fast');
					} else {
						$('#<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>').parent().slideUp('fast');
					}
				} );
				// Relocate subtitle input after title if available
				if ( $('#<?php echo $widget->get_field_id( 'title' ) ?>').parent('p').length ) {
					$('#<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'title' ) ?>').parent('p') );
					$('#<?php echo $widget->get_field_id( 'widget_subtitle_location' ) ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'widget_subtitle' ) ?>').parent('p') );
				}
			})( jQuery );
		</script>

	<?php
	}

	/**
     * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
     */
	function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {

		// Subtitle
		if ( isset( $new_instance['widget_subtitle'] ) ) {
			$instance['widget_subtitle'] = strip_tags( $new_instance['widget_subtitle'] );
		} else { 
			$instance['widget_subtitle'] = ''; 
		}

		// Subtitle location
		if ( isset( $new_instance['widget_subtitle_location'] ) ) {
			$instance['widget_subtitle_location'] = strip_tags( $new_instance['widget_subtitle_location'] );
		} else { 
			$instance['widget_subtitle_location'] = ''; 
		}

		return $instance;

	}
	
	/**
     * Gets called from within the dynamic_sidebar function which displays a widget container.
     * This filter gets called for each widget instance in the sidebar.
     */
	function dynamic_sidebar_params( $params ) {

		global $wp_registered_widgets;

		if ( ! isset( $params[0]['widget_id'] ) ) {
			return $params;
		}

		$widget_id = $params[0]['widget_id'];
		$widget = $wp_registered_widgets[ $widget_id ];
	
		// Get instance settings
		if ( array_key_exists( 'callback', $widget ) ) {

			$instance = get_option( $widget['callback'][0]->option_name );
		
			// Check if there's an instance of the widget
			if ( array_key_exists( $params[1]['number'], $instance ) ) {

				$instance = $instance[$params[1]['number']];
			
				// Add the subtitle
				if ( ! empty( $instance['widget_subtitle'] ) ) {

					$subtitle_location = 'after-inside'; // default
					// Get location value if it exists and is valid
					if ( ! empty( $instance['widget_subtitle_location'] ) && array_key_exists( $instance['widget_subtitle_location'], $this->locations ) ) {
						$subtitle_location = $instance['widget_subtitle_location'];
					}

					// Filters subtitle element (default: span)
					$subtitle_element = apply_filters( 'widget_subtitle_element', 'span', $widget_id );

					// Create subtitle classes
					$subtitle_classes = array( 'widget-subtitle', 'widgetsubtitle' );
					// Add subtitle location classes
					$subtitle_location_classes = explode( '-', $subtitle_location );
					foreach( $subtitle_location_classes as $location ) {
						$subtitle_classes[] = 'subtitle-' . $location;
					}
					// Allow filter for subtitle classes to overwrite, remove or add classes
					$subtitle_classes = apply_filters( 'widget_subtitle_classes', $subtitle_classes, $widget_id );
					// Create class string to use
					$subtitle_classes = is_array( $subtitle_classes ) ? '' . implode( ' ', $subtitle_classes ) . '' : '';

					// Start the output
					$subtitle = '<' . $subtitle_element . ' class="' . $subtitle_classes . '">';
					$subtitle .= $instance['widget_subtitle'];
					$subtitle .= '</' . $subtitle_element . '>';

					// Assign the output to the correct location in the correct order
					switch ( $subtitle_location ) {

						case 'before-inside':
							$params[0]['before_title'] = $params[0]['before_title'] . $subtitle . ' '; // a space to separate subtitle from title
							break;

						case 'before-outside':
							$params[0]['before_title'] = $subtitle . $params[0]['before_title'];
							break;

						case 'after-inside':
							$params[0]['after_title'] = ' ' . $subtitle . $params[0]['after_title']; // a space to separate subtitle from title
							break;

						case 'after-outside':
							$params[0]['after_title'] = $params[0]['after_title'] . $subtitle;
							break;
					}

				}
			}
		}
		return $params;
	}
	
	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since  1.1
	 * @access public
	 * @return void
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  1.1
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'widget-subtitle' ), null );
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since  1.1
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'widget-subtitle' ), null );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.1
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( get_class( $this ) . "::{$method}", esc_html__( 'Method does not exist.', 'widget-subtitle' ), null );
		unset( $method, $args );
		return null;
	}
	
}

/**
 * Main instance of Widget Subtitle.
 *
 * Returns the main instance of Widget_Subtitle to prevent the need to use globals.
 *
 * @since	0.1
 * @return 	Widget_Subtitle
 */
function Get_Widget_Subtitle() {
	return Widget_Subtitle::get_instance();
}
Get_Widget_Subtitle();

endif; // if ( ! class_exists( 'Widget_Subtitle' ) )
