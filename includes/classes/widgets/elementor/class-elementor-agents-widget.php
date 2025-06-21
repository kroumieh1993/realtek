<?php

use Elementor\Controls_Manager;

/**
 * Class Elementor_Es_Agents_Widget.
 */
class Elementor_Es_Agents_Widget extends Elementor_Es_Base_Widget {

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
		return 'es-agents-widget';
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
		return _x( 'Estatik Agents', 'widget name', 'es' );
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
		return 'es-icon es-icon_glasses';
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
		$shortcode = es_get_shortcode_instance( 'es_my_listing' );
		$attributes = $shortcode->get_attributes();

		$this->start_controls_section(
			'es_section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'page_title', array(
			'label' => __( 'Title', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attributes['page_title'],
		) );

		$this->add_custom_control( 'layout', array(
			'label' => __( 'Layout', 'es' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'list' => _x( 'List', 'listings layout name', 'es' ),
				'grid' => _x( 'Grid', 'listings layout name', 'es' ),
			),
			'default' => $attributes['layout']
		) );

		$this->add_custom_control( 'disable_pagination', array(
			'label' => __( 'Disable pagination', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['disable_pagination'] ? 'yes' : $attributes['disable_pagination'],
		) );

		$this->add_custom_control( 'view_all_link_name', array(
			'label' => __( 'View all link name', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attributes['view_all_link_name'],
		) );

		$this->add_custom_control( 'view_all_page_id', array(
			'label' => __( 'View all page', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'options' => es_get_pages(),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_listings_navbar', array( 'label' => _x( 'Navbar', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'disable_navbar', array(
			'label' => __( 'Disable navbar', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['disable_navbar'] ? 'yes' : $attributes['disable_navbar']
		) );

		$this->add_custom_control( 'show_sort', array(
			'label' => __( 'Show sorting', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_sort'] ? 'yes' : $attributes['show_sort'],
		) );

		$this->add_custom_control( 'show_page_title', array(
			'label' => __( 'Show page title', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_page_title'] ? 'yes' : $attributes['show_page_title'],
		) );

		$this->add_custom_control( 'show_total', array(
			'label' => __( 'Show total', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_total'] ? 'yes' : $attributes['show_total'],
		) );

		$this->add_custom_control( 'show_layouts', array(
			'label' => __( 'Show layouts', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_layouts'] ? 'yes' : $attributes['show_layouts'],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_search_navbar', array( 'label' => _x( 'Search navbar', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'enable_search', array(
			'label' => __( 'Enable search bar', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['enable_search'] ? 'yes' : $attributes['enable_search'],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content', array( 'label' => _x( 'Query filter', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'posts_per_page', array(
			'label' => __( 'Agents per page', 'es' ),
			'type' => Controls_Manager::NUMBER,
			'default' => $attributes['posts_per_page']
		) );

		$this->add_custom_control( 'sort', array(
			'label' => __( 'Default sort', 'es' ),
			'type' => Controls_Manager::SELECT,
			'default' => $attributes['sort'],
			'options' => ests_selected( 'agents_sorting_options' )
		) );

		$this->add_custom_control( 'agents_id', array(
			'label' => __( 'Agents IDs', 'es' ),
			'type' => 'text',
			'description' => __( 'Comma separated agents IDs', 'es' ),
		) );

		$this->add_custom_control( 'agency_id', array(
			'label' => __( 'Agency', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'options' => es_get_agencies_list(),
		) );

		$this->add_custom_control( 'es_service_area', array(
			'label' => __( 'Service areas', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => es_get_terms_list( 'es_service_area' ),
		) );

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
		$settings = $this->get_settings();
//		$settings = wp_array_slice_assoc( $settings, $this->get_custom_controls_keys() );

		$shortcode = es_get_shortcode_instance( 'es_my_agents', $settings );
		echo $shortcode->get_content();
	}
}
