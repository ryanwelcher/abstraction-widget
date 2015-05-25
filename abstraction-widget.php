<?php
/**
 * Plugin Name: Abstraction Widget
 */

class Abstraction_Widget extends WP_Widget {

	/**
	 * @var array Form elements
	 */
	public $form_elements = array(
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
		),
		array(
			'type' => 'textarea',
			'label' => 'My Bio',
			'name' => 'bio',
		),
	);


	/**
	 * Standard constructor process for Widgets
	 */
	function __construct() {
		$widget_ops = array(
			'classname' => 'abstraction_widget',
			'description' => esc_html__( 'A Widget for testing Abstraction ideas' ) );
		parent::__construct( 'abstraction-widget','Abstraction Widget', $widget_ops );
	}


	/**
	 * Echo the widget content.
	 *
	 * Subclasses should over-ride this function to generate their widget code.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including before_title, after_title,
	 *                        before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		$this->before_widget( $args, $instance );
		$this->widget_markup();
		$this->after_widget( $args, $instance );
	}


	/**
	 * Default treatment of before widget markup
	 *
	 * @param $args
	 * @param $instance
	 */
	public function before_widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', ! isset($instance['title']  ) || empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the custom markup content for the widget
	 *
	 * Subclasses will override this to create their output
	 */
	public function widget_markup() {}

	/**
	 * Default treatment of after widget markup
	 * @param $args
	 * @param $instance
	 */
	public function after_widget( $args, $instance ) {
		echo $args['after_widget'];
	}


	/**
	 * Output the settings update form.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 * @return string Default return is 'noform'.
	 */
	public function form($instance) {
		$this->_generate_form_elements( $instance );
	}


	/**
	 * Helper method to generate the markup for the widget form
	 * @param $instance
	 */
	protected function _generate_form_elements( $instance, $tag = 'p' ) {

		foreach ( $this->form_elements as $element ) {
			$el = '<'.$tag.'>';
			$el .= $this->generate_label( $element );
			$el .= $this->generate_form_field( $element, $instance );
			$el .= '</'.$tag.'>';
			echo $el;
		}
	}

	/**
	 * Generate the label
	 *
	 * @param $element
	 *
	 * @return string
	 */
	public function generate_label( $element ) {

		$defaults = array(
			'label' => 'Unlabelled ' . ucfirst( $element['type'] ) . ' Input',
			'id'    => 'unlabelled-' . $element['type'],
		);

		$details = wp_parse_args( $element, $defaults );
		ob_start();
		?>
		<label for="<?php echo $this->get_field_id( $details['name'] ); ?>"><?php echo esc_html( $details['label'] ); ?></label>
		<?php
		return ob_get_clean();
	}


	/**
	 * Generate the form field
	 *
	 * @param $element
	 * @param $instance
	 *
	 * @return string
	 */
	public function generate_form_field( $element, $instance ) {

		//process generic items here
		$classes = ( isset( $element['classes'] ) && ! empty( $element['classes'] ) ) ? $element['classes'] : '';

		ob_start();
		switch ( $element['type'] ) {
			case 'text':
				$value = isset( $instance[ $element['name'] ] ) ? $instance[ $element['name'] ] : '';
				?>
				<input type="text"
				       class="widefat <?php echo esc_attr( $classes );?>"
				       id="<?php echo esc_attr( $this->get_field_id( $element['name'] ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( $element['name'] ) ); ?>"
				       value="<?php echo esc_attr( $value ) ?>" />
				<?php
				break;
			case 'textarea':
				$value = isset( $instance[ $element['name'] ] ) ? $instance[ $element['name'] ] : '';
				?><br/>
				<textarea
					class="widefat <?php echo esc_attr( $classes );?>"
					rows="16"
					cols="20"
					id="<?php echo esc_attr( $this->get_field_id( $element['name'] ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( $element['name'] ) ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php
				break;
			case 'checkbox':
				$value = isset( $instance[ $element['name'] ] ) ? (bool) $instance[ $element['name'] ] : false;
				?>
				<input type="checkbox"
				       class="checkbox <?php echo esc_attr( $classes );?>"
				       id="<?php echo esc_attr( $this->get_field_id( $element['name'] ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( $element['name'] ) ); ?>"
						<?php checked( $value ); ?> />

				<?php
				break;
		}

		return ob_get_clean();
	}
}



class My_Widget extends Abstraction_Widget {

	/**
	 * The Form elements for this widget
	 * @var array
	 */
	public $form_elements = array(
		array(
			'type' => 'text',
			'label'    => 'Name',
			'name'  => 'title',
		),
	);

	/**
	 * Standard constructor process for Widgets
	 */
	function __construct() {
		$widget_ops = array(
			'classname' => 'my_widget',
			'description' => esc_html__( 'An Instance my new widget' ) );
		parent::__construct( 'my-new-widget', esc_html__( 'WIDGET!' ), $widget_ops );
	}


	function widget_markup() {
		echo 'adfasdfadsf';
	}

}

add_action('widgets_init', function() {
	//register_widget( 'Abstraction_Widget' );
	register_widget( 'My_Widget' );
});