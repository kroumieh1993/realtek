<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Locations_Grid_Widget extends Elementor_Es_Base_Widget {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'es-locations-grid-widget';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return _x( 'Estatik Locations', 'widget name', 'es' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'es-icon es-icon_locations';
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		/** @var Es_Request_Form_Shortcode $shortcode */
		$shortcode = es_get_shortcode_instance( 'es_locations_grid' );
		$attr = $shortcode->get_attributes();

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_custom_control( 'enable_slider', array(
			'label' => __( 'Enable slider if more than 4 locations', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['enable_slider'] ? 'yes' : $attr['enable_slider'],
		) );

		$this->add_custom_control( 'enable_counter', array(
			'label' => __( 'Show subtitle with quantity of listings', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['enable_counter'] ? 'yes' : $attr['enable_counter'],
		) );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image', [
				'label' => __( 'Image', 'es' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'location_id', [
				'label' => __( 'Location', 'es' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => get_terms( array( 'taxonomy' => 'es_location', 'fields' => 'id=>name', 'hide_empty' => false ) )
			]
		);

		$this->add_custom_control(
			'items',
			[
				'label' => __( 'Items', 'es' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array $instance
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
//		$settings = wp_array_slice_assoc( $settings, $this->get_custom_controls_keys() );

		if ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $key => $item ) {
				$item['image'] = ! empty( $item['image']['id'] ) ? $item['image']['id'] : null;
				$settings['items'][ $key ] = $item;
			}
		}
		$shortcode = es_get_shortcode_instance( 'es_locations_grid', $settings );
		echo $shortcode->get_content();
	}
}