<?php
/**
 * Plugin Name: Abstraction Widget
 */


require_once(  __DIR__ . '/classes/class-abstraction-widget.php' );
require_once(  __DIR__ . '/classes/class-abstracted-widget.php' );


add_action('widgets_init', function() {
	register_widget( 'My_Widget' );
});