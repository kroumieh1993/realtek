<?php

/**
 * Class Es_Locations_Grid_Shortcode.
 */
class Es_Locations_Grid_Shortcode extends Es_Shortcode {

	public function get_content() {
		ob_start();
		es_load_template( 'front/shortcodes/locations-grid/locations-grid.php', array_merge(
			$this->get_attributes(),
			array( 'instance' => $this )
		) );
		return ob_get_clean();
	}

	/**
	 * @param $args
	 *
	 * @return string
	 */
	public function generate_location_link( $args ) {
		$request_args = array();

		if ( ! empty( $args ) ) {
			foreach ( $args as $field => $value ) {
				if ( ! es_property_get_field_info( $field ) ) continue;

				$request_args[ $field ][] = $value;
			}
		}

		return add_query_arg( $request_args, es_get_page_url( 'search_results' ) );
	}

	/**
	 * @param $args
	 *
	 * @return int
	 */
	public function count_properties( $args ) {
		$query_args = array();
		$found = 0;

		if ( ! empty( $args ) ) {
			foreach ( $args as $field => $value ) {
				if ( ! es_property_get_field_info( $field ) ) continue;

				$query_args['fields'][ $field ] = $value;
			}
		}

		if ( ! empty( $query_args ) ) {
			$query = new WP_Query( es_get_properties_query_args( $query_args ) );
			$found = $query->found_posts;
		}

		return apply_filters( 'es_locations_grid_count_properties', $found, $args, $query_args, $this );
	}

	/**
	 * @return array
	 */
	public function get_default_attributes() {
		return array(
			'title' => __( 'Explore Popular Locations', 'es' ),
			'items' => array(
				0 => array(
					'image' => '',
					'location_id' => '',
				)
			),
			'enable_counter' => 1,
			'enable_slider' => 0,
		);
	}

	/**
	 * Return shortcode name.
	 *
	 * @return array
	 */
	public static function get_shortcode_name() {
		return array( 'es_locations_grid', 'locations_grid' );
	}
}