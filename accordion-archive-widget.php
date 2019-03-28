<?php
/**
 * Plugin Name: Accordion Archive Widget
 * Plugin URI: http://github.com/pathartl/accordion-archive-widget
 * Description: An archive widget that collapses the standard archive widget into an accordion by year
 * Version: 1.0
 * Author: Pat Hartl
 * Author URI: http://pathartl.me
 * License: CC0 1.0
 */

function accordion_archives_js() {
    wp_enqueue_script( 'accordion_archives_script', plugins_url( '/script.js', __FILE__ ), array('jquery') );
}
add_action('wp_enqueue_scripts', 'accordion_archives_js');

function accordion_archives_styles() {
    wp_register_style('accordion_archives', plugins_url( '/style.css', __FILE__ ), array(), '1.0', 'all');
    wp_enqueue_style('accordion_archives'); // Enqueue it!
}

add_action('wp_enqueue_scripts', 'accordion_archives_styles');

/**
 * Accordion Archives widget class
 *
 * @since 2.8.0
 */
class WP_Widget_Accordion_Archives extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_accordion_archive', 'description' => __( 'A yearly archive of your site&#8217;s Posts in an accordion.') );
		parent::__construct('accordion_archives', __('Accordion Archives'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty($instance['title'] ) ? __( 'Accordion Archives' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>
		<ul>
<?php
		/**
		 * Filter the arguments for the Archives widget.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_get_archives()
		 *
		 * @param array $args An array of Archives option arguments.
		 */
		$archives = strip_tags(wp_get_archives( apply_filters( 'widget_accordion_archives_args', array(
			'type'            => 'monthly',
			'format'          => 'custom',
			'echo'            => 0,
			'after'           => ','
		) ) ) );

		$archives = explode(',', $archives);

		$months = array();
		$years = array();

		// Grab our years first
		foreach ($archives as $archive) {
			$archive = explode(' ', $archive);
			if (isset($archive[1])) {
				array_push($years, $archive[1]);
			}
		}

		$years = array_values(array_unique($years));

		$i = 0;
		foreach ($years as $year) {

		?><li class="archive-accordion-year"><a><?php

			echo $year;

			?></a><ul><?php

			foreach ($archives as $archive) {

				$archive = explode(' ', $archive);
				if (!empty($archive[1]) && $archive[1] == $year) {
					echo '<li class="archive-accordion-month"><a href="' . 
					// Get the archive link
					get_month_link($year, date("m", $archive[0] . '-' . $year)) . 
					'">' . trim($archive[0]) . '</a></li>';
				}

			}

			?></ul><?php

		?></li><?php

		}

?>
		</ul>
<?php

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '') );
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
}

function register_accordion_archive_widget() {
    register_widget( 'WP_Widget_Accordion_Archives' );
}
add_action( 'widgets_init', 'register_accordion_archive_widget' );
