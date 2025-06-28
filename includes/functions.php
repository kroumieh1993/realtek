<?php

if ( ! function_exists( 'es_format_values' ) ) {

	/**
	 * @param $values
	 * @param $format
	 * @param array $args
	 *
	 * @return mixed
	 */
	function es_format_values( $values, $format, $args = array() ) {
		if ( ! empty( $values ) ) {
			foreach ( $values as $key => $value ) {
				$values[ $key ] = es_format_value( $value, $format, $args );
			}
		}
		return $values;
	}
}

if ( ! function_exists( 'es_format_value' ) ) {

	/**
	 * Format value by formatter type.
	 *
	 * @param $value string|array
	 * @param $format string
	 * @param array $args
	 *
	 * @return mixed
	 */
	function es_format_value( $value, $format, $args = array() ) {
		$formatted_value = $value;

		switch ( $format ) {
			case 'url':
				if ( ! empty( $value['url'] ) && ! empty( $value['label'] ) ) {
					$formatted_value = sprintf( "<a href='%s' target='_blank'>%s</a>", esc_url( $value['url'] ), $value['label'] );
				} else if ( is_string( $value ) ) {
					$formatted_value = sprintf( "<a href='%s' target='_blank'>%s</a>", esc_url( $value ), $value );
				}
				break;

			case 'link':
				if ( is_array( $value ) && ! empty( $value['url'] ) ) {
					$label = ! empty( $value['label'] ) ? $value['label'] : $value['url'];
					$formatted_value = sprintf( "<a href='%s'>%s</a>", esc_url( $value['url'] ), $label );
				}
				break;

            case 'date_added':
                $date = human_time_diff( $value, current_time( 'U' ) );
                $formatted_value = sprintf( __( 'Added %s ago', 'es' ), $date );
                break;
            case 'price':
            case 'price-area':
                if ( $value ) {
                    $dec = stripslashes( ests( 'currency_dec' ) );
                    $sup = stripslashes( ests( 'currency_sup' ) );
                    $price_format = $sup . $dec;
                    $position = ests( 'currency_position' );
                    $sign = ests_label( 'currency_sign' );
                    $currency = $sign ? $sign : ests( 'currency' );
                    $space = empty( $sign ) ? ' ' : '';

					if ( in_array( $position, array( 'before_space', 'after_space' ) ) ) {
						$space = ' ';
					}

                    $dec_num = $sup == ' ' || $sup == ',' || $sup == '.' || '\'' || empty( $sup ) ? 0 : 2;
                    $dec_num = $price_format == ',.' || $price_format == '.,' ? 2 : $dec_num;

					$price_temp = floatval( $value );
					$price_temp = number_format( $price_temp, $dec_num, $dec, $sup );

                    if ( $format == 'price-area' ) {
                        $price_temp .= '/' . __( 'sq ft', 'es' );
                    }
                    $formatted_value = $position == 'after' || $position == 'after_space' ? $price_temp . $space . $currency : $currency . $space . $price_temp;
                }
                break;

			case 'beds':
			    $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'bed', 'beds', $value, 'es' ) );
				break;

            case 'baths':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'bath', 'baths', $value, 'es' ) );
				break;

            case 'floors':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'floor', 'floors', $value, 'es' ) );
                break;

            case 'half_baths':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'half bath', 'half baths', $value, 'es' ) );
                break;

			case 'post-link':
				if ( $value ) {
					$formatted_value = sprintf( "<a href='%s'>%s</a>", get_permalink( $value ), get_the_title( $value ) );
				}
				break;

			case 'area':
			case 'lot_size':
				if ( ! empty( $args['unit'] ) ) {
					$values = ests_values( $format . '_unit' );
					$unit = ! empty( $values[ $args['unit'] ] ) ? $values[ $args['unit'] ] :
						ests_label( $format . '_unit' );
				} else {
					$unit = ests_label( $format . '_unit' );
				}

                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, $unit );
                break;

			case 'document':
				if ( ! empty( $value ) ) {
					ob_start();
					es_load_template( 'front/property/partials/documents.php', array(
						'attachments_ids' => $value
					) );
					$formatted_value = ob_get_clean();
				}
				break;

			case 'image':
				if ( ! empty( $value ) ) {
					ob_start();
					es_load_template( 'front/property/partials/images.php', array(
						'attachments_ids' => $value
					) );
					$formatted_value = ob_get_clean();
				}
				break;

			case 'country':
			case 'city':
			case 'state':
			case 'province':
				$term = get_term( $value, 'es_location' );
				if ( $term ) {
					if ( es_get_search_page_url() ) {
						$formatted_value = "<a href='" . esc_url( get_term_link( $term ) ) . "'>{$term->name}</a>";
					} else {
						$formatted_value = $term->name;
					}
				}

				break;

			case 'rooms':
				if ( is_array( $value ) ) {
					$value = array_filter( es_array_filter_recursive( $value ) );
				}
				if ( ! empty( $value ) ) {
					ob_start();
					es_load_template( 'front/property/partials/rooms.php', array(
						'rooms' => $value
					) );
					$formatted_value = ob_get_clean();
				}
				break;

			case 'appointments':
				ob_start();
				echo "<ul class='es-appointments'>";
				if ( is_array( $value ) ) {
					$time_format = ests( 'time_format' );

					foreach ( $value as $appointment ) {
						if ( $time_format == 'h' ) {
							foreach ( array( 'start_time', 'end_time' ) as $field ) {
								if ( ! empty( $appointment[ $field ] ) ) {
									$dt = new DateTime( $appointment[ $field ] );
									$appointment[ $field ] = $dt->format( 'h:i A ' );
								}
							}
						}

						printf( "<li><b>%s</b><span>%s to %s</span></li>", $appointment['date'], $appointment['start_time'], $appointment['end_time'] );
					}
				}
				echo "</ul>";
				$formatted_value = ob_get_clean();
				break;

			case 'phones':
				/** @var $value array */
				if ( ! empty( $value ) && is_array( $value ) ) {
					$formatted_value = array();
					foreach ( $value as $tel_item ) {
						if ( ! empty( $tel_item['phone'] ) ) {
							$tel = es_get_formatted_tel( $tel_item['phone'] );
							$formatted_value[] = $tel;
						}
					}

					if ( ! empty( $formatted_value ) ) {
						$formatted_value = implode( '<br>', $formatted_value );
					}
				}
				break;

			case 'social_links':
				$networks = es_get_social_networks_list();
				$links = array();

				foreach ( $networks as $network => $label ) {
					if ( ! empty( $args['entity']->{$network} ) ) {
						$links[ $network ] = $args['entity']->{$network};
					}
				}

				if ( ! empty( $links ) ) {
					ob_start();
					es_load_template( 'front/partials/social-links.php', array(
						'links' => $links,
					) );
					$formatted_value = ob_get_clean();
				} else {
					$formatted_value = false;
				}

				break;

			case 'links':
				/** @var $value array */
				if ( ! empty( $value ) && is_array( $value ) ) {
					$formatted_value = array();
					foreach ( $value as $link ) {
						$key = key( $link );
						if ( ! empty( $link[ $key ] ) ) {
							$formatted_value[] = sprintf( "<a href='%s'>%s</a>", esc_url( $link[ $key ] ), $link[ $key ] );
						}
					}

					if ( ! empty( $formatted_value ) ) {
						$formatted_value = implode( '<br>', $formatted_value );
					}
				}
				break;

			case 'single-repeater':
				/** @var $value array */
				if ( ! empty( $value ) ) {
					$formatted_value = array();
					foreach ( $value as $item ) {
						$key = key( $item );
						if ( ! empty( $item[ $key ] ) ) {
							$formatted_value[] = $item[ $key ];
						}
					}

					if ( ! empty( $formatted_value ) ) {
						$formatted_value = implode( ', ', $formatted_value );
					}
				}
				break;

			case 'video':
				if ( $value ) {
					$formatted_value = '';
					if ( ! empty( $value['video_url'] ) ) {
						$formatted_value = wp_oembed_get( esc_url( $value['video_url'] ) );
					}
					if ( ! empty( $value['video_iframe'] ) ) {
						$formatted_value .= html_entity_decode( $value['video_iframe'] );
					}
					if ( ! empty( $value['video_file'] ) ) {
						$formatted_value .= wp_video_shortcode( array(
							'src' => wp_get_attachment_url( $value['video_file'] ),
						) );
					}
				}
				break;

			case 'switcher':
				if ( ! $value ) {
					$formatted_value = _x( 'No', 'switcher field value', 'es' );
				} else {
					$formatted_value = _x( 'Yes', 'switcher field value', 'es' );
				}
				break;

            default:
                if ( is_array( $value ) ) {
                    $formatted_value = implode( ', ', $value );
                }

			}

		if ( ! empty( $args['suff'] ) ) {
			$formatted_value = $formatted_value . $args['suff'];
		}

		if ( ! empty( $args['pref'] ) ) {
			$formatted_value = $args['pref'] . $formatted_value;
		}

		return apply_filters( 'es_format_value', $formatted_value, $value, $format, $args );
	}
}

