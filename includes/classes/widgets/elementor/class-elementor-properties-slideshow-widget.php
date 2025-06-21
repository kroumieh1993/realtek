<?php

use Elementor\Controls_Manager;

/**
 * Class Elementor_Es_Properties_Slideshow_Widget.
 */
class Elementor_Es_Properties_Slideshow_Widget extends Elementor_Es_Query_Widget {

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
		return 'es-properties-slideshow-widget';
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
		return _x( 'Estatik Slideshow', 'widget name', 'es' );
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
		return 'es-icon es-icon_slider';
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
		/** @var Es_Properties_Slideshow_Shortcode $shortcode */
		$shortcode = es_get_shortcode_instance( 'es_properties_slideshow' );
		$attr = $shortcode->get_attributes();

		$this->start_controls_section(
			'section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'layout', array(
			'label' => __( 'Layout', 'es' ),
			'type' => Controls_Manager::SELECT,
			'default' => $attr['layout'],
			'options' => $shortcode::get_layouts()
		) );

		$this->add_custom_control( 'side', array(
			'label' => __( 'Info \ Pager block side', 'es' ),
			'type' => Controls_Manager::SELECT,
			'default' => $attr['side'],
			'options' => array(
				'left' => _x( 'Left', 'Properties slider info block side', 'es' ),
				'right' => _x( 'Right', 'Properties slider info block side', 'es' ),
			)
		) );

		$this->add_custom_control( 'info_background', array(
			'label' => __( 'Info block background color', 'es' ),
			'type' => Controls_Manager::COLOR,
			'default' => $attr['info_background']
		) );

		$this->add_custom_control( 'max_height', array(
			'label' => __( 'Main image max height', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attr['max_height']
		) );

		$this->add_custom_control( 'enable_wishlist', array(
			'label' => __( 'Enable wishlist', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['enable_wishlist'] ? 'yes' : $attr['enable_wishlist'],
		) );

		$this->add_custom_control( 'fields_to_show', array(
			'label' => __( 'Visible fields', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'default' => $attr['fields_to_show'],
			'options' => array(
				'title' => __( 'Property title', 'es' ),
				'es_label' => __( 'Labels', 'es' ),
				'address' => __( 'Address', 'es' ),
				'price' => __( 'Price', 'es' ),
				'price_note' => __( 'Price note' ),
				'es_type' => __( 'Type', 'es' ),
				'es_category' => __( 'Category', 'es' ),
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_slider_settings', array( 'label' => _x( 'Slider settings', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'autoplay', array(
			'label' => __( 'Autoplay', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['autoplay'] ? 'yes' : $attr['autoplay'],
		) );

		$this->add_custom_control( 'is_arrows_enabled', array(
			'label' => __( 'Enable arrows', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['is_arrows_enabled'] ? 'yes' : $attr['is_arrows_enabled'],
		) );

		$this->end_controls_section();

		$this->query_register_controls();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
//		$settings = wp_array_slice_assoc( $settings, $this->get_custom_controls_keys() );
		$shortcode = es_get_shortcode_instance( 'es_properties_slideshow', $settings );
		echo $shortcode->get_content();
	}
}