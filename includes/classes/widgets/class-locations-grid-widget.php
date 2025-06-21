<?php

/**
 * Class Es_Locations_Grid_Widget.
 */
class Es_Locations_Grid_Widget extends Es_Properties_Filter_Widget {

	/**
	 * Es_Widget_Example constructor.
	 */
	public function __construct() {
		parent::__construct( 'es-locations-grid', _x( 'Estatik Locations', 'widget name', 'es' ) );
	}

	/**
	 * Render widget on frontend.
	 *
	 * @param $instance
	 *
	 * @return void
	 */
	public function render( $instance = array() ) {
		$shortcode = es_get_shortcode_instance( 'es_locations_grid', $instance );
		echo $shortcode->get_content();
	}

	/**
	 * Return default widget data.
	 *
	 * @return array
	 */
	public function get_default_data() {
		$shortcode = es_get_shortcode_instance( 'es_locations_grid' );
		return array_merge( parent::get_default_data(), $shortcode->get_default_attributes() );
	}

	/**
	 * Return widget fields array.
	 *
	 * @param $instance
	 * @return array
	 */
	public function get_widget_form( $instance ) {
		$filter_form = parent::get_widget_form( $instance );
		unset( $filter_form['es_type']['after'], $filter_form['post__in'], $filter_form['post__in'], $filter_form['show_properties_by'], $filter_form['posts_per_page'] );

		$fields = array_merge( array(
			'title' => array(
				'label' => __( 'Title' ),
				'type'  => 'text',
			),
		), $this->get_page_access_fields(), $filter_form, array(
			'enable_counter' => array(
				'type' => 'switcher',
				'label' => __( 'Show subtitle with quantity of listings', 'es' ),
			),
			'enable_slider' => array(
				'type' => 'switcher',
				'label' => __( 'Enable slider if more than 4 locations', 'es' ),
			),
			'items' => array(
				'type' => 'repeater',
				'add_button_label' => __( 'Add one more location', 'es' ),
				'fields' => array(
					'image' => array(
						'label' => __( 'Location {#index}', 'es' ),
						'type' => 'images',
						'enable_caption' => false,
						'button_label' => __( 'Upload photo', 'es' ),
					),
					'location_id' => array(
						'type' => 'select',
						'label' => __( 'Enter location', 'es' ),
						'ajax_term_id_field' => true,
						'attributes' => array(
							'class' => 'js-es-select2-search',
							'data-request' => es_esc_json_attr( array(
								'action' => 'get_select2_locations',
								'_ajax_nonce' => wp_create_nonce( 'get_select2_locations' )
							) ),
							'data-placeholder' => __( 'Start type location', 'es' ),
							'data-formatNoMatches' => __( 'Location not found', 'es' ),
						),
					),
				),
			),
		) );

		return $fields;
	}
}

new Es_Locations_Grid_Widget();