if ( ! function_exists( 'es_locate_template' ) ) {

	/**
	 * Return plugin template path.
	 *
	 * @param $template_path string
	 *
	 * @return string
	 */
	function es_locate_template( $template_path ) {

		$find = array();
		$path = $template_path;
		$context = ES_PLUGIN_PATH . DS . 'templates' . DS;
		$base = $template_path;

		$find[] = 'realtek4/' . $template_path;
		$find[] = $context . $template_path;

		$template_path = locate_template( array_unique( $find ) );

		if ( ! $template_path ) {
			$template_path = $context . $base;
		}

		return apply_filters( 'es_locate_template', $template_path, $path );
	}
}

if ( ! function_exists( 'es_load_template' ) ) {

	/**
	 * Include template by provided path.
	 *
	 * @see es_locate_template
	 *
	 * @param $template_path string
	 *    Template path.
	 *
	 * @param array $args
	 *    Template variables list.
	 */
	function es_load_template( $template_path, $args = array() ) {

		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		$args = apply_filters( 'es_template_args', $args, $template_path );
		extract( $args );

		include es_locate_template( $template_path );
	}
}

if ( ! function_exists( 'es_get_settings_container' ) ) {

	/**
	 * Return plugin settings container.
	 *
	 * @return Es_Settings_Container
	 */
	function es_get_settings_container() {
		return apply_filters( 'es_settings_container', new Es_Settings_Container() );
	}
}

