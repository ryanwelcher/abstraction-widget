<?php

class My_Widget extends Abstraction_Widget {

	/**
	 * The Form elements for this widget
	 * @var array
	 */
	public $form_elements = array(
		array(
			'type' => 'text',
			'label'    => 'Name',
			'name'  => 'name',
		),
		array(
			'type' => 'text',
			'label' => 'Title',
			'class' => 'title',
			'name'  => 'title',
		),
		array(
			'type' => 'checkbox',
			'label' => 'Remember this status?',
			'name'  => 'remember',
			'label_after' => true,
		),
		array(
			'type' => 'textarea',
			'label' => 'My Bio',
			'name' => 'bio',
		),
	);

	/**
	 * Setup the widget
	 *
	 * @return array
	 */
	function widget_setup() {
		return array(
			'id_base'           => 'my-new-widget',
			'name'              => esc_html__( 'WIDGET!' ),
			'widget_options'    => array(
				'classname' => 'my-widget',
				'description' => esc_html__( 'An Instance my new widget 2' )
			),
			'control_options'   => array(),
		);
	}

	/**
	 * Generate the markup for this widget
	 *
	 * @param $args
	 * @param $instance
	 */
	function widget_markup( $args, $instance ) {
		echo '<p>';
		echo 'The title is : ' . esc_html( $this->widget_title( $instance ) );
		echo '</p>';
		echo '<p>';
		if ( isset(  $instance['remember'] ) ) {
			echo 'I\'m remembering this setting!';
		} else {
			echo 'What setting?';
		}
		echo '</p>';
		echo '<p>';
		if ( isset( $instance['bio'] ) && ! empty(  $instance['bio'] ) ) {
			echo $instance['bio'];
		} else {
			echo get_bloginfo( 'title' );
		}
		echo '</p>';
	}
}
