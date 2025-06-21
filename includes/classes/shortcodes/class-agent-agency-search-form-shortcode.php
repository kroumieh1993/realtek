<?php

/**
 * Class Es_Search_Form_Shortcode.
 */
class Es_Agent_Agency_Search_Form_Shortcode extends Es_Shortcode {

	/**
	 * @param $attributes
	 */
	public function merge_shortcode_attr( $attributes ) {
		$this->_attributes = wp_parse_args( $attributes, $this->get_default_attributes() );
		$this->_attributes['entity'] = $this->_attributes['type'];
	}

	/**
	 * Return search shortcode DOM.
	 *
	 * @return string|void
	 */
	public function get_content() {
		$template = sprintf( 'front/shortcodes/agent-agency-search/%s.php', $this->_attributes['search_type'] );

		foreach ( array( 'fields' ) as $fields ) {
			if ( ! empty( $this->_attributes[ $fields ] ) && is_string( $this->_attributes[ $fields ] ) ) {
				$this->_attributes[ $fields ] = explode( ',', $this->_attributes[ $fields ] );
			}
		}

		$search_page_id = $this->_attributes['search_page_id'];

		ob_start();
		es_load_template( $template, array(
			'shortcode_instance' => $this,
			'attributes' => $this->_attributes,
			'search_page_id' => $search_page_id,
			'container_classes' => $this->get_container_classes(),
			'search_page_uri' => get_permalink( $search_page_id ),
			'search_page_exists' => ! empty( $search_page_id ) && get_post_status( $search_page_id ) == 'publish'
		) );
		return ob_get_clean();
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
		return 'es-icon es-icon_search-form';
	}

	/**
	 * Return search form container css classes.
	 *
	 * @return mixed|void
	 */
	public function get_container_classes() {
		$atts = $this->_attributes;
		$classes[] = 'es-search';
		$classes[] = 'js-es-search';

		if ( empty( $atts['enable_ajax'] ) ) {
			$classes[] = 'es-search--ignore-ajax';
		}

		$classes[] = 'es-search--agent';
		$classes[] = 'js-es-search--agent';

		if ( ! empty( $atts['container_classes'] ) ) {
			$container_classes = explode( ' ', $atts['container_classes'] );

			if ( $container_classes ) {
				$classes = array_merge( $classes, $container_classes );
			}
		}

		return apply_filters( 'es_agent_search_form_get_container_classes', implode( ' ', $classes ), $classes, $atts, $this );
	}

	/**
	 * Return shortcode default attributes.
	 *
	 * @return array|void
	 */
	public function get_default_attributes() {
		$args = array(
			'title' => __( 'Find Your Real Estate Professional', 'es' ),
			'padding' => '30px 10%',
			'search_page_id' => ests( 'agent_agency_search_results_page_id' ),
			'background' => '',
			'enable_ajax' => false,
			'type' => 'agent',
			'fields' => array( 'type', 'es_service_area', 'keywords' ),
			'entity' => 'agent',
			'search_type' => 'simple',
		);

		return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), $args );
	}

	/**
	 * @return Exception|string
	 */
	public static function get_shortcode_name() {
		return 'es_agent_agency_search_form';
	}
}