if ( ! function_exists( 'es_get_available_compare_fields' ) ) {

	/**
	 * Return available search fields for main and collapsed filters.
	 *
	 * @return array
	 */
	function es_get_available_compare_fields() {
		$fields = es_get_entity_fields( 'property' );
		$fields = array_filter( $fields, 'es_filter_compare_fields' );

		if ( ! empty( $fields ) ) {
			$fields = wp_list_pluck( $fields, 'label' );
			asort( $fields );
		}

		return apply_filters( 'es_get_available_compare_fields', $fields );
	}
}

if ( ! function_exists( 'es_get_available_search_fields' ) ) {

	/**
	 * Return available search fields for main and collapsed filters.
	 *
	 * @return array
	 */
	function es_get_available_search_fields() {
		$fields = es_get_entity_fields( 'property' );
		$fields = array_filter( $fields, 'es_filter_search_fields' );

	    if ( ! empty( $fields ) ) {
	        $fields = wp_list_pluck( $fields, 'label' );
			asort( $fields );
        }

		return apply_filters( 'es_get_available_search_fields', $fields );
	}
}

if ( ! function_exists( 'es_get_default_sections' ) ) {

	/**
	 * Return entity default sections.
	 *
	 * @param string $entity
	 *
	 * @return array
	 */
	function es_get_default_sections( $entity = 'property' ) {

		$sections = apply_filters( 'es_get_default_sections', array(
			'property' => array(
				'basic-facts' => array(
					'label' => __( 'Basics', 'es' ),
					'fb_settings' => array(
						'disable_order' => true,
						'disable_deletion' => true,
					),
					'order' => 10,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'description' => array(
					'label' => __( 'Description', 'es' ),
					'order' => 20,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'is_pdf_visible' => true,
				),
				'rooms' => array(
					'label' => __( 'Rooms', 'es' ),
					'order' => 25,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'is_pdf_visible' => true,
				),
				'open-house' => array(
                    'label' => __( 'Open House', 'es' ),
                    'order' => 30,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                    'is_pdf_visible' => true,
                ),
				'location' => array(
					'label' => __( 'Location', 'es' ),
					'order' => 40,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'is_pdf_visible' => true,
				),
				'media' => array(
					'label' => __( 'Photos & Media', 'es' ),
					'order' => 50,
				),
                'building-details' => array(
                    'label' => __( 'Building Details', 'es' ),
                    'order' => 60,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                    'is_pdf_visible' => true,
                ),
                'video' => array(
                    'label' => __( 'Video', 'es' ),
                    'order' => 70,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
                'documents' => array(
                    'label' => __( 'Documents', 'es' ),
                    'order' => 80,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                    'is_pdf_visible' => true,
                ),
                'floors_plans' => array(
                    'label' => __( 'Floor Plans', 'es' ),
                    'order' => 90,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                    'is_pdf_visible' => true,
                ),
				'features' => array(
					'label' => __( 'Amenities & Features', 'es' ),
					'order' => 100,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'is_pdf_visible' => true,
				),
				'request_form' => array(
					'label' => __( 'Ask an Agent About This Home', 'es' ),
					'section_name' => __( 'Request form', 'es' ),
					'order' => 110,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'options' => array(
						'background_color' => '#ffffff',
						'text_color' => '#263238',
						'recipient_type' => 2,
					),
				),
			),
			'agent' => array(
				'basic-facts' => array(
					'label' => __( 'User account', 'es' ),
					'order' => 10,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'professional-info' => array(
					'label' => __( 'Professional Information', 'es' ),
					'order' => 40,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'description' => array(
					'label' => __( 'About', 'es' ),
					'frontend_name_callback' => 'es_get_the_agent_description_section_title',
					'order' => 30,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'contacts' => array(
					'label' => __( 'Contacts', 'es' ),
					'order' => 20,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'listings' => array(
					'label' => __( 'Listings', 'es' ),
					'frontend_name_callback' => 'es_get_the_agent_listings_section_title',
					'order' => 50,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'frontend_action' => 'es_agent_listings',
				),
				'reviews' => array(
					'frontend_name_callback' => 'es_get_the_agent_reviews_section_title',
					'order' => 60,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'frontend_action' => 'es_entity_reviews',
				),
				'video' => array(
					'label' => __( 'Video', 'es' ),
					'order' => 30,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'social-networks' => array(
					'label' => __( 'Social Networks', 'es' ),
					'order' => 60,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'request_form' => array(
					'order' => 100,
					'is_visible' => ests( 'is_agent_contact_form_enabled' ),
					'is_visible_for' => array( 'all_users' ),
					'options' => array(
						'background_color' => '#ffffff',
						'text_color' => '#263238',
					),
					'frontend_action' => 'es_entity_request_form_section',
				),
			),
			'agency' => array(
				'basic-facts' => array(
					'label' => __( 'Basic facts', 'es' ),
					'order' => 10,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'team' => array(
					'label' => __( 'Team', 'es' ),
					'frontend_name' => __( 'Our team', 'es' ),
					'order' => 20,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'frontend_action' => 'es_agency_our_team_section_content'
				),
				'video' => array(
					'label' => __( 'Video', 'es' ),
					'order' => 30,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'contacts' => array(
					'label' => __( 'Contacts', 'es' ),
					'order' => 40,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'description' => array(
					'label' => __( 'About', 'es' ),
					'frontend_name_callback' => 'es_get_the_agent_description_section_title',
					'order' => 50,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'professional-info' => array(
					'label' => __( 'Professional Information', 'es' ),
					'order' => 60,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'listings' => array(
					'label' => __( 'Listings', 'es' ),
					'frontend_name_callback' => 'es_get_the_agent_listings_section_title',
					'order' => 70,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'frontend_action' => 'es_agency_listings',
				),
				'social-networks' => array(
					'label' => __( 'Social Networks', 'es' ),
					'order' => 80,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'reviews' => array(
					'frontend_name_callback' => 'es_get_the_agent_reviews_section_title',
					'order' => 90,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'frontend_action' => 'es_entity_reviews',
				),
				'request_form' => array(
					'order' => 100,
					'is_visible' => ests( 'is_agency_contact_form_enabled' ),
					'is_visible_for' => array( 'all_users' ),
					'options' => array(
						'background_color' => '#ffffff',
						'text_color' => '#263238',
					),
					'frontend_action' => 'es_entity_request_form_section',
				),
			),
		) );

		return apply_filters( 'es_get_entity_default_sections',
			! empty( $sections[ $entity ] ) ? $sections[ $entity ] : array() );
	}
}

if ( ! function_exists( 'es_get_property' ) ) {

	/**
	 * Return property instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Property
	 */
	function es_get_property( $id = null ) {
		return apply_filters( 'es_get_property', new Es_Property( $id ), $id );
	}
}

if ( ! function_exists( 'es_get_request' ) ) {

	/**
	 * Return property instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Request
	 */
	function es_get_request( $id = null ) {
		return apply_filters( 'es_get_request', new Es_Request( $id ), $id );
	}
}


if ( ! function_exists( 'es_get_order' ) ) {

	/**
	 * Return property instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Order
	 */
	function es_get_order( $id = null ) {
		return apply_filters( 'es_get_order', new Es_Order( $id ), $id );
	}
}

if ( ! function_exists( 'es_get_subscription_plan' ) ) {

	/**
	 * Return subscription instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Subscription_Plan
	 */
	function es_get_subscription_plan( $id = null ) {
		return apply_filters( 'es_get_subscription_plan', new Es_Subscription_Plan( $id ), $id );
	}
}

if ( ! function_exists( 'es_get_agency' ) ) {

	/**
	 * @param null $id
	 *
	 * @return Es_Agency
	 */
	function es_get_agency( $id = null ) {
		return apply_filters( 'es_get_agency', new Es_Agency( $id ) );
	}
}

if ( ! function_exists( 'es_get_agent' ) ) {

	/**
	 * @param null $id
	 *
	 * @return Es_Agent_Post
	 */
	function es_get_agent( $id = null ) {
		return apply_filters( 'es_get_agent', new Es_Agent_Post( $id ) );
	}
}

if ( ! function_exists( 'es_get_saved_search' ) ) {

	/**
	 * Return saved search instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Saved_Search
	 */
	function es_get_saved_search( $id = null ) {
		return apply_filters( 'es_get_saved_search', new Es_Saved_Search( $id ), $id );
	}
}

if ( ! function_exists( 'es_get_term_color' ) ) {

	/**
	 * Return term color. Function used for es_label taxonomy.
	 *
	 * @param $term_id .
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	function es_get_term_color( $term_id, $default = '' ) {
		$default = $default ? $default : ests( 'default_label_color' );
		$term_color = get_term_meta( $term_id, 'es_color', true );
		$term_color = $term_color ? $term_color : $default;

		return es_strtolower( $term_color );
	}
}

if ( ! function_exists( 'es_search_get_field_config' ) ) {

	/**
	 * Return field config for search widget.
	 *
	 * @param $field string
	 *
	 * @param $entity_name
	 *
	 * @return array
	 */
	function es_search_get_field_config( $field, $entity_name = 'property' ) {
		$entity = es_get_entity( $entity_name );
		$field = trim( $field );

		$field_config = $entity::get_field_info( $field );

		if ( ! empty( $field_config ) ) {
			$field_config = es_parse_args( $field_config, array(
				'search_settings' => array(
					'type' => ! empty( $field_config['type'] ) ? $field_config['type'] : null,
					'wrapper_class' => '',
					'attributes' => array(
						'data-single_unit' => '',
						'data-plural_unit' => '',
						'multiple' => false,
					),
					'values' => array(),
				),
				'formatter' => 'default'
			) );

			$field_config['type'] = ! empty( $field_config['type'] ) ? $field_config['type'] : $field_config['search_settings']['type'];

			if ( ! empty( $field_config['options'] ) ) {
				$field_config['search_settings']['values'] = $field_config['options'];
			}

			if ( 'select' == $field_config['search_settings']['type'] && empty( $field_config['search_settings']['attributes']['data-placeholder'] ) ) {
				$field_config['search_settings']['attributes']['data-placeholder'] = __( 'Choose value', 'es' );
				$field_config['search_settings']['attributes']['search-placeholder'] = ! empty( $field_config['label'] ) ? $field_config['label'] : '';
			}

			if ( isset( $field_config['search_settings']['label'] ) && $field_config['search_settings']['label'] === false ) {
				unset( $field_config['label'] );
			}

			if ( ! empty( $field_config['search_settings']['values_callback'] ) ) {
				$callback = $field_config['search_settings']['values_callback'];
				$args = ! empty( $callback['args'] ) ? $callback['args'] : array();
				$values = call_user_func_array( $callback['callback'], $args );
				$field_config['search_settings']['values'] = $values;
				/** @var Es_Entity $entity_fields */
				$entity::$entity_fields[ $field ]['search_settings']['values'] = $values;
			}
		}

		return $field_config;
	}
}

/**
 * Return shortcode class name by shortcode name.
 *
 * @param $shortcode_name
 *
 * @return bool|string
 */
function es_get_shortcode_classname( $shortcode_name ) {
	$class_name = ! empty( Es_Shortcodes_List::$_shortcodes[ $shortcode_name ] ) ?
		Es_Shortcodes_List::$_shortcodes[ $shortcode_name ] : false;

	return apply_filters( 'es_get_shortcode_classname', $class_name, $shortcode_name );
}

/**
 * Return shortcode instance.
 *
 * @param $shortcode_name
 * @param array $attributes
 *
 * @return null|Es_Shortcode
 */
function es_get_shortcode_instance( $shortcode_name, $attributes = array() ) {
	$shortcode_classname = es_get_shortcode_classname( $shortcode_name );
	$instance = null;

	if ( ! empty( $shortcode_classname ) ) {
		$instance = new $shortcode_classname( $attributes );
	}

	return apply_filters( 'es_get_shortcode_instance', $instance, $shortcode_name, $attributes );
}

if ( ! function_exists( 'es_get_entities_table_instance' ) ) {

	/**
	 * @param $entity_type
	 * @param $args
	 *
	 * @return Es_Properties_Table
	 */
	function es_get_entities_table_instance( $entity_type, $args = array() ) {
		$instance = null;

		if ( $entity_type == 'property' ) {
			$instance = new Es_Properties_Table( $args );
		}

		return apply_filters( 'es_get_entities_table_instance', $instance, $entity_type, $args );
	}
}

/**
 * Return entities compare instance.
 *
 * @return Es_Compare
 */
function es_get_compare_instance() {
	return apply_filters( 'es_get_compare_instance', Es_Compare::get_instance() );
}

if ( ! function_exists( 'es_get_wishlist_instance' ) ) {

	/**
	 * Return wishlist instance.
	 *
	 * @param $entity_name
	 *
	 * @return Es_Wishlist_Cookie|Es_Wishlist_User
	 */
	function es_get_wishlist_instance( $entity_name = 'property' ) {

		if ( is_user_logged_in() ) {
			$instance =  new Es_Wishlist_User( get_current_user_id(), $entity_name );
		} else {
			$instance = new Es_Wishlist_Cookie( $entity_name );
		}

		return apply_filters( 'es_get_wishlist_instance', $instance );
	}
}

/**
 * @return mixed|void
 */
function es_get_auth_networks_list() {
	return apply_filters( 'es_get_auth_networks_list', array( 'facebook', 'google' ) );
}

/**
 * Return social network auth class instance.
 *
 * @param $network
 * @param array $config
 *
 * @return Es_Authentication
 */
function es_get_auth_instance( $network, $config = array() ) {
	$instance = null;

	switch ( $network ) {
		case 'facebook':
			$instance = new Es_Facebook_Authentication( $config );
			break;

		case 'google':
			$instance = new Es_Google_Authentication( $config );
			break;
	}

	return apply_filters( 'es_get_auth_instance', $instance, $network, $config );
}

/**
 * Return redirect url after success auth.
 *
 * @return mixed|void
 */
function es_get_success_auth_redirect_url() {
	$profile_page_id = ests( 'profile_page_id' );

    if ( $profile_page_id && get_post_status( $profile_page_id ) ) {
        $url = get_permalink( $profile_page_id );
    } else {
        $url = home_url();
    }

	return apply_filters( 'es_get_success_auth_redirect_url', $url );
}

if ( ! function_exists( 'es_get_user_entity' ) ) {

	/**
	 * Return user entity.
	 *
	 * @param null $user_id
	 *
	 * @return Es_User|null
	 */
	function es_get_user_entity( $user_id = null ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		$entity = null;

		if ( $user_id ) {
			if ( user_can( $user_id, 'agent' ) ) {
				$entity = es_get_agent_user( $user_id );
			} else {
				$entity = new Es_User( $user_id );
			}
		}

		return $entity;
	}
}

/**
 * @param $user_id
 *
 * @return Es_Agent_User
 */
function es_get_agent_user( $user_id ) {
	return apply_filters( 'es_get_agent_user', new Es_Agent_User( $user_id ), $user_id );
}

/**
 * @return mixed|void
 */
function es_get_search_page_url() {
	$url = null;
	$map_search_page_id = ests( 'map_search_page_id' );
	$search_results_page_id = ests( 'search_results_page_id' );

	if ( $search_results_page_id && get_post_status( $search_results_page_id ) == 'publish' ) {
		$url = es_get_permalink( $search_results_page_id );
	} else if ( $map_search_page_id && get_post_status( $map_search_page_id ) == 'publish' ) {
		$url = es_get_permalink( $map_search_page_id );
	}

	return apply_filters( 'es_get_search_page_url', $url );
}

/**
 * Return list of supported social network links.
 *
 * @return mixed|void
 */
function es_get_social_networks_list() {
	return apply_filters( 'es_get_social_networks_list', array(
		'facebook' => __( 'Facebook', 'es' ),
		'linkedin' => __( 'LinkedIn', 'es' ),
		'twitter' => __( 'Twitter', 'es' ),
		'instagram' => __( 'Instagram', 'es' ),
		'pinterest' => __( 'Pinterest', 'es' ),
		'youtube' => __( 'YouTube', 'es' ),
	) );
}

if ( ! function_exists( 'es_user_get_default_image_url' ) ) {

	/**
	 * @param null $user_id
	 *
	 * @return string
	 */
	function es_user_get_default_image_url( $user_id = null ) {
		$def_image = ES_PLUGIN_URL . 'public/img/avatar.svg';
		$def_image = apply_filters( 'es_user_get_default_image_url_avatar', $def_image );

		return apply_filters( 'es_user_get_default_image_url', $def_image, $user_id );
	}
}

if ( ! function_exists( 'es_agent_get_default_image_url' ) ) {

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	function es_agent_get_default_image_url( $size = 'thumbnail' ) {
		$agent_attachment_id = ests( 'default_agent_avatar_attachment_id' );

		return $agent_attachment_id ?
			wp_get_attachment_image_url( $agent_attachment_id, $size ) : es_user_get_default_image_url();
	}
}

if ( ! function_exists( 'es_agency_get_default_image_url' ) ) {

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	function es_agency_get_default_image_url( $size = 'thumbnail' ) {
		$agency_attachment_id = ests( 'default_agency_avatar_attachment_id' );

		return $agency_attachment_id ?
			wp_get_attachment_image_url( $agency_attachment_id, $size ) :
			ES_PLUGIN_URL . 'public/img/agency-avatar.svg';
	}
}

if ( ! function_exists( 'es_get_rating_markup' ) ) {

	/**
	 * @param $rating
	 *
	 * @param bool $short_layout
	 *
	 * @return mixed|void
	 */
	function es_get_rating_markup( $rating, $short_layout = true ) {
		if ( strlen( $rating ) ) {
			if ( $rating ) {
				if ( $short_layout ) {
					$star = $rating ? '<span class="es-star es-star--small es-star--active"></span>' :
						'<span class="es-star es-star--small"></span>';
					$result = sprintf( '<div class="es-rating-num es-rating--small">%s %s / 5</div>', $star, $rating );
				} else {
					$result = '<div class="es-rating">';
					for ( $i = 1; $i <= 5; $i++ ) {
						$is_active = $rating >= $i ? 'es-star--active' : '';
						$result .= "<div data-value='{$i}' class='es-star {$is_active}'></div>";
					}
					$result .= '</div>';
				}

				return apply_filters( 'es_get_rating_markup', $result, $rating );
			}
		}
	}
}

if ( ! function_exists( 'es_get_contact_link' ) ) {

	/**
	 * @param $contact_config
	 * @param $entity
	 *
	 * @return mixed|void
	 */
	function es_get_contact_link( $contact_config, $entity ) {
		$result = null;

		switch ( $contact_config['type'] ) {
			case 'tel':
				$result = $contact_config['type'] . ':' . $contact_config['value'];
				break;

			case 'mail':
				$result = 'mailto:' . $contact_config['value'];
				break;

			case 'whatsapp':
				$result = sprintf( "https://wa.me/%s", preg_replace( '/[^\w\d\+]+/', "", $contact_config['value'] ) );
				break;
		}

		return apply_filters( '', $result, $contact_config, $entity );
	}
}

/**
 * Return comments template path.
 *
 * @return string
 */
function es_get_comments_template_path() {
	return es_locate_template( 'front/partials/comments.php' );
}

if ( ! function_exists( 'es_get_entity_tabs' ) ) {

	function es_get_entity_tabs( $entity_name ) {
		$sections_builder = es_get_sections_builder_instance();
		$tabs             = array();

		if ( $sections = $sections_builder::get_items( $entity_name ) ) {
			foreach ( $sections as $section_id => $section ) {
				if ( es_can_render_tab( $entity_name, $section_id ) ) {
					$tabs[ $section_id ] = array(
						'label' => $section['label'],
						'template' => es_locate_template( sprintf( 'admin/' . $entity_name . '/tabs/%s.php', $section_id ) ),
						'action'   => 'es_' . $entity_name . '_metabox_tab',
					);
				}
			}
		}

		return apply_filters( 'es_get_entity_tabs', $tabs, $entity_name );
	}
}

if ( ! function_exists( 'es_can_render_tab' ) ) {

	/**
	 * @param $entity_name
	 * @param $tab_id
	 *
	 * @return mixed|void
	 */
	function es_can_render_tab( $entity_name, $tab_id ) {
		$fields_builder = es_get_fields_builder_instance();
		if ( is_admin() ) {
			$fields = $fields_builder::get_tab_fields( $tab_id, $entity_name );
		} else {
			$fields = $fields_builder::get_frontend_tab_fields( $tab_id, $entity_name );
		}
		$can_render = ! empty( $fields ) && es_is_tab_active( $tab_id );

		return apply_filters( 'es_can_render_tab', $can_render, $tab_id, $entity_name );
	}
}

/**
 * @return mixed|void
 */
function es_get_featured_term_id() {
	$featured = ests( 'featured_term_id' );
	$featured_id = ! empty( $featured['default'] ) ? $featured['default'] : null;
	$featured_id = ! empty( $featured[ es_get_locale() ] ) ? $featured[ es_get_locale() ] : $featured_id;

	return apply_filters( 'es_get_featured_term_id', $featured_id );
}

/**
 * @param $is_new
 * @param $post_author_id
 *
 * @return string
 */
function es_get_saving_post_status( $is_new, $post_author_id ) {
	// If enabled approving submit listing by admin.
	if ( ests( 'manual_listing_approve' ) ) {
		$post_status = current_user_can( 'manage_options' ) ? 'publish' : 'draft';
	} else {
		$post_status = 'publish';
	}

	return apply_filters( 'es_get_saving_post_status', $post_status, $is_new, $post_author_id );
}

/**
 * @param $post_id
 *
 * @return false|mixed
 */
function et_builder_es_get_property_layout( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return false;
	}

	return get_post_meta( $post_id, '_et_pb_property_page_layout', true );
}

/**
 * @param $post_id
 *
 * @return bool
 */
function et_builder_is_enabled( $post_id ) {
	return get_post_meta( $post_id, '_et_pb_use_builder', true ) == 'on';
}

/**
 * @return string
 */
function es_et_builder_realtek_get_initial_property_content() {
	$content = '[et_pb_section admin_label="section"]
			[et_pb_row admin_label="row"]
				[et_pb_column type="4_4"][es_single_entity_page][/et_pb_column]
			[/et_pb_row]
		[/et_pb_section]';

	if ( ! empty( $args['existing_shortcode'] ) ) {
		$content = $content . $args['existing_shortcode'];
	}

	return $content;
}

/**
 * @return mixed|void
 */
function es_get_profile_tabs() {
	$tabs = array(
		'my-listings' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/my-listings.php' ),
			'label' => __( 'My listings', 'es' ),
			'icon' => "<span class='es-icon es-icon_home'></span>",
			'id' => 'my-listings',
		),
		'requests' => array(
			'template' => ! empty( $_GET['request_id'] ) ?
				es_locate_template( 'front/shortcodes/profile/tabs/single-request.php' ) : es_locate_template( 'front/shortcodes/profile/tabs/requests.php' ),
			'label' => __( 'Requests', 'es' ),
			'counter' => es_get_new_requests_count(),
			'icon' => "<span class='es-icon es-icon_mail'></span>",
			'id' => 'requests',
		),
		'saved-homes' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-homes.php' ),
			'label' => __( 'Saved homes', 'es' ),
			'icon' => "<span class='es-icon es-icon_heart'></span>",
			'id' => 'saved-homes',
		),
		'saved-searches' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-searches.php' ),
			'label' => __( 'Saved searches', 'es' ),
			'icon' => "<span class='es-icon es-icon_search'></span>",
			'id' => 'saved-searches',
		),
		'saved-agents' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-agents.php' ),
			'label' => __( 'Saved agents', 'es' ),
			'icon' => "<span class='es-icon es-icon_glasses'></span>",
			'id' => 'saved-agents',
		),
		'saved-agencies' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-agencies.php' ),
			'label' => __( 'Saved agencies', 'es' ),
			'icon' => "<span class='es-icon es-icon_case'></span>",
			'id' => 'saved-agencies',
		),
	);

	if ( ! current_user_can( 'agent' ) && ! current_user_can( 'administrator' ) ) {
		unset( $tabs['requests'] );
		unset( $tabs['my-listings'] );
	}

	if ( isset( $tabs['requests'] ) && ! ests( 'is_profile_requests_tab_enabled' ) ) {
		unset( $tabs['requests'] );
	}

	if ( ! ests( 'is_saved_search_enabled' ) ) {
		unset( $tabs['saved-searches'] );
	}

	if ( ! ests( 'is_properties_wishlist_enabled' ) ) {
		unset( $tabs['saved-homes'] );
	}

	if ( ! ests( 'is_agents_wishlist_enabled' ) || ! ests( 'is_agents_enabled' ) ) {
		unset( $tabs['saved-agents'] );
	}

	if ( ! ests( 'is_agencies_wishlist_enabled' ) || ! ests( 'is_agencies_enabled' ) ) {
		unset( $tabs['saved-agencies'] );
	}

	if ( ests( 'is_subscriptions_enabled' ) && current_user_can( 'agent' ) ) {
		$tabs['billing'] = array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/billing.php' ),
			'label' => __( 'Billing', 'es' ),
			'icon' => "<span class='es-icon es-icon_billing'></span>",
			'id' => 'billing',
		);
	}

	return apply_filters( 'es_profile_get_tabs', $tabs );
}
