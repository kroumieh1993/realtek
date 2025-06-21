<?php

/**
 * Class Es_Properties_Management_Shortcode.
 */
class Es_Property_Management_Shortcode extends Es_Shortcode {

	/**
	 * @return mixed|void
	 */
	public static function get_tabs() {
		$sections_builder = es_get_sections_builder_instance();
		$tabs             = array();

		if ( $sections = $sections_builder::get_items( 'property' ) ) {
			$i = 1;
			foreach ( $sections as $section_id => $section ) {
				if ( es_can_render_tab( 'property', $section_id ) ) {
					$tabs[ $section_id ] = array(
						'label'    => "<span class='es-tabs__numeric'>{$i}</span>" . $section['label'],
						'template' => es_locate_template( sprintf( 'front/shortcodes/property-management/tabs/%s.php', $section_id ) ),
						'action'   => 'es_property_pm_tab',
					);
					$i++;
				}
			}
		}

		return apply_filters( 'es_pm_tabs', $tabs );
	}

	/**
	 * @param $item
	 * @param $section_id
	 *
	 * @return mixed|void
	 */
	public static function tab_content( $item, $section_id ) {
		$fields_builder = es_get_fields_builder_instance();

		if ( $fields = $fields_builder::get_frontend_tab_fields( $section_id ) ) {
			echo "<div class='es-tabs__content-inner'>";

			foreach ( $fields as $field_key => $field_config ) {
				if ( in_array( $field_key, array( 'lot_size', 'area' ) ) ) {
					$field_config['unit_field_after_config'] = "<div class='es-unit'>" . ests_label( $field_key . '_unit' ) . "</div>";
				}

				$field_config = apply_filters( 'es_pm_before_render_property_field', $field_config, $field_key, $section_id );

				es_property_field_render( $field_key, $field_config );
			}

			echo "</div>";
		}
	}

	/**
	 * @return mixed|void
	 */
	public function get_default_attributes() {
		$def = apply_filters( 'es_pm_default_attributes', array(
			'search_type' => 'simple',
			'main_fields' => array( 'es_category', 'es_type', 'post_status' ),
			'collapsed_fields' => array(),
			'enable_saved_search' => false,
			'search_page_id' => get_the_ID(),
			'address_placeholder' => __( 'Search by title, ID or address', 'es' ),
			'show_add_new' => true,
			'container_classes' => 'js-es-submit-on-form-change',
			'search_context' => 'pm'
		), $this );

		return shortcode_atts( $def, $this->get_attributes() );
	}

	/**
	 * @return string|void
	 */
	public function get_content() {
		$screen = filter_input( INPUT_GET, 'screen' );

		if ( ! has_action( 'es_property_pm_tab', array( __CLASS__, 'tab_content' ) ) ) {
			add_action( 'es_property_pm_tab', array( __CLASS__, 'tab_content' ), 10, 2 );
		}

		ob_start();

		if ( ! get_current_user_id() ) {
			return do_shortcode( '[es_authentication]' );
		}

		es_current_user_can_or_die( 'view_property_management' );

		if ( ! es_user_can_publish_listings() ) {
			es_clear_flash( 'prop-management' );
			es_set_flash( 'prop-management', __( 'You’re hit your listing limits.' ) . sprintf( wp_kses( __( 'Please <a href="%s">upgrade your plan</a> to publish more listings.', 'es' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( es_get_page_url( 'pricing' ) ) ), 'warning' );
		}

		wp_enqueue_script( 'es-property-metabox' );

		if ( ! $screen || $screen == 'listings' ) {
			es_load_template( 'front/shortcodes/property-management/listings.php', array(
				'config' => $this->get_attributes()
			) );
		} else if ( $screen == 'add-new-property' ) {
			if ( ! ests( 'is_frontend_management_enabled' ) ) {
				wp_die( __( 'Nothing to display', 'es' ) );
			} else {
				if ( es_current_user_can_or_die( 'create_es_properties' ) ) {
					if ( es_user_can_publish_listings() ) {
						es_load_template( 'front/shortcodes/property-management/property-form.php', array(
							'tabs' => static::get_tabs(),
							'id' => null
						) );
					} else {
						es_load_template( 'front/shortcodes/property-management/limit-exceeded.php' );
					}
				}
			}
		} else if ( $screen == 'edit-property' ) {
			$property_id = intval( filter_input( INPUT_GET, 'property_id' ) );

			if ( es_current_user_can_or_die( 'edit_post', $property_id ) ) {
				$query = new WP_Query( array( 'post_type' => 'properties', 'p' => $property_id ) );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						es_load_template( 'front/shortcodes/property-management/property-form.php', array(
							'tabs' => static::get_tabs()
						) );
					}

					wp_reset_postdata();
				}
			}
		} else {
			wp_die( __( 'Nothing to display', 'es' ) );
		}

		return ob_get_clean();
	}

	/**
	 * @return Exception|string|string[]
	 */
	public static function get_shortcode_name() {
		return array( 'es_prop_management', 'property_management' );
	}
}
