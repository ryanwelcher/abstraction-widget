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
	 * Build the form element
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

		return ( bool ) $this->_out;
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

	/**
	 * @param array $instance
	 */
	public function form2( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'exclude' => '') );
		$title = esc_attr( $instance['title'] );
		$exclude = esc_attr( $instance['exclude'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
	<?php
	}

}