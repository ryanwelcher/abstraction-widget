<?php
/**
 * Pages widget class
 *
 * @since 2.8.0
 */
class WP_Widget_Pages extends Abstraction_Widget {


	/**
	 * Holds the output of the wp_list_pages call.
	 * @var bool
	 */
	private $_out = false;


	/**
	 * Define the form elements
	 * @var array
	 */
	public $form_elements = array(
		array(
			'type' => 'text',
			'label' => 'Title',
			'name'  => 'title',
		),
		array(
			'type' => 'text',
			'label' => 'Exclude',
			'name'  => 'exclude',
		),
		array(
			'type' => 'select',
			'label' => 'Sort By:',
			'name' => 'sortby',
			'options' => array(
				'post_title' => 'Page Title',
				'menu_order' => 'Page Order',
				'ID' => 'Page ID',
			)
		),
	);


	/**
	 * Widget setup.
	 *
	 * This is meant to replace the convoluted __construct method.
	 *
	 * @return array
	 */
	public function widget_setup() {
		return array(
			'id_base'           => 'pages',
			'name'              => esc_html__( 'Pages' ),
			'widget_options'    => array(
				'classname' => 'widget_pages',
				'description' => esc_html__( 'A list of your site&#8217;s Pages.' )
			),
		);
	}

	/**
	 * Lets be sure we're supposed to show the widget
	 * @param array $instance
	 *
	 * @return bool
	 */
	function verify_settings( $instance ) {

		$sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];

		if ( $sortby == 'menu_order' )
			$sortby = 'menu_order, post_title';

		/**
		 * Filter the arguments for the Pages widget.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_list_pages()
		 *
		 * @param array $args An array of arguments to retrieve the pages list.
		 */
		$this->_out = wp_list_pages( apply_filters( 'widget_pages_args', array(
			'title_li'    => '',
			'echo'        => 0,
			'sort_column' => $sortby,
			'exclude'     => $exclude,
		) ) );

		return (bool) $this->_out;
	}


	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget_markup( $args, $instance ) {
		?>
		<ul>
			<?php echo $this->_out; ?></ul>
		<?php
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}

		$instance['exclude'] = strip_tags( $new_instance['exclude'] );

		return $instance;
	}
}