<?php

/**
 * Class Es_Authentication_Shortcode.
 */
class Es_Compare_Shortcode extends Es_Shortcode {

	/**
	 * Render shortcode content.
	 *
	 * @return string|void
	 */
	public function get_content() {
		$instance = es_get_compare_instance();

		if ( $instance->is_enabled() ) {
			ob_start();

			es_load_template( 'front/shortcodes/compare/index.php', array(
				'query' => $instance->get_query(),
				'compare' => $instance,
				'entities_ids' => $instance->get_entities_ids(),
				'attr' => $this->get_attributes(),
			) );

			return ob_get_clean();
		}
	}

	/**
	 * @return array|mixed|void
	 */
	public function get_default_attributes() {
		return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), array(
			'title' => __( 'Compare properties', 'es' ),
		) );
	}

	/**
	 * Return shortcode name.
	 *
	 * @return string
	 */
	public static function get_shortcode_name() {
		return 'es_compare';
	}
}
