<?php
/**
 * Plugin Name: Abstraction Widget
 */


require_once(  __DIR__ . '/classes/class-abstraction-widget.php' );
require_once(  __DIR__ . '/classes/class-abstracted-widget.php' );
require_once( __DIR__ . '/classes/abstracted-default-widgets.php' );
//individual widgets
require_once( __DIR__ . '/classes/default-widgets/class-wp-widget-pages.php' );


/**
 * Don't load the default widgets
 */
add_filter( 'load_default_widgets', '__return_false' );


add_action( 'plugins_loaded', 'add_widgets' );

function add_widgets() {
	add_action( '_admin_menu', 'wp_widgets_add_menu' );
}

add_action( 'widgets_init', function(){

	register_widget( 'My_Widget' );

});
