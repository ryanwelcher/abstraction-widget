<?php

/**
 * Abstracted version of WP_Widget
 */
class Abstraction_Widget extends WP_Widget {

	/**
	 * List of form elements to render
	 *
	 * @since 4.4
	 * @access public
	 * @var array
	 */
	public $form_elements = array();


	/**
	 * Root ID for all widgets of this type.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var mixed|string
	 */
	public $id_base;

	/**
	 * Name for this widget type.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Option array passed to {@see wp_register_sidebar_widget()}.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var array
	 */
	public $widget_options;

	/**
	 * Option array passed to {@see wp_register_widget_control()}.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var array
	 */
	public $control_options;

	/**
	 * Unique ID number of the current instance.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool|int
	 */
	public $number = false;

	/**
	 * Unique ID string of the current instance (id_base-number).
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool|string
	 */
	public $id = false;

	/**
	 * Whether the widget data has been updated.
	 *
	 * Set to true when the data is updated after a POST submit - ensures it does
	 * not happen twice.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool
	 */
	public $updated = false;

	// Member functions that you must over-ride.

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
		//verify that we should be showing the widget
		if ( ! $this->verify_settings( $instance ) ) {
			return;
		}

		$this->before_widget( $args, $instance );
		$this->widget_markup( $args, $instance );
		$this->after_widget( $args, $instance );
	}

	/**
	 * Verify settings
	 *
	 * Used to determine whether we should render the widget or not. Props @jdgrimes {link} https://core.trac.wordpress.org/ticket/32470#comment:15
	 * @param array $instance
	 * @return bool
	 */
	public function verify_settings( $instance ) {
		return true;
	}


	/**
	 * Default treatment of before widget markup
	 *
	 * @param $args
	 * @param $instance
	 */
	public function before_widget( $args, $instance ) {

		$title = $this->widget_title( $instance );

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
	public function widget_markup( $args, $instance ) {}

	/**
	 * Default treatment of after widget markup
	 * @param $args
	 * @param $instance
	 */
	public function after_widget( $args, $instance ) {
		echo $args['after_widget'];
	}

	/**
	 * Return the title of the widget
	 *
	 * @param $instance
	 *
	 * @return mixed|void
	 */
	public function widget_title( $instance ) {
		return  apply_filters( 'widget_title', ! isset($instance['title']  ) || empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
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

	// Functions you'll need to call.


	function widget_setup(){

		return false;
	}

	/**
	 * PHP5 constructor.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
	 *                                a portion of the widget's class name will be used Has to be unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See {@see wp_register_sidebar_widget()} for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See {@see wp_register_widget_control()}
	 *                                for information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base = '', $name = '', $widget_options = array(), $control_options = array() ) {

		//call the setup method
		if ( $setup = $this->widget_setup() ) {
			$setup = wp_parse_args( $this->widget_setup(), array( 'id_base' => '', 'name' => '', 'widget_options' => array(), 'control_options' => array() ) );
		}

		$id_base = ( $setup ) ? $setup['id_base'] : $id_base;
		$name    = ( $setup ) ? $setup['name'] : $name;
		$widget_options = ( $setup ) ? $setup['widget_options'] : $widget_options;
		$control_options = ( $setup ) ? $setup['name'] : $control_options;


		$this->id_base = empty( $id_base ) ? preg_replace( '/(wp_)?widget_/', '', strtolower( get_class( $this ) ) ) : strtolower( $id_base );
		$this->name = $name;
		$this->option_name = 'widget_' . $this->id_base;
		$this->widget_options = wp_parse_args( $widget_options, array( 'classname' => $this->option_name ) );
		$this->control_options = wp_parse_args( $control_options, array( 'id_base' => $this->id_base ) );
	}

	/**
	 * PHP4 constructor
	 *
	 * @param string $id_base
	 * @param string $name
	 * @param array  $widget_options
	 * @param array  $control_options
	 */
	public function WP_Widget( $id_base = '', $name = '', $widget_options = array(), $control_options = array() ) {
		WP_Widget::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Constructs name attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create name attributes for fields to be saved by update()
	 *
	 * @param string $field_name Field name
	 * @return string Name attribute for $field_name
	 */
	public function get_field_name($field_name) {
		return 'widget-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
	}

	/**
	 * Constructs id attributes for use in {@see WP_Widget::form()} fields.
	 *
	 * This function should be used in form() methods to create id attributes
	 * for fields to be saved by {@see WP_Widget::update()}.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param string $field_name Field name.
	 * @return string ID attribute for `$field_name`.
	 */
	public function get_field_id( $field_name ) {
		return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}

	/**
	 * Register all widget instances of this widget class.
	 *
	 * @since 2.8.0
	 * @access private
	 */
	public function _register() {
		$settings = $this->get_settings();
		$empty = true;

		if ( is_array($settings) ) {
			foreach ( array_keys($settings) as $number ) {
				if ( is_numeric($number) ) {
					$this->_set($number);
					$this->_register_one($number);
					$empty = false;
				}
			}
		}

		if ( $empty ) {
			// If there are none, we register the widget's existence with a
			// generic template
			$this->_set(1);
			$this->_register_one();
		}
	}

	/**
	 * Set the internal order number for the widget instance.
	 *
	 * @since 2.8.0
	 * @access private
	 *
	 * @param int $number The unique order number of this widget instance compared to other
	 *                    instances of the same class.
	 */
	public function _set($number) {
		$this->number = $number;
		$this->id = $this->id_base . '-' . $number;
	}

	public function _get_display_callback() {
		return array($this, 'display_callback');
	}

	public function _get_update_callback() {
		return array($this, 'update_callback');
	}

	public function _get_form_callback() {
		return array($this, 'form_callback');
	}

	/**
	 * Determine whether the current request is inside the Customizer preview.
	 *
	 * If true -- the current request is inside the Customizer preview, then
	 * the object cache gets suspended and widgets should check this to decide
	 * whether they should store anything persistently to the object cache,
	 * to transients, or anywhere else.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @return bool True if within the Customizer preview, false if not.
	 */
	public function is_preview() {
		global $wp_customize;
		return ( isset( $wp_customize ) && $wp_customize->is_preview() ) ;
	}

	/**
	 * Generate the actual widget content (Do NOT override).
	 *
	 * Finds the instance and calls {@see WP_Widget::widget()}.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array     $args        Display arguments. See {@see WP_Widget::widget()} for information
	 *                               on accepted arguments.
	 * @param int|array $widget_args {
	 *     Optional. Internal order number of the widget instance, or array of multi-widget arguments.
	 *     Default 1.
	 *
	 *     @type int $number Number increment used for multiples of the same widget.
	 * }
	 */
	public function display_callback( $args, $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$this->_set( $widget_args['number'] );
		$instance = $this->get_settings();

		if ( array_key_exists( $this->number, $instance ) ) {
			$instance = $instance[$this->number];

			/**
			 * Filter the settings for a particular widget instance.
			 *
			 * Returning false will effectively short-circuit display of the widget.
			 *
			 * @since 2.8.0
			 *
			 * @param array     $instance The current widget instance's settings.
			 * @param WP_Widget $this     The current widget instance.
			 * @param array     $args     An array of default widget arguments.
			 */
			$instance = apply_filters( 'widget_display_callback', $instance, $this, $args );

			if ( false === $instance ) {
				return;
			}

			$was_cache_addition_suspended = wp_suspend_cache_addition();
			if ( $this->is_preview() && ! $was_cache_addition_suspended ) {
				wp_suspend_cache_addition( true );
			}

			$this->widget( $args, $instance );

			if ( $this->is_preview() ) {
				wp_suspend_cache_addition( $was_cache_addition_suspended );
			}
		}
	}

	/**
	 * Deal with changed settings (Do NOT override).
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param int $deprecated Not used.
	 */
	public function update_callback( $deprecated = 1 ) {
		global $wp_registered_widgets;

		$all_instances = $this->get_settings();

		// We need to update the data
		if ( $this->updated )
			return;

		if ( isset($_POST['delete_widget']) && $_POST['delete_widget'] ) {
			// Delete the settings for this instance of the widget
			if ( isset($_POST['the-widget-id']) )
				$del_id = $_POST['the-widget-id'];
			else
				return;

			if ( isset($wp_registered_widgets[$del_id]['params'][0]['number']) ) {
				$number = $wp_registered_widgets[$del_id]['params'][0]['number'];

				if ( $this->id_base . '-' . $number == $del_id )
					unset($all_instances[$number]);
			}
		} else {
			if ( isset($_POST['widget-' . $this->id_base]) && is_array($_POST['widget-' . $this->id_base]) ) {
				$settings = $_POST['widget-' . $this->id_base];
			} elseif ( isset($_POST['id_base']) && $_POST['id_base'] == $this->id_base ) {
				$num = $_POST['multi_number'] ? (int) $_POST['multi_number'] : (int) $_POST['widget_number'];
				$settings = array( $num => array() );
			} else {
				return;
			}

			foreach ( $settings as $number => $new_instance ) {
				$new_instance = stripslashes_deep($new_instance);
				$this->_set($number);

				$old_instance = isset($all_instances[$number]) ? $all_instances[$number] : array();

				$was_cache_addition_suspended = wp_suspend_cache_addition();
				if ( $this->is_preview() && ! $was_cache_addition_suspended ) {
					wp_suspend_cache_addition( true );
				}

				$instance = $this->update( $new_instance, $old_instance );

				if ( $this->is_preview() ) {
					wp_suspend_cache_addition( $was_cache_addition_suspended );
				}

				/**
				 * Filter a widget's settings before saving.
				 *
				 * Returning false will effectively short-circuit the widget's ability
				 * to update settings.
				 *
				 * @since 2.8.0
				 *
				 * @param array     $instance     The current widget instance's settings.
				 * @param array     $new_instance Array of new widget settings.
				 * @param array     $old_instance Array of old widget settings.
				 * @param WP_Widget $this         The current widget instance.
				 */
				$instance = apply_filters( 'widget_update_callback', $instance, $new_instance, $old_instance, $this );
				if ( false !== $instance ) {
					$all_instances[$number] = $instance;
				}

				break; // run only once
			}
		}

		$this->save_settings($all_instances);
		$this->updated = true;
	}

	/**
	 * Generate the widget control form (Do NOT override).
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param int|array $widget_args Widget instance number or array of widget arguments.
	 */
	public function form_callback( $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$all_instances = $this->get_settings();

		if ( -1 == $widget_args['number'] ) {
			// We echo out a form where 'number' can be set later
			$this->_set('__i__');
			$instance = array();
		} else {
			$this->_set($widget_args['number']);
			$instance = $all_instances[ $widget_args['number'] ];
		}

		/**
		 * Filter the widget instance's settings before displaying the control form.
		 *
		 * Returning false effectively short-circuits display of the control form.
		 *
		 * @since 2.8.0
		 *
		 * @param array     $instance The current widget instance's settings.
		 * @param WP_Widget $this     The current widget instance.
		 */
		$instance = apply_filters( 'widget_form_callback', $instance, $this );

		$return = null;
		if ( false !== $instance ) {
			$return = $this->form($instance);

			/**
			 * Fires at the end of the widget control form.
			 *
			 * Use this hook to add extra fields to the widget form. The hook
			 * is only fired if the value passed to the 'widget_form_callback'
			 * hook is not false.
			 *
			 * Note: If the widget has no form, the text echoed from the default
			 * form method can be hidden using CSS.
			 *
			 * @since 2.8.0
			 *
			 * @param WP_Widget $this     The widget instance, passed by reference.
			 * @param null      $return   Return null if new fields are added.
			 * @param array     $instance An array of the widget's settings.
			 */
			do_action_ref_array( 'in_widget_form', array( &$this, &$return, $instance ) );
		}
		return $return;
	}

	/**
	 * Register an instance of the widget class.
	 *
	 * @since 2.8.0
	 * @access private
	 *
	 * @param integer $number Optional. The unique order number of this widget instance
	 *                        compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		wp_register_sidebar_widget(	$this->id, $this->name,	$this->_get_display_callback(), $this->widget_options, array( 'number' => $number ) );
		_register_widget_update_callback( $this->id_base, $this->_get_update_callback(), $this->control_options, array( 'number' => -1 ) );
		_register_widget_form_callback(	$this->id, $this->name,	$this->_get_form_callback(), $this->control_options, array( 'number' => $number ) );
	}

	/**
	 * Save the settings for all instances of the widget class.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $settings Multi-dimensional array of widget instance settings.
	 */
	public function save_settings( $settings ) {
		$settings['_multiwidget'] = 1;
		update_option( $this->option_name, $settings );
	}

	/**
	 * Get the settings for all instances of the widget class.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @return array Multi-dimensional array of widget instance settings.
	 */
	public function get_settings() {

		$settings = get_option($this->option_name);

		if ( false === $settings && isset($this->alt_option_name) )
			$settings = get_option($this->alt_option_name);

		if ( !is_array($settings) )
			$settings = array();

		if ( !empty($settings) && !array_key_exists('_multiwidget', $settings) ) {
			// old format, convert if single widget
			$settings = wp_convert_widget_settings($this->id_base, $this->option_name, $settings);
		}

		unset($settings['_multiwidget'], $settings['__i__']);
		return $settings;
	}
}