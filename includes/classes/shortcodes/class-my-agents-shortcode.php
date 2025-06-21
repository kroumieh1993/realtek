<?php

use Elementor\Controls_Manager;

/**
 * Class Es_My_Listings_Shortcode.
 */
class Es_My_Agents_Shortcode extends Es_My_Entities_Shortcode {

	/**
	 * @return false|string
	 */
	public function get_content() {
		if ( ! ests( 'is_agents_enabled' ) ) {
			return null;
		}

		$attr = $this->get_attributes();

		if ( $attr['type'] == 'agency' ) {
			$instance = es_get_shortcode_instance( 'es_my_agencies', $attr );

			return $instance->get_content();
		}

		ob_start();
		$query = $this->get_query();

		// Set template when load listings via ajax.
		$template = $attr['_ajax_mode'] ? 'front/entity/entities-list.php' : 'front/shortcodes/my-entities.php';

		// Remove unused variables.
		unset( $this->_attributes['action'],
			$this->_attributes['_ajax_mode'],
			$this->_attributes['hash'] );

		$template_args = array(
			'query' => $query,
			'search_form' => empty( $attr['_ajax_mode'] ) ? $this->get_search_form_instance() : false,
			'args' => $this->get_attributes(),
			'hash' => es_encode( $this->get_attributes() ),
			'css_layout' => $this->get_layout_class(),
			'wishlist_confirm' => $attr['wishlist_confirm'],
			'wrapper_class' => $this->get_wrapper_class(),
			'entity_name' => 'agent',
			'plural_entity_name' => 'agents',
		);

		// Remove unused variables.
		unset( $this->_attributes['search_type'],
			$this->_attributes['search_form_selector'],
			$this->_attributes['collapsed_fields'],
			$this->_attributes['fields'],
			$this->_attributes['main_fields'] );

		$shortcode_name =  static::get_shortcode_name();

		$template_args = apply_filters( $shortcode_name[0] . '_template_args', $template_args, $this->_attributes, $this );
		es_load_template( $template, $template_args );

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
		$classes = array( 'es-agents', 'js-es-agents-wrap', 'js-es-entities-wrap' );

		return apply_filters( 'es_my_agents_wrapper_classes', implode( ' ', $classes ), $classes, $this );
	}

	/**
	 * Return listings layout.
	 *
	 * @return mixed|string
	 */
	public function get_layout_class() {
		$attr = $this->get_attributes();
		$layout_css_class = 'es-entities--list';

		if ( in_array( $attr['layout'], array( 'grid', 'list' ) ) ) {
			$layout_css_class = "es-entities--" . $attr['layout'];
		}

		return apply_filters( 'es_my_listings_layout_css_class', $layout_css_class, $attr );
	}

	/**
	 * Return search form shortcode instance.
	 *
	 * @return mixed|void
	 */
	public function get_search_form_instance() {
		if ( $this->_attributes['enable_search'] ) {
			$attributes = $this->_attributes;
			unset( $attributes['layout'] );

			$shortcode = es_get_shortcode_instance( 'es_agent_agency_search_form', $attributes );
		} else {
			$shortcode = null;
		}

		$shortcode_name =  static::get_shortcode_name();

		return apply_filters( sprintf( '%s_search_form_shortcode_instance', $shortcode_name[0] ), $shortcode, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_query_args() {

		$page_num = ! empty( $_GET[ 'paged-' . $this->_attributes['loop_uid'] ] ) ? $_GET[ 'paged-' . $this->_attributes['loop_uid'] ] : $this->_attributes['page_num'];
		$page_num = intval( $page_num );

		if ( $sort = filter_input( INPUT_GET, 'sort-' . $this->_attributes['loop_uid'] ) ) {
			$this->_attributes['sort'] = $sort;
		}

		$args = array(
			'query' => array(
				'posts_per_page' => $this->_attributes['posts_per_page'],
				'paged' => $page_num,
			),
			'fields' => $this->_attributes,
		);

		if ( ! empty( $this->_attributes['limit'] ) ) {
			unset( $args['query']['paged'] );
			$args['query']['no_found_rows'] = true;
			$args['query']['posts_per_page'] = $this->_attributes['limit'];
		}

		if ( ! empty( $this->_attributes['agents_id'] ) ) {
			$args['query']['post__in'] = array_map( 'trim', explode( ',', $this->_attributes['agents_id'] ) );
		}

		$query = apply_filters( 'es_agents_shortcode_query_args', es_get_agents_query_args( $args ), $this->_attributes, $this );

		$shortcode_name =  static::get_shortcode_name();

		return apply_filters( sprintf( "es_%s_query_args", $shortcode_name[0] ),
			$query, $this->_attributes, $this );
	}

	/**
	 * Return shortcode default attributes.
	 *
	 * @return array|void
	 */
	public function get_default_attributes() {
		$shortcode_name =  static::get_shortcode_name();
		return apply_filters( sprintf( '%s_default_attributes', $shortcode_name[0] ), array(
			'layout' => ests( 'agents_layout' ),
			'posts_per_page' => ests( 'agents_per_page' ),
			'disable_navbar' => false,
			'show_sort' => ests( 'is_agents_sorting_enabled' ),
			'show_total' => true,
			'show_page_title' => true,
			'show_layouts' => ests( 'is_agents_layout_switcher_enabled' ),
			'sort' => ests( 'agents_default_sorting_option' ),
			'limit' => 0,
			'page_num' => null,
			'agents_id' => '',
			'type' => 'agent',
			'loop_uid' => '',
			'page_title' => get_the_title(),
			'ignore_search' => false,
			'search_form_selector' => ests( 'is_update_search_results_enabled' ) ? '.js-es-search:not(.es-search--ignore-ajax)' : null,
			'enable_search' => false,
			'search_type' => 'simple',
			'enable_ajax' => true,
			'view_all_link_name' => __( 'View all', 'es' ),
			'view_all_page_id' => null,
			'disable_pagination' => false,
			'wishlist_confirm' => false,
			'_ajax_mode' => false,
			'setup_postdata_post_id' => false,
		) );
	}

	/**
	 * Return shortcode name.
	 *
	 * @return array
	 */
	public static function get_shortcode_name() {
		return array( 'es_my_agents', 'es_agents', 'agents' );
	}
}
