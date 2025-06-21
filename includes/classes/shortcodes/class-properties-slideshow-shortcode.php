<?php

/**
 * Class Es_Slider_Shortcode.
 */
class Es_Properties_Slideshow_Shortcode extends Es_My_Listing_Shortcode {

	/**
	 * Return search shortcode DOM.
	 *
	 * @return string|void
	 */
	public function get_content() {
		$attributes = $this->get_attributes();
		ob_start();
		$layouts = static::get_layouts();

		if ( ! empty( $layouts[ $attributes['layout'] ] ) ) {
			$query = new WP_Query( $this->get_query_args() );

			if ( is_string( $attributes['fields_to_show'] ) ) {
				$attributes['fields_to_show'] = explode( ',', $attributes['fields_to_show'] );
			}

			es_load_template( 'front/shortcodes/slideshow/' . $this->get_layout_template() . '.php', array(
				'query' => $query,
				'attributes' => $attributes,
				'fields_to_show' => array_combine( $attributes['fields_to_show'], $attributes['fields_to_show'] ),
			) );

			wp_reset_postdata();
		}

		return ob_get_clean();
	}

	/**
	 * @return mixed|void
	 */
	public function get_layout_template() {
		$attributes = $this->get_attributes();
		$layout = $attributes['layout'];

		if ( in_array( $attributes['layout'], array( 'side-previews', 'side-info' ) ) ) {
			$layout = 'side-info';
		}

		return apply_filters( 'es_properties_slideshow_layout_template', $layout, $this );
	}

	/**
	 * Return shortcode name.
	 *
	 * @return Exception|string
	 */
	public static function get_shortcode_name() {
		return 'es_properties_slideshow';
	}

	/**
	 * Return slideshow layouts list.
	 *
	 * @return mixed|void
	 */
	public static function get_layouts() {
		return apply_filters( 'es_properties_slideshow_layouts', array(
			'info-block' => __( 'Info block', 'es' ),
			'side-previews' => __( 'Side previews', 'es' ),
			'side-info' => __( 'Side info', 'es' ),
			'bottom-previews' => __( 'Bottom previews', 'es' ),
		) );
	}

	/**
	 * Return shortcode default args.
	 *
	 * @return array|void
	 */
	public function get_default_attributes() {
		$args = array(
			'max_height' => '530px',
			'layout' => 'info-block',
			'info_background' => '#263238',
			'id' => 'es-slideshow-' . uniqid(),
			'side' => 'left',
			'ignore_search' => 1,
			'enable_progress_bar' => 1,
			'progress_bar_position' => 'top',
			'is_arrows_enabled' => 1,
			'enable_wishlist' => 1,
			'autoplay' => true,
			'fields_to_show' => array(
				'title', 'es_label', 'address', 'price', 'price_note', 'es_type',
				'es_category',
			),
		);

		return es_parse_args( $args, parent::get_default_attributes() );
	}
}
