<?php

/**
 * Class Es_My_Listings_Shortcode.
 */
class Es_Properties_Map_Shortcode extends Es_My_Listing_Shortcode {

	/**
	 * @return false|string
	 */
	public function get_content() {
		ob_start();

		$attr = $this->get_attributes();
		$query = $this->get_query();
		$query_args = $this->get_query_args();

		$template_args = array(
			'query' => $query,
			'args' => $this->get_attributes(),
			'hash' => es_encode( $this->get_attributes() ),
			'coordinates' => empty( $attr['_ignore_coordinates'] ) ? es_properties_get_markers( $query_args ) : '',
			'wrapper_class' => $this->get_wrapper_class(),
			'wishlist_confirm' => $attr['wishlist_confirm'],
		);

		// Remove unused variables.
		unset( $this->_attributes['search_type'],
			$this->_attributes['search_form_selector'],
			$this->_attributes['collapsed_fields'],
			$this->_attributes['fields'],
			$this->_attributes['main_fields'] );

		$shortcode_name = static::get_shortcode_name();
		$shortcode_name = $shortcode_name[0];

		$template_args = apply_filters( $shortcode_name . '_template_args', $template_args, $this->_attributes, $this );
		es_load_template( 'front/shortcodes/map.php', $template_args );

		if ( ! empty( $attr['setup_postdata_post_id'] ) ) {
			global $post;
			$post = get_post( $attr['setup_postdata_post_id'] );
		}

		return ob_get_clean();
	}

	/**
	 * @return mixed|void
	 */
	public function get_wrapper_class() {
		$classes = array( 'js-es-properties', 'es-property-map' );

		return apply_filters( 'es_property_map_wrapper_classes', implode( ' ', $classes ), $classes, $this );
	}

	/**
	 * Return shortcode name.
	 *
	 * @return Exception|array
	 */
	public static function get_shortcode_name() {
		return array( 'es_property_map' );
	}

	/**
	 * @return array|void
	 */
	public function get_default_attributes() {
		$attr = parent::get_default_attributes();
		$attr['layout'] = 'half_map';
		$attr['hfm_full_width'] = false;
		$attr['width'] = '100%';
		$attr['height'] = '300px';
		return $attr;
	}
}
