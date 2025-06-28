<?php

/**
 * Customize property excerpt.
 *
 * @param $excerpt
 *
 * @return mixed
 */
function es_the_excerpt( $excerpt ) {
	$post = get_post( 0 );
	if ( $excerpt && $post instanceof WP_Post && $post->post_type == 'properties' ) {
		$excerpt = wp_trim_words( $excerpt, ests( 'excerpt_length' ), '...' );
	}
	return $excerpt;
}
add_filter( 'the_excerpt', 'es_the_excerpt' );

if ( ! function_exists( 'es_search_render_field' ) ) {

	/**
	 * Render advanced search field.
	 *
	 * @param $field
	 * @param array $attributes
	 * @param null $force_type
	 */
	function es_search_render_field( $field, $attributes = array(), $force_type = null ) {
		$attributes = es_parse_args( $attributes, array( 'entity' => 'property' ) );
		$field_config = es_search_get_field_config( $field, $attributes['entity'] );
		if ( $field_config && ! empty( $field_config['search_support'] ) ) {
			$search_settings = $field_config['search_settings'];
			$type = $force_type ? $force_type : $search_settings['type'];
			$uid = uniqid();
			$selected_value = isset( $attributes[ $field ] ) ? $attributes[ $field ] : null;
			$selected_value = isset( $_GET[ $field ] ) ? es_clean( $_GET[ $field ] ) : $selected_value;

			if ( empty( $search_settings['values'] ) && ! empty( $search_settings['values_callback'] ) ) {
				if ( ! empty( $search_settings['values_callback']['args'] ) ) {
					$values = call_user_func_array( $search_settings['values_callback']['callback'], $search_settings['values_callback']['args'] );
				} else {
					$values = call_user_func( $search_settings['values_callback']['callback'] );
				}

				if ( $values && ! is_wp_error( $values ) ) {
					$search_settings['values'] = $values;
				}
			}

			$field_html = null;

			switch ( $type ) {
				case 'price':
					$values = array();
					if ( ests( 'is_same_price_for_categories_enabled' ) ) {
						$values['min'] = ests( 'min_prices_list' ) ? explode( ',', ests( 'min_prices_list' ) ) : array();
						$values['max'] = ests( 'max_prices_list' ) ? explode( ',', ests( 'max_prices_list' ) ) : array();

						$values['min'] = array_combine( $values['min'], $values['min'] );
						$values['max'] = array_combine( $values['max'], $values['max'] );

						$prices_list = array();
					} else {
						if ( $prices_list = ests( 'custom_prices_list' ) ) {
							$formatter = $field_config['formatter'];
							foreach ( $prices_list as $k => $price_item ) {
								if ( empty( $price_item['type'] ) && empty( $price_item['category'] ) ) {
									$values['min'] = explode( ',', $price_item['min_prices_list'] );
									$values['max'] = explode( ',', $price_item['max_prices_list'] );
								}

								$min_values = explode( ',', $price_item['min_prices_list'] );
								$max_values = explode( ',', $price_item['max_prices_list'] );

								if ( ! empty( $min_values ) ) {
									$prices_list[ $k ]['min_prices_list'] = array_combine( $min_values, es_format_values( $min_values, $formatter ) );
								}

								if ( ! empty( $max_values ) ) {
									$prices_list[ $k ]['max_prices_list'] = array_combine( $max_values, es_format_values( $max_values, $formatter ) );
								}
							}
						}
					}

					$field_html = "<div class='es-field-row es-field-row__range js-search-field-container'>";

					foreach ( array( 'min', 'max' ) as $field_range ) {
						if ( ! empty( $values[ $field_range ] ) ) {
							$values[ $field_range ] = array_combine( $values[ $field_range ], es_format_values( $values[ $field_range ], $field_config['formatter'] ) );
						}
						$range_label = ! empty( $search_settings['range_label'] ) ? $search_settings['range_label'] : $field_config['label'];
						$field_name = $field_range . '_' . $field;
						$value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
						$value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $value;

						$config = array(
							'type' => ! empty( $values[ $field_range ] ) ? 'select' : 'number',
							'label' => $field_range == 'min' ? $range_label : false,
							'value' => $value,
							'attributes' => array(
								'data-prices-list' => es_esc_json_attr( $prices_list ),
								'id' => sprintf( '%s-%s-%s', $field, $field_range, $uid ),
								'class' => 'js-es-search-field js-es-search-field--price ' . sprintf( 'js-es-search-field--price-%s', $field_range ),
								'data-base-name' => $field,
								'data-placeholder' => $field_range == 'min' ? __( 'Min', 'es' ) : __( 'Max', 'es' ),
							),
							'options' => ! empty( $values[ $field_range ] ) ? array( '' => '' ) + $values[ $field_range ] : array(),
						);

						$field_html .= es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
					}
					$field_html .= "</div>";
					$search_settings['range'] = false;

					break;
				case 'select':
				case 'list':
				case 'dropdown':
					$search_settings['values'] = es_format_values( $search_settings['values'], $field_config['formatter'] );
					$values = $search_settings['values'];

					if ( ! empty( $search_settings['attributes']['data-placeholder'] ) ) {
						$values = array( '' => '' ) + $values;
					}

					if ( 'keywords' == $field && $selected_value ) {
						$values = array_combine( $selected_value, $selected_value );
					}

//                    if ( ! $search_settings['attributes']['multiple'] ) {
//                        $values = array( '' => _x( 'All', 'search dropdown placeholder', 'es' ) ) + $values;
//                    }

					$config = array(
						'type'       => $type,
						'options'    => $values,
						'value' => $selected_value,
						'attributes' => array(
							'id' => sprintf( '%s-%s', $field, $uid ),
							'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
							'data-base-name' => $field,
						),
						'label' => ! empty( $field_config['label'] ) ? $field_config['label'] : '',
					);

					if ( ! empty( $selected_value ) ) {
						if ( is_scalar( $selected_value ) ) {
							$config['attributes']['data-value'] = $selected_value;
						} else if ( is_array( $selected_value ) ) {
							$config['attributes']['data-value'] = es_esc_json_attr( $selected_value );
						}
					}

					$search_settings['wrapper_class'] .= ' js-search-field-container';
					$field_html = es_framework_get_field_html( $field, es_parse_args( $config, $search_settings ) );
					break;

				case 'checkboxes':
					if ( ! empty( $search_settings['values'] ) ) {
						$values = es_format_values( $search_settings['values'], $field_config['formatter'] );
						$visible_items = ! empty( $search_settings['visible_items'] ) ? $search_settings['visible_items'] : false;

						$config = array(
							'type'       => $type,
							'options'    => $values,
							'disable_hidden_input' => true,
							'value' => $selected_value,
							'visible_items' => $visible_items,
							'button_label' => ! empty( $search_settings['show_more_label'] ) ? $search_settings['show_more_label'] : '',
							'attributes' => array(
								'id' => sprintf( '%s-%s', $field, $uid ),
								'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
								'data-base-name' => $field,
							),
							'label'      => $field_config['label'],
						);

						$search_settings['wrapper_class'] .= ' js-search-field-container';
						$field_html = es_framework_get_field_html( $field, es_parse_args( $config, $search_settings ) );
					}
					break;

				case 'radio-bordered':
				case 'checkboxes-bordered':
				case 'checkboxes-boxed':
					if ( ! empty( $search_settings['values'] ) ) {
						$options = $search_settings['values'];
						$field_name = $field;
						$field_class = sprintf( 'js-es-search-field js-es-search-field--%s', $field_name );

						if ( in_array( $field, array( 'bedrooms', 'bathrooms', 'half_baths' ) ) ) {
							array_walk( $search_settings['values'], 'es_arr_add_suffix_plus' );
							$options = array( '' => __( 'Any', 'es' ) ) + $search_settings['values'];
							$field_name = 'from_' . $field;
							$selected_value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
							$selected_value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $selected_value;
						}

						$config = array(
							'type' => $type,
							'options' => $options,
							'label' => $field_config['label'],
							'value' => $selected_value,
							'disable_hidden_input' => true,
							'attributes' => array(
								'id' => sprintf( '%s-%s', $field_name, $uid ),
								'class' => $field_class,
								'data-formatter' => $field_config['formatter'],
								'data-base-name' => $field,
							),
						);
						$search_settings['wrapper_class'] .= ' js-search-field-container';
						$field_html = es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
					}
					break;

				case 'range':
					$field_html = "<div class='es-field-row es-field-row__range js-search-field-container'>";
					foreach ( array( 'min', 'max' ) as $field_range ) {
						$range_label = ! empty( $search_settings['range_label'] ) ? $search_settings['range_label'] : $field_config['label'];
						$values = ! empty( $search_settings['values_' . $field_range] ) ? $search_settings['values_' . $field_range] : array();
						$values = es_format_values( $values, $field_config['formatter'] );
						$field_name = $field_range . '_' . $field;
						$selected_value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
						$selected_value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $selected_value;
						$config = array(
							'type' => $values ? 'select' : 'number',
							'label' => $field_range == 'min' ? $range_label : false,
							'value' => $selected_value,
							'attributes' => array(
								'id' => sprintf( '%s-%s-%s', $field, $field_range, $uid ),
								'min' => ests( 'search_min_' . $field ),
								'max' => ests( 'search_max_' . $field ),
								'data-formatter' => $field_config['formatter'],
								'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
								'data-base-name' => $field,
								'data-placeholder' => $field_range == 'min' ? __( 'No min', 'es' ) : __( 'No max', 'es' ),
								'placeholder' => $field_range == 'min' ? __( 'No min', 'es' ) : __( 'No max', 'es' ),
							),
							'options' => array( '' => '' ) + $values,
						);

						$field_html .= es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
					}
					$field_html .= "</div>";
					break;
				default:
					$search_settings['wrapper_class'] .= ' js-search-field-container';
					$field_config = es_array_merge_recursive( $field_config, $search_settings );
					$field_config['value'] = $selected_value;
					$field_html = es_framework_get_field_html( $field, $field_config );
			}

			if ( ! empty( $field_html ) || ( ! empty( $attributes['type'] ) && $attributes['type'] == 'range' ) ) {
				echo apply_filters( 'es_search_render_field_html', $field_html, $field, $attributes, $force_type );
			}

			if ( ! empty( $search_settings['range'] ) &&  $type != 'range' ) {
				$field_config['type'] = 'range';
				$field_config['search_settings']['type'] = 'range';
				es_search_render_field( $field, $field_config, 'range' );
			}
		}
	}
}
add_action( 'es_search_render_field', 'es_search_render_field', 10, 2 );

/**
 * @param WP_Admin_Bar $admin_bar
 */
function es_admin_bar_edit_property_link( $admin_bar ) {

	if ( es_is_single_property() && current_user_can( 'edit_post', get_the_ID() ) ) {
		global $wp_query;
		$admin_bar->add_menu( array(
			'id'    => 'edit-property',
			'title' => __( 'Edit property', 'es' ),
			'href'  => get_edit_post_link( $wp_query->post->ID ),
			'meta'  => array(
				'title' => __( 'Edit property', 'es' ),
			),
		) );
	}

	if ( es_is_single_agent() && current_user_can( 'edit_post', get_the_ID() ) ) {
		global $wp_query;
		$admin_bar->add_menu( array(
			'id'    => 'edit-agent',
			'title' => __( 'Edit agent', 'es' ),
			'href'  => get_edit_post_link( $wp_query->post->ID ),
			'meta'  => array(
				'title' => __( 'Edit agent', 'es' ),
			),
		) );
	}

	if ( es_is_single_agency() && current_user_can( 'edit_post', get_the_ID() ) ) {
		global $wp_query;
		$admin_bar->add_menu( array(
			'id'    => 'edit-agency',
			'title' => __( 'Edit agency', 'es' ),
			'href'  => get_edit_post_link( $wp_query->post->ID ),
			'meta'  => array(
				'title' => __( 'Edit agency', 'es' ),
			),
		) );
	}
}
add_action( 'admin_bar_menu', 'es_admin_bar_edit_property_link', 100 );

if ( ! function_exists( 'es_privacy_policy' ) ) {

	/**
	 * Display terms & conditions text / checkbox.
	 *
	 * @param $context
	 */
	function es_privacy_policy( $context ) {
		$content = null;
		$terms_forms = ests( 'terms_forms' );
		$terms_conditions_page_id = ests( 'terms_conditions_page_id' );
		$privacy_policy_page_id = ests( 'privacy_policy_page_id' );

		if ( $terms_forms && is_array( $terms_forms ) && in_array( $context, $terms_forms ) ) {

			$terms = __( 'Terms of Use', 'es' );
			$policy = __( 'Privacy Policy', 'es' );

			if ( $terms_conditions_page_id && get_post_status( $terms_conditions_page_id ) == 'publish' ) {
				$terms = "<a href='" . esc_url( get_permalink( $terms_conditions_page_id ) ) . "' class='es-terms-link'>{$terms}</a>";
			}

			if ( $privacy_policy_page_id && get_post_status( $privacy_policy_page_id ) == 'publish' ) {
				$policy = "<a href='" . esc_url( get_permalink( $privacy_policy_page_id ) ) . "' class='es-terms-link'>{$policy}</a>";
			}

			$context_label = null;

			if ( $context == 'comment_form' ) {
				$context_label = _x( 'SUBMIT YOUR REVIEW', 'terms & conditions', 'es' );
			}

			if ( $context == 'request_form' ) {
				$context_label = _x( 'REQUEST INFO', 'terms & conditions', 'es' );
			}

			if ( $context == 'sign_up_form' ) {
				$context_label = _x( 'SIGN UP', 'terms & conditions', 'es' );
			}

			$context_label = apply_filters( 'es_es_privacy_policy_button_label', $context_label, $content );

			$content = sprintf( __( 'By clicking the %s button you agree to the %s and %s', 'es' ), '«' . $context_label . '»', $terms, $policy );
			$content = "<div class='es-terms-text'>{$content}</div>";

			if ( 'checkbox' == ests( 'terms_input_type' ) ) {
				$content = sprintf( __( 'I agree to the %s and %s', 'es' ), $terms, $policy );
				$content = "<div class='es-terms-text'>{$content}</div>";
				$content = es_framework_get_field_html( 'terms_conditions', array(
						'type' => 'checkbox',
						'attributes' => array(
							'required' => 'required',
							'id' => 'terms-conditions-' . uniqid()
						),
					) ) . $content;
			}
		}

		if ( ! empty( $content ) ) {
			$content = "<div class='es-privacy-policy-container'>{$content}</div>";
		}

		echo apply_filters( 'es_privacy_policy_content', $content, $context );
	}
}
add_action( 'es_privacy_policy', 'es_privacy_policy', 10, 1 );

/**
 * @param $entity
 */
function es_enqueue_entity_recaptcha( $entity ) {
	if ( in_array( $entity, array( 'agent', 'agency' ) ) && es_is_entity_contact_btn_enabled( $entity ) ) {
		$forms = ests( 'recaptcha_forms' );

		if ( in_array( 'request_form', $forms ) ) {
			es_enqueue_recaptcha();
		}
	}
}
add_action( 'es_before_entities', 'es_enqueue_entity_recaptcha', 10, 1 );

/**
 * @return bool
 */
function es_enqueue_recaptcha() {
	if ( ! wp_script_is( 'es-google-recaptcha' ) ) {
		$site_key = ests( 'recaptcha_site_key' );
		$site_secret = ests( 'recaptcha_secret_key' );

		if ( $site_key && $site_secret ) {
			$lang = es_get_locale();
			$recaptcha_version = ests( 'recaptcha_version' );

			$url = 'https://www.google.com/recaptcha/api.js';
			$args = array( 'hl' => $lang );

			if ( 'v3' == $recaptcha_version ) {
				$args['render'] = $site_key;
			}

			if ( 'v2' == $recaptcha_version ) {
				$args['onload'] = 'es_initialize_recaptcha';
			}

			wp_enqueue_script( 'es-google-recaptcha', add_query_arg( $args, $url ), array( 'es-frontend' ) );

			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

if ( ! function_exists( 'es_recaptcha' ) ) {

	/**
	 * @param $context
	 */
	function es_recaptcha( $context = 'basic' ) {
		$forms = ests( 'recaptcha_forms' );

		if ( ! $context || ( is_array( $forms ) && in_array( $context, $forms ) ) ) {
			$enqueued = es_enqueue_recaptcha();

			if ( $enqueued ) {
				$recaptcha_version = ests( 'recaptcha_version' );
				$siteKey = ests( 'recaptcha_site_key' );
				$uid = uniqid();

				if ( 'v3' == $recaptcha_version ) { ?>
					<input type="hidden" name="g-recaptcha-response" id="recaptchaResponse-<?php echo $uid; ?>"/>
					<?php
				} else if ( 'v2' == $recaptcha_version ) : ?>
					<div class="es-recaptcha-wrapper">
						<div class="js-g-recaptcha" id="g-recaptcha-<?php echo $uid; ?>"></div>
					</div>
				<?php endif;

				if ( 'v3' == $recaptcha_version ) {
					wp_add_inline_script( 'es-google-recaptcha', "
					(function(){
					    var interval = setInterval(function(){
                            if ( window.grecaptcha ) {
                                window.grecaptcha.ready(function () {
                                    if ( document.getElementById('recaptchaResponse-" . $uid . "') ) {
                                        window.grecaptcha.execute('" . $siteKey . "', { action: '" . $context . "' }).then(function (token) {
                                            var recaptchaResponse = document.getElementById('recaptchaResponse-" . $uid . "');
                                            recaptchaResponse.value = token;
                                        });
                                    }
                                });
                                clearInterval(interval);
                            }
                        });
					})();    
                    ");
				}
			}
		}
	}
}
add_action( 'es_recaptcha', 'es_recaptcha' );

/**
 * Add sorting labels to the settings array.
 *
 * @param $values array
 * @param $name string Setting name.
 *
 * @return mixed
 */
function es_add_sort_labels_settings( $values, $name ) {
	if ( 'properties_sorting_options' == $name || 'properties_default_sorting_option' == $name ) {
		$labels = es_get_terms_list( 'es_label', false, array(), 'all' );

		if ( $labels && ! is_wp_error( $labels ) ) {
			foreach ( $labels as $label ) {
				$values[ $label->slug ] = $label->name;
			}
		}
	}

	return $values;
}
add_filter( 'es_settings_get_available_values', 'es_add_sort_labels_settings', 10, 2 );

/**
 * Display login / register popup.
 *
 * @return void
 */
function es_authentication_popup() {
	es_load_template( 'front/popup/authentication.php' );
}
add_action( 'wp_footer', 'es_authentication_popup' );

/**
 * @param $post_id int
 * @param $post WP_Post
 */
function es_set_settings_pages_ids( $post_id, $post ) {
	if ( ! ests( 'profile_page_id' ) && ! empty( $post->post_content ) && stristr( $post->post_content, '[es_profile' ) !== false ) {
		ests_save_option( 'profile_page_id', $post_id );
	}

	if ( ! ests( 'pricing_page_id' ) && ! empty( $post->post_content ) && stristr( $post->post_content, '[es_subscription' ) !== false ) {
		ests_save_option( 'pricing_page_id', $post_id );
	}

	if ( ! ests( 'search_results_page_id' ) && ! empty( $post->post_content ) && stristr( $post->post_content, 'ignore_search="0"' ) !== false ) {
		ests_save_option( 'search_results_page_id', $post_id );
	}

	if ( ! ests( 'map_search_page_id' ) && ! empty( $post->post_content ) && stristr( $post->post_content, 'layout="half_map"' ) !== false ) {
		ests_save_option( 'map_search_page_id', $post_id );
	}

	if ( ! ests( 'property_management_page_id' ) && ! empty( $post->post_content ) && stristr( $post->post_content, '[property_management"' ) !== false ) {
		ests_save_option( 'property_management_page_id', $post_id );
	}
}
add_action( 'save_post_page', 'es_set_settings_pages_ids', 10, 2 );

/**
 * @param $avatar
 * @param $id_or_email
 * @param $size
 * @param $default
 * @param $alt
 *
 * @return string
 */
function es_get_avatar( $avatar, $id_or_email, $args ) {
	$user = false;
	$size = $args['size'];

	if ( is_numeric( $id_or_email ) ) {

		$id = (int) $id_or_email;
		$user = get_user_by( 'id' , $id );

	} elseif ( is_object( $id_or_email ) ) {

		if ( ! empty( $id_or_email->user_id ) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_user_by( 'id' , $id );
		}

	} else {
		$user = get_user_by( 'email', $id_or_email );
	}

	if ( $user && is_object( $user ) && ( $entity = es_get_user_entity( $user->ID ) ) ) {
		$attachment_id = $entity->avatar_id;
		if ( $attachment_id ) {
			$src = wp_get_attachment_url( $attachment_id );
		} else {
			$src = esc_attr( es_user_get_default_image_url( $user->ID ) );
		}

		$avatar = "<img alt='{$args['alt']}' src='{$src}' class='avatar avatar-{$size}' width='{$size}' height='{$size}'/>";
	}

	return $avatar;
}
add_action( 'pre_get_avatar', 'es_get_avatar', 9999999, 3 );

/**
 * @param $states array
 * @param $post WP_Post
 *
 * @return mixed
 */
function es_display_post_states( $states, $post ) {
	$pages = array(
		'login_page_id' => __( 'Realtek Authentication', 'es' ),
		'profile_page_id' => __( 'Realtek Profile', 'es' ),
		'map_search_page_id' => __( 'Realtek Half map', 'es' ),
		'search_results_page_id' => __( 'Realtek Search results', 'es' ),
		'property_management_page_id' => __( 'Realtek Properties Management', 'es' ),
		'pricing_page_id' => __( 'Realtek Pricing', 'es' ),
	);

	foreach ( $pages as $key => $label ) {
		$page_id = ests( $key );
		if ( $page_id && get_post_status( $page_id ) == 'publish' && $post->ID == $page_id ) {
			$states[ $key ] = $label;
		}
	}

	return $states;
}
add_filter( 'display_post_states', 'es_display_post_states', 10, 2 );

/**
 * Display dynamic content on single property page.
 *
 * @return void
 */
function es_render_dynamic_content() {
	if ( ests( 'is_dynamic_content_enabled' ) && ests( 'dynamic_content' ) ) {
		do_action( 'es_before_dynamic_content' ); ?>
		<div class='es-dymanic-content content-font'>
		<?php echo do_shortcode( strtr( stripslashes( ests( 'dynamic_content' ) ), array(
			'{blog_name}' => get_bloginfo( 'name' )
		) ) );
		?>
		</div><?php
		do_action( 'es_after_dynamic_content' );
	}
}
add_action( 'es_after_single_property_content', 'es_render_dynamic_content', 1 );

if ( ! function_exists( 'es_powered_by' ) ) {

	/**
	 * Render powered by.
	 *
	 * @return void
	 */
	function es_powered_by() {
		if ( ! ests( 'is_white_label_enabled' ) ) {
			echo "<div class='es-powered content-font'>" . sprintf( __( 'Powered by %s' ), "<a target='_blank' href='https://realtek.net'>" . __( 'Realtek', 'es' ) . "</a>" ) . "</div>";
		}
	}
}
add_action( 'es_after_listings', 'es_powered_by' );
add_action( 'es_after_single_content', 'es_powered_by' );
add_action( 'es_after_authentication', 'es_powered_by' );
add_action( 'es_after_profile', 'es_powered_by' );

if ( ! function_exists( 'es_login_logo' ) ) {

	/**
	 * Add custom logo on wp login page.
	 *
	 * @return void
	 */
	function es_login_logo() {
		if ( ests( 'logo_attachment_id' ) && ( $url = wp_get_attachment_image_url( ests( 'logo_attachment_id' ), 'medium' ) ) ) : ?>
			<style type="text/css">
                #login h1 a, .login h1 a {
                    background-image: url(<?php echo $url; ?>);
                    height:150px;
                    width:150px;
                    background-size: contain;
                    background-repeat: no-repeat;
                    padding-bottom: 30px;
                }
			</style>
		<?php endif; }
}
add_action( 'login_enqueue_scripts', 'es_login_logo' );

/**
 * Change term link for location taxonomy.
 *
 * @param $url
 * @param $term
 * @param $taxonomy
 * @return string|string[]
 */
function es_location_term_permalink( $url, $term, $taxonomy ) {
	if ( 'es_location' == $taxonomy ) {
		$location_type = get_term_meta( $term->term_id, 'type', true );
		$taxonomy = get_taxonomy( $taxonomy );
		// If is state.
		if ( 'administrative_area_level_1' == $location_type && ests( 'state_slug' ) ) {
			$url = str_replace('/' . $taxonomy->rewrite['slug'], '/' . ests( 'state_slug' ), $url);
		} else if ( 'locality' == $location_type && ests( 'city_slug' ) ) {
			$url = str_replace('/' . $taxonomy->rewrite['slug'], '/' . ests( 'city_slug' ), $url);
		}
	}

	return $url;
}
add_filter( 'term_link', 'es_location_term_permalink', 10, 3 );

/**
 * Change location taxonomy request for cities and states.
 *
 * @param $query
 * @return mixed
 */
function es_location_term_request( $query ) {
	$state_slug = ests( 'state_slug' );
	$city_slug = ests( 'city_slug' );

	if ( ( ! empty( $_SERVER['REQUEST_URI'] ) &&
	       ( ( $state_slug && stristr( $_SERVER['REQUEST_URI'], $state_slug ) ) ||
	         ( $city_slug  && stristr( $_SERVER['REQUEST_URI'], $city_slug ) ) ) ) || empty( $_SERVER['REQUEST_URI'] ) ) {
		if ( ! empty( $query['name'] ) ) {
			$name = $query['name'];

			if ( $name && term_exists( $name, 'es_location' ) ) {
				$query['es_location'] = $name;
				unset( $query['name'] );
			}
		} else if ( ! empty( $query['attachment'] ) ) {
			$name = $query['attachment'];

			if ( $name && term_exists( $name, 'es_location' ) ) {
				$query['es_location'] = $name;
				unset( $query['attachment'] );
			}
		} else if ( ! empty( $query['pagename'] ) ) {
			if ( ( $state_slug && stristr( $query['pagename'], $state_slug ) ) ||
			     ( $city_slug && stristr( $query['pagename'], $state_slug ) ) ) {
				$name = explode( '/', $query['pagename'] );
				$name = $name[ count( $name ) - 1 ];

				if ( $name && term_exists( $name, 'es_location' ) ) {
					$query['es_location'] = $name;
					$query = array();
				}
			}
		}
	}

	return $query;
}
add_filter( 'request', 'es_location_term_request', 1, 1 );

/**
 * Disable featured image for property single page.
 *
 * @param $html
 * @param $image_post
 * @return string
 */
function es_disable_single_featured_image( $html, $image_post ) {
	$image_post = get_post( $image_post );

	return $image_post && is_singular( 'properties' ) && $image_post->post_type == 'properties' &&
	       $image_post->ID == get_the_ID() ? '' : $html;
}
add_filter( 'post_thumbnail_html', 'es_disable_single_featured_image', 10, 2 );

/**
 * Delete children locations.
 *
 * @param $term
 * @param $taxonomy
 */
function es_delete_children_locations( $term, $taxonomy ) {
	if ( 'es_location' == $taxonomy ) {
		/** @var Int[] $children_terms */
		$children_terms = es_get_children_locations( $term, $taxonomy );

		if ( $children_terms ) {
			remove_filter( 'pre_delete_term', 'es_delete_children_locations', 10 );

			foreach ( $children_terms as $child_term ) {
				if ( $child_term == $term ) continue;
				wp_delete_term( $child_term, $taxonomy );
			}
		}
	}
}
add_action( 'pre_delete_term', 'es_delete_children_locations', 10, 2 );

/**
 * Prepare date and date-time values for getting.
 *
 * @param $value
 * @param $field
 *
 * @return mixed
 */
function es_property_alter_get_field_value( $value, $field ) {
	$field_info = es_property_get_field_info( $field );

	if ( ! empty( $field_info['type'] ) && in_array( $field_info['type'], array( 'date', 'date-time' ) ) && $value ) {
		$format = $field_info['attributes']['data-date-format'];
		$value = date( $format, $value );
	}

	return $value;
}
add_filter( 'es_property_get_field_value', 'es_property_alter_get_field_value', 10, 2 );

/**
 * Prepare date and date-time values for saving.
 *
 * @param $value
 * @param $field
 * @return mixed
 */
function es_property_alter_save_field_value( $value, $field ) {
	$field_info = es_property_get_field_info( $field );

	if ( ! empty( $field_info['type'] ) && in_array( $field_info['type'], array( 'date', 'date-time' ) ) && $value ) {
		$format = $field_info['attributes']['data-date-format'];

		$value = DateTime::createFromFormat( $format, $value );

		if ( $field_info['type'] == 'date' && $value ) {
			$value->setTime( 0, 0, 0 );
		}

		$value = $value instanceof DateTime ? $value->getTimestamp() : null;
	}

	return $value;
}
add_filter( 'es_property_save_field_value', 'es_property_alter_save_field_value', 10, 2 );

/**
 * Generate search keywords after property save action.
 *
 * @param $data
 * @param $entity Es_Entity
 */
function es_request_generate_keywords( $data, $entity ) {
	$post_id = $entity->get_id();
	$property = es_get_request( $post_id );
	$keywords_fields = apply_filters( 'es_request_keywords_fields', array( 'post_excerpt', 'post_title', 'ID', 'tel', 'email' ) );
	$property->delete_field_value( 'keywords' );

	foreach ( $keywords_fields as $field ) {
		if ( $value = $property->{$field} ) {
			if ( 'tel' == $field ) {
				$value = es_get_formatted_tel( $value );
			}
			add_post_meta( $post_id, 'es_request_keywords', $value, false );
		}
	}
}
add_action( 'es_request_after_save_fields', 'es_request_generate_keywords', 10, 2 );

/**
 * Generate search keywords after property save action.
 *
 * @param $data
 * @param $entity Es_Entity
 */
function es_property_generate_keywords( $data, $entity ) {
	$post_id = $entity->get_id();
	$property = es_get_property( $post_id );
	$keywords_fields = apply_filters( 'es_property_keywords_fields', array( 'post_title', 'address', 'ID' ) );
	$property->delete_field_value( 'keywords' );

	foreach ( $keywords_fields as $field ) {
		if ( $value = $property->{$field} ) {
			add_post_meta( $post_id, 'es_property_keywords', $value, false );
		}
	}
}
add_action( 'es_property_after_save_fields', 'es_property_generate_keywords', 10, 2 );

/**
 * @param $data
 * @param $entity Es_Order
 */
function es_order_generate_keywords( $data, $entity ) {
	$post_id = $entity->get_id();

	if ( ! empty( $entity->user_id ) ) {
		$user = get_user_by( 'ID', $entity->user_id );
		$entity->delete_field_value( 'keywords' );
		$user_entity = es_get_user_entity( $user->ID );

		if ( $user instanceof WP_User ) {
			add_post_meta( $post_id, 'es_order_keywords', $user->user_email, false );
			add_post_meta( $post_id, 'es_order_keywords', $user->user_email, false );
			add_post_meta( $post_id, 'es_order_keywords', $user->user_nicename, false );
			add_post_meta( $post_id, 'es_order_keywords', $user->ID, false );

			if ( $user_entity instanceof Es_Agent_User ) {
				$agent = $user_entity->get_post_entity();
				if ( $agent && $agent->post_title ) {
					add_post_meta( 'es_order_keywords', $user->ID, $agent->post_title );
				}
			}

			if ( $user_entity instanceof Es_User && $user_entity->get_full_name() ) {
				add_post_meta( 'es_order_keywords', $user->ID, $user_entity->get_full_name() );
			}

		}
	}
}
add_action( 'es_after_save_order', 'es_order_generate_keywords', 10, 2 );

/**
 * @param $data
 * @param $entity
 */
function es_agent_agency_generate_keywords( $data, $entity ) {
	$post_id = $entity->get_id();
	$entity = es_get_entity_by_id( $post_id );
	$keywords_fields = apply_filters( 'es_' . $entity::get_post_type_name() . '_keywords_fields', array( 'post_title', 'ID', 'email', 'user_email', 'first_name', 'last_name' ) );
	$entity->delete_field_value( 'keywords' );

	foreach ( $keywords_fields as $field ) {
		if ( $value = $entity->{$field} ) {
			add_post_meta( $post_id, $entity->get_entity_prefix() . 'keywords', $value, false );
		}
	}
}
add_action( 'es_agent_after_save_fields', 'es_agent_agency_generate_keywords', 10, 2 );
add_action( 'es_agency_after_save_fields', 'es_agent_agency_generate_keywords', 10, 2 );

/**
 * Auto tags function.
 *
 * @param $post_id
 * @param $post
 */
function es_generate_property_tags( $post_id ) {
	$post = get_post( $post_id );

	if ( ! empty( $post->post_content ) && ests( 'is_auto_tags_enabled' ) ) {

		$append_tags = array();

		$tags = get_terms( array(
			'taxonomy' => 'es_tag',
			'hide_empty' => false,
			'fields' => 'id=>name',
		) );

		if ( ! empty( $tags ) ) {
			foreach ( $tags as $id => $tag ) {
				if ( stristr( $post->post_content, $tag ) ) {
					$append_tags[] = $id;
				}
			}
		}

		if ( ! empty( $append_tags ) ) {
			wp_set_post_terms( $post_id, $append_tags, 'es_tag', true );
		}
	}
}
add_action( 'save_post_properties', 'es_generate_property_tags', 10 );

/**
 * @param $value
 * @param $field
 * @return mixed
 */
function es_get_the_formatter_post_content( $value, $field ) {
	if ( 'post_content' == $field ) {
		if ( ests( 'is_auto_tags_enabled' ) && ests( 'is_clickable_tags_enabled' ) ) {
			$tags = get_terms( array(
				'taxonomy' => 'es_tag',
				'hide_empty' => true,
				'fields' => 'id=>name',
			) );
			$replace = array();

			/** @var $tags string[] */
			if ( ! empty( $tags ) ) {
				foreach ( $tags as $id => $tag ) {
					$replace[ $tag ] = "<a href='" . get_term_link( $id, 'es_tag' ) . "'>{$tag}</a>";
				}
			}

			if ( $replace ) {
				$value = strtr( $value, $replace );
			}
		}
	}

	return $value;
}
add_filter( 'es_get_the_formatted_field', 'es_get_the_formatter_post_content', 10, 2 );

/**
 * Delete property attachments.
 *
 * @param $post_id
 */
function es_property_delete_attachments( $post_id ) {
	if ( es_is_property( $post_id ) ) {
		$attachments = get_children( array(
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'fields' => 'ids',
		) );

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
		}
	}
}
add_action( 'before_delete_post', 'es_property_delete_attachments', 10, 1 );

/**
 * @param $post_id
 */
function es_agent_delete_related_entity( $post_id ) {
	if ( es_is_agent( $post_id ) ) {
		$agent = es_get_agent( $post_id );
		if ( $related = $agent->get_user_entity() ) {
			$related->delete( true );
		}
	}
}
add_action( 'before_delete_post', 'es_agent_delete_related_entity', 10, 1 );

/**
 * @param $field_config
 * @param $field_key
 * @param $entity Es_Agent_Post
 *
 * @return array
 */
function es_entity_form_password_field_config( $field_config, $field_key, $entity ) {
	if ( 'user_password' == $field_key ) {
		global $post;
		$post_id = sanitize_key( filter_input( INPUT_GET, 'post' ) );
		if ( $entity->get_id() && $post_id == $entity->get_id() ) {
			unset( $field_config['description'] );
			if ( $entity->has_user_entity() ) {
				$field_config['attributes']['class'] .= ' js-es-user-exists';
			}
		} else {
			$field_config['attributes']['required'] = 'required';
			$field_config['label'] .= ' *';
		}
	}

	return $field_config;
}
add_filter( 'es_entity_form_field_config', 'es_entity_form_password_field_config', 10, 3 );

/**
 * @param string $entity_type
 *
 * @return void
 */
function es_reviews_link( $entity_type = 'agent' ) {
	if ( ests( 'is_' . $entity_type . '_comments_enabled' ) && comments_open() ) : ?>
		<?php if ( $comments_num = get_comments_number() ) : ?>
			<a href="<?php echo add_query_arg( 'form', 'review', get_the_permalink() ); ?>" class="es-reviews-link">
				<?php printf( _n( '%s review', '%s reviews', $comments_num, 'es' ), $comments_num ); ?>
			</a>
		<?php else : ?>
			<?php if ( ests( 'is_' . $entity_type . '_commenting_enabled' ) ) : ?>
				<a href="<?php echo add_query_arg( 'form', 'review', get_the_permalink() ); ?>" class="es-reviews-link"><?php _e( 'Write a review', 'es' ); ?></a>
			<?php else : ?>
				<span class="es-reviews-link"><?php _e( 'No reviews', 'es' ); ?></span>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif;
}
add_action( 'es_reviews_link', 'es_reviews_link' );

/**
 * @param $entity_id
 */
function es_stats_counter( $entity_id ) {
	$entity = es_get_entity_by_id( $entity_id ); ?>
	<div class="es-stats">
	<div class="es-stats__item es-stats__item--active">
		<b><?php echo $entity->get_property_qty(); ?></b>
		<span><a href="<?php echo es_get_the_permalink(); ?>#listings">
                <?php echo _n( 'property', 'properties', $entity->get_property_qty(), 'es' ); ?></a></span>
	</div>
	</div><?php
}
add_action( 'es_stats_counter', 'es_stats_counter' );

/**
 * @param $counts
 * @param $type
 *
 * @return mixed
 */
function es_count_posts( $counts, $type ) {
	if ( 'agent' == $type ) {
		global $wpdb;
		$counts->all = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='%s'", array( $type ) ) );
	}
	return $counts;
}
add_filter( 'wp_count_posts', 'es_count_posts', 10, 2 );

/**
 * @param $post_id
 */
function es_reviews_form( $post_id ) {
	$shortcode = es_get_shortcode_instance( 'es_agent_agency_review_form', array( 'entity_id' => $post_id ) );

	echo $shortcode->get_content();

	add_filter( 'comments_open', '__return_false' );
}
add_action( 'es_reviews_form', 'es_reviews_form' );

/**
 * @param $commentdata
 *
 * @return mixed
 */
function es_review_form_validate_recaptcha( $commentdata ) {

	$forms = ests( 'recaptcha_forms' );

	if ( ! empty( ests( 'recaptcha_site_key' ) ) ) {
		if ( is_array( $forms ) && in_array( 'comment_form', $forms ) ) {
			if ( ! isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) ) {
				wp_die( __( 'ReCaptcha Field is Required', 'es' ) );
			}

			if ( ! es_verify_recaptcha() ) {
				wp_die( __( 'Invalid recaptcha', 'es' ) );
			}
		}
	}

	return $commentdata;
}
add_filter( 'preprocess_comment', 'es_review_form_validate_recaptcha' );

/**
 * @param $comment_id int
 * @param $is_approved int
 * @param $comment
 */
function es_save_comment_meta_data( $comment_id, $is_approved, $comment ) {
	$rating = intval( filter_input( INPUT_POST, 'rating' ) );

	if ( strlen( $rating ) ) {
		$comment = (array) $comment;
		update_comment_meta( $comment_id, 'es_review_rating', $rating );
		$entity = es_get_entity_by_id( $comment['comment_post_ID'] );

		if ( $entity ) {
			$reviews_meta = es_calculate_entity_reviews_meta( $comment['comment_post_ID'] );

			if ( strlen( $reviews_meta['rating'] ) ) {
				$rating = $reviews_meta['rating'] ? round( $reviews_meta['rating'], 2 ) : $reviews_meta['rating'];
				$entity->save_field_value( 'rating', $rating );
				$entity->save_field_value( 'reviews_count', $reviews_meta['count'] );
			}
		}
	}
}
add_action( 'comment_post', 'es_save_comment_meta_data', 10, 3 );

/**
 * @param $label
 * @param $field
 *
 * @return mixed
 */
function es_property_table_alter_column_label( $label, $field ) {
	if ( 'gallery' == $field ) {
		$label = __( 'Image', 'es' );
	}

	if ( 'post_status' == $field ) {
		$label = __( 'Status', 'es' );
	}

	return $label;
}
add_filter( 'es_property_table_column_label', 'es_property_table_alter_column_label', 10, 2 );

/**
 * @param $value
 * @param $field
 *
 * @return string
 */
function es_property_table_alter_column_value( $value, $field ) {
	$entity = es_get_entity_by_id( get_the_ID() );

	if ( 'gallery' == $field && $entity::get_entity_name() == 'property' ) {
		$value = sprintf( "<img alt='%s' src='%s'/>", get_the_title(), es_get_the_featured_image_url() );
	}

	if ( 'price' == $field ) {
		if ( $entity->call_for_price ) {
			$value .= " (" . __( 'Call for price', 'es' ) . ")";
		}
	}

	return $value;
}
add_filter( 'es_property_table_column_value', 'es_property_table_alter_column_value', 10, 2 );

/**
 * @param $classes
 * @param $class
 * @param $post_id
 *
 * @return mixed
 */
function es_entities_post_class( $classes, $class, $post_id ) {
	$entity = es_get_entity_by_id( $post_id );

	if ( $entity ) {
		$classes[] = 'es-post-entity';

		if ( $entity instanceof Es_Request && ! $entity->is_viewed ) {
			$classes[] = 'es-request-new';
		}
	}

	return $classes;
}
add_filter( 'post_class', 'es_entities_post_class', 10, 3 );

/**
 * @param $value
 * @param $name
 * @param $settings_instance
 *
 * @return mixed
 */
function es_before_save_featured_term_id( $value, $name, $settings_instance ) {
	if ( 'featured_term_id' == $name ) {
		$old_value = ests( 'featured_term_id' );
		$old_value = $old_value ? $old_value : array();
		$value = wp_parse_args( $value, $old_value );
	}
	return $value;
}
add_action( 'es_before_save_option_value', 'es_before_save_featured_term_id', 10, 3 );

/**
 * @return mixed
 */
function es_body_classes( $classes ) {
	$template = get_option( 'template' );
	$template = sanitize_key( sprintf( 'es-theme-%s', $template ) );
	$classes[] = $template;
	return $classes;
}

add_filter( 'body_class', 'es_body_classes' );

/**
 * Save property action from property management panel.
 *
 * @return void
 */
function es_pm_save_property_handler() {
	if ( ! wp_doing_ajax() && es_get_nonce( 'es_save_property' ) ) {
		$valid_request = wp_verify_nonce( es_get_nonce( 'es_save_property' ), 'es_save_property' );
		$redirect = esc_url( filter_input( INPUT_POST, '_wp_http_referer' ) );

		if ( $valid_request && ! defined( 'DOING_AJAX' ) ) {
			$post_id = intval( filter_input( INPUT_POST, 'post_id' ) );
			$is_new = empty( $post_id );

			if ( ! $is_new || ( es_user_can_publish_listings( get_current_user_id() ) || $is_new ) ) {
				$is_new = empty( $post_id );
				$data = es_clean( $_POST['es_property'] );
				$saved = es_save_property( $data, $post_id );

				if ( ! is_wp_error( $saved ) ) {
					if ( $is_new ) {
						$redirect = add_query_arg( array( 'screen' => 'edit-property', 'property_id' => $post_id ) );
					}
				} else {
					es_set_flash( 'prop-management', $saved->get_error_message(), 'error' );
				}
			} else {
				es_set_flash( 'prop-management', __( 'Your subscription plan doesn\'t allow to publish more listings.', 'es' ), 'error' );
			}
		}

		wp_safe_redirect( $redirect );
	}
}

/**
 * Save property via ajax action.
 *
 * @return void
 */
function es_pm_ajax_save_property_handler() {
	$error = __( 'Invalid security nonce. Please, reload the page and try again.', 'es' );
	$error_template = '<span class="es-icon es-icon_close"></span><h4>%s</h4><p>%s</p>';
	$error_title = _x( 'Error!', 'popup error title', 'es' );
	$message = sprintf( $error_template, $error_title,  $error );
	$btn = '<a href="" class="es-btn es-btn--secondary js-es-close-popup">' . __( 'Got it', 'es' ) . '</a>';
	$response = es_error_ajax_response( $message . $btn );

	if ( check_ajax_referer( 'es_save_property_ajax', 'es_save_property_ajax' ) ) {
		$post_id = intval( filter_input( INPUT_POST, 'post_id' ) );
		$is_new = empty( $post_id );
		$data = es_clean( $_POST['es_property'] );
		$success_template = '<span class="es-icon es-icon_check-mark"></span><h4>%s</h4>';

		if ( ! $is_new || ( es_user_can_publish_listings( get_current_user_id() ) || $is_new ) ) {
			$data['system']['post_status'] = es_get_saving_post_status( $is_new, get_current_user_id() );
			$saved = es_save_property( $data, $post_id );

			if ( is_wp_error( $saved ) ) {
				$message = sprintf( $error_template, $error_title, $saved->get_error_message() );
				$response = es_error_ajax_response( $message . $btn );
			} else {
				if ( $is_new ) {
					$message = __( 'New home was submitted successfully.', 'es' );
				} else {
					$message = __( 'The home was updated successfully.', 'es' );
				}
				$response = es_success_ajax_response( sprintf( $success_template, $message ) );
				$response['post_id'] = $saved;

				$profile_page = get_post_status( ests( 'profile_page_id' ) ) == 'publish' ? ests( 'profile_page_id' ) : null;
				$profile_page = get_post_status( ests( 'property_management_page_id' ) ) == 'publish' ? ests( 'property_management_page_id' ) : $profile_page;

				if ( $profile_page ) {
					$response['redirect_url'] = add_query_arg( array( 'tab' => 'my-listings' ), get_permalink( $profile_page ) );
					$response['redirect_timeout'] = 3000;
				}
			}
		} else {
			$error_template = '<span class="es-icon es-icon_error"></span><h4>%s</h4>';
			$message = __( 'Your subscription plan doesn\'t allow to publish more listings.', 'es' );
			$response = es_error_ajax_response( sprintf( $error_template, $message ) );
		}

		$response['message'] = sprintf( "<div class='es-magnific-popup es-ajax-form-popup'>%s</div>", $response['message'] );
	}

	$response['response_view'] = 'popup';

	$response = apply_filters( 'es_pm_ajax_save_property_handler_response', $response );

	wp_die( json_encode( $response ) );
}

add_action( 'init', 'es_pm_save_property_handler' );
add_action( 'wp_ajax_es_ajax_save_property', 'es_pm_ajax_save_property_handler' );

/**
 * @param $post_id
 */
function es_send_admin_draft_agent_property_email( $post_id ) {
	$post = get_post( $post_id );

	if ( ! current_user_can( 'manage_options' ) ) {
		if ( ests( 'manual_listing_approve' ) && $post->post_status == 'draft' ) {
			$instance = es_get_email_instance( 'admin_listing_added_by_agent', array(
				'post_id' => $post_id,
				'user_id' => $post->post_author,
			) );

			if ( $instance && $instance::is_active() ) {
				$instance->send();
			}

			$user_entity = es_get_user_entity( $post->post_author );

			if ( $user_entity ) {
				es_send_email( 'agent_listing_submitted', $user_entity->get_email(), array(
					'post_id' => $post_id,
					'user_id' => $post->post_author,
				) );
			}
		} else if ( ! ests( 'manual_listing_approve' ) && $post->post_status == 'publish' ) {
			$instance = es_get_email_instance( 'admin_listing_published', array(
				'post_id' => $post_id,
				'user_id' => $post->post_author,
			) );

			if ( $instance && $instance::is_active() ) {
				$instance->send();
			}
		}
	}
}
add_action( 'es_after_save_property', 'es_send_admin_draft_agent_property_email' );

/**
 * @param $post_id
 */
function es_attach_property_agent( $post_id ) {
	if ( es_is_property( $post_id ) && current_user_can( 'agent' ) ) {
		$property = es_get_property( $post_id );
		$agents = $property->agent_id;
		$agents = $agents ? $agents : array();
		$agent_user = es_get_agent_user( get_current_user_id() );

		if ( empty( $agents ) || ( $agent_user->post_id && is_array( $agents ) && ! in_array( $agent_user->post_id, $agents ) ) ) {
			$agents[] = $agent_user->post_id;
			$assign_type = $property->assign_entity_type ? $property->assign_entity_type : array();

			if ( ( is_array( $assign_type ) && ! in_array( 'agent', $assign_type ) ) || ! $assign_type ) {
				$assign_type[] = 'agent';
				$property->save_field_value( 'assign_entity_type', $assign_type );
			}

			$property->save_field_value( 'agent_id', $agents );
		}
	}
}
add_action( 'es_after_save_property', 'es_attach_property_agent' );

/**
 * Switch agent user status on agent post status changed.
 *
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function es_send_agent_published_property_email( $new_status, $old_status, $post ) {
	if ( ests( 'manual_listing_approve' ) && es_is_property( $post ) && $new_status != $old_status && $new_status == 'publish' ) {
		$user_entity = es_get_user_entity( $post->post_author );

		if ( $user_entity && ! user_can( $post->post_author, 'administrator' ) && user_can( $post->post_author, 'publish_es_properties' ) ) {
			es_send_email( 'agent_listing_published', $user_entity->get_email(), array(
				'post_id' => $post->ID
			) );
		}
	}
}
add_action( 'transition_post_status', 'es_send_agent_published_property_email', 15, 3 );

/**
 * @param $schedules
 *
 * @return mixed
 */
function es_cron_new_schedules( $schedules ) {

	if ( empty( $schedules['daily'] ) ) {
		$schedules['daily'] = array(
			'interval' => 86400,
			'display'  => esc_html__( 'Daily', 'es' ),
		);
	}

	if ( empty( $schedules['weekly'] ) ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => esc_html__( 'Weekly', 'es' ),
		);
	}

	if ( empty( $schedules['monthly'] ) ) {
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => esc_html__( 'Monthly', 'es' ),
		);
	}

	if ( empty( $schedules['15m'] ) ) {
		$schedules['15m'] = array(
			'interval' => 900,
			'display'  => esc_html__( 'Each 15 min', 'es' ),
		);
	}

	if ( empty( $schedules['10m'] ) ) {
		$schedules['10m'] = array(
			'interval' => 600,
			'display'  => esc_html__( 'Each 10 min', 'es' ),
		);
	}

	if ( empty( $schedules['5min'] ) ) {
		$schedules['5min'] = array(
			'interval' => 300,
			'display'  => esc_html__( 'Each 5 min', 'es' ),
		);
	}

	if ( empty( $schedules['2min'] ) ) {
		$schedules['2min'] = array(
			'interval' => 120,
			'display'  => esc_html__( 'Each 2 min', 'es' ),
		);
	}

	return $schedules;
}
add_filter( 'cron_schedules', 'es_cron_new_schedules' );

/**
 * @param $data
 *
 * @return array
 */
function es_request_form_submit_data( $data ) {
	if ( ! empty( $data['post_id'] ) ) {
		$entity = es_get_entity_by_id( $data['post_id'] );

		if ( $entity instanceof Es_Agent_Post ) {
			$data['agent'][] = $entity->get_id();
		}
	}

	return $data;
}
add_filter( 'es_request_form_submit_data', 'es_request_form_submit_data' );

/**
 * Save request to database for agent after request form submit.
 *
 * @param $data
 *
 * @return false|void
 */
function es_save_request_form_handler( $data ) {
	$data = es_parse_args( $data, array(
		'post_id' => '',
		'phone' => '',
		'email' => '',
		'message' => '',
		'recipient_type' => '',
	) );

	if ( ! $data['recipient_type'] ) return false;

	$rt = $data['recipient_type'];

	// Base request data to save.
	$request_data = array(
		'system' => array(
			'post_title' => ! empty( $data['name'] ) ? $data['name'] : __( 'ADMIN', 'es' ),
			'post_excerpt' => $data['message']
		),
		'post_id' => $data['post_id'],
		'tel' => $data['phone'],
		'email' => $data['email'],
	);

	/** @var Es_Request_Form_Shortcode $req_class */
	$req_class = es_get_shortcode_instance( 'es_request_form' );

	// Create request post for each administrator.
	if ( $rt == $req_class::SEND_ADMIN || $rt == $req_class::SEND_ADMIN_AGENTS ) {
		$admin_ids = get_users( array(
			'role' => 'administrator',
			'fields' => 'ID'
		) );

		if ( $admin_ids ) {
			foreach ( $admin_ids as $admin_id ) {
				es_save_request( array_merge( $request_data, array(
					'recipient_user_id' => $admin_id,
				) ) );
			}
		}
	}

	// Create request post for selected agents.
	if ( ! empty( $data['agent'] ) && $rt == $req_class::SEND_ADMIN_AGENTS || $rt == $req_class::SEND_AGENTS ) {
		foreach ( $data['agent'] as $agent_id ) {
			es_save_request( array_merge( $request_data, array(
				'recipient_entity_id' => $agent_id,
			) ) );
		}
	}

	// Create request for recipients with custom emails.
	if ( $rt == $req_class::SEND_OTHER && ! empty( $data['send_to_emails'] ) ) {
		es_save_request( array_merge( $request_data, array(
			'recipient_custom_emails' => sanitize_text_field( $data['send_to_emails'] ),
		) ) );
	}
}
add_action( 'es_after_request_form_submitted', 'es_save_request_form_handler' );

/**
 * @param $atts
 */
function es_request_form_section_agents( $atts ) {
	if ( ! empty( $atts['attributes']['layout'] ) && $atts['attributes']['layout'] == 'section' ) {
		/** @var Es_Request_Form_Shortcode $instance */
		$instance = $atts['shortcode_instance'];
		$recipient_type = $atts['attributes']['recipient_type'];

		if ( in_array( $recipient_type, array( $instance::SEND_ADMIN_AGENTS, $instance::SEND_AGENTS ) ) ) {
			remove_filter( 'post_thumbnail_html', array( 'Es_Template_Loader', 'featured_image_filter' ) );
			es_load_template( 'front/shortcodes/request/request-agents.php' );
			add_filter( 'post_thumbnail_html', array( 'Es_Template_Loader', 'featured_image_filter' ) );
		}
	}
}
add_action( 'es_before_request_form', 'es_request_form_section_agents' );

/**
 * @param $atts
 */
function es_request_form_sidebar_agents( $atts ) {
	if ( ! empty( $atts['attributes']['layout'] ) && $atts['attributes']['layout'] == 'sidebar' ) {
		/** @var Es_Request_Form_Shortcode $instance */
		$instance = $atts['shortcode_instance'];
		$recipient_type = $atts['attributes']['recipient_type'];

		if ( in_array( $recipient_type, array( $instance::SEND_ADMIN_AGENTS, $instance::SEND_AGENTS ) ) ) {
			remove_filter( 'post_thumbnail_html', array( 'Es_Template_Loader', 'featured_image_filter' ) );
			es_load_template( 'front/shortcodes/request/request-agents.php', array(
				'context' => 'slim'
			) );
			add_filter( 'post_thumbnail_html', array( 'Es_Template_Loader', 'featured_image_filter' ) );
		}
	}
}
add_action( 'es_before_request_form_submit_button', 'es_request_form_sidebar_agents' );

/**
 *
 *
 * @param $allcaps
 * @param $caps
 * @param $args
 * @param $instance
 *
 * @return mixed
 */
function es_user_has_request_cap( $allcaps, $caps, $args ) {
	if ( ! empty( $args[2] ) && in_array( $args[0], array( 'read_post', 'edit_post', 'delete_post' ) ) ) {

		if ( get_post_type( $args[2] ) === Es_Request::get_post_type_name() ) {
			$request = es_get_request( $args[2] );
			$user_id = $request->user_id;

			if ( ! $user_id ) {
				$agent_post_id = $request->recipient_entity_id;
				if ( $agent_post_id && get_post_status( $agent_post_id ) ) {
					$agent = es_get_agent( $agent_post_id );
					$user_id = $agent->user_id;
				}
			}

			if ( $user_id == $args[1] ) {
				switch ( $args[0] ) {
					case 'read_post':
						$allcaps['read'] = true;
						break;

					case 'edit_post':
						$allcaps['edit_others_es_requests'] = true;
						$allcaps['edit_published_es_requests'] = true;
						break;

					case 'delete_post':
						$allcaps['delete_others_es_requests'] = true;
						$allcaps['delete_published_es_requests'] = true;
						break;
				}
			}
		}
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'es_user_has_request_cap', 10, 3 );

/**
 * @return void
 */
function es_save_request_nonce() {
	if ( wp_verify_nonce( es_get_nonce( 'es_save_request_nonce' ), 'es_save_request_nonce' ) ) {
		$post_id = es_post( 'post_id', 'intval' );

		if ( current_user_can( 'edit_post', $post_id ) && es_is_request( $post_id ) ) {
			$request = es_get_request( $post_id );
			$request->save_field_value( 'note', es_post( 'note' ) );

			wp_safe_redirect( es_post( 'redirect_url' ) ); die;
		}
	}
}
add_action( 'init', 'es_save_request_nonce' );

/**
 * @param $path
 *
 * @return mixed
 */
function es_alter_profile_form_tab_template_path( $path ) {
	if ( get_current_user_id() ) {
		if ( current_user_can( 'agent' ) ) {
			$path = 'front/shortcodes/profile/tabs/agent-profile-form.php';
		}
	}

	return $path;
}
add_filter( 'es_profile_form_tab_template_path', 'es_alter_profile_form_tab_template_path' );

/**
 * @throws \Mpdf\MpdfException
 */
function es_render_property_pdf() {
	$property_id = es_get( 'pdf', 'intval' );

	if ( ! es_is_property( $property_id ) ) return;

	//GuzzleHttpRealtek
	require_once ES_PLUGIN_PATH . '/vendor/autoload.php';

	$query = new WP_Query( array( 'p' => $property_id, 'post_type' => Es_Property::get_post_type_name() ) );

	if ( $query->have_posts() ) {
		$mpdf = new \Mpdf\Mpdf( array(
			'margin_bottom' => 40,
			'margin_top' => 0,
			'margin_right' => 0,
			'margin_left' => 0,
			'default_font' => 'dejavusans'
		) );

		add_filter( 'es_user_get_default_image_url_avatar', 'es_user_get_default_image_url_avatar_pdf' );

		ob_start();

		while ( $query->have_posts() ) {
			$query->the_post();

			include es_locate_template( 'front/property-pdf/pdf.php' );
		}

		$content = ob_get_clean();

		$contact_fields = apply_filters( 'es_pdf_contact_fields', array(
			__( 'Phone', 'es' ) => ests( 'pdf_phone' ),
			__( 'Email', 'es' ) => ests( 'pdf_email' ),
			__( 'Address', 'es' ) => ests( 'pdf_address' ),
		) );

		$contact_fields = array_filter( $contact_fields );

		$mpdf->margin_footer = 0;

		ob_start();
		include es_locate_template( 'front/property-pdf/partials/footer.php' );
		$footer = ob_get_clean();

		$mpdf->SetHTMLFooter( $footer );

		wp_reset_postdata();

		$mpdf->WriteHTML( $content );
		$name = apply_filters( 'es_property_pdf_filename', 'property.pdf', $property_id, $mpdf );
		$mpdf->Output( $name, 'I' );
		die;
	}
}

if ( ! empty( $_GET['pdf'] ) ) {
	add_action( 'init', 'es_render_property_pdf' );
}


if ( ! function_exists( 'es_saved_search_after_query' ) ) {

	/**
	 * @param $saved_search_id
	 */
	function es_saved_search_after_query( $saved_search_id ) {
		es_load_template( 'front/partials/saved-search-update.php', array(
			'saved_search' => es_get_saved_search( $saved_search_id )
		) );
	}
}
add_action( 'es_saved_search_after_query', 'es_saved_search_after_query' );

/**
 * @param $mimes
 *
 * @return mixed
 */
function es_custom_mime_types( $mimes ) {
	if ( empty( $mimes['svg'] ) ) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'es_custom_mime_types' );

/**
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function es_agent_change_status( $new_status, $old_status, $post ) {
	if ( es_is_agent( $post->ID ) ) {
		$agent = es_get_agent( $post->ID );
		$agent_user = $agent->get_user_entity();

		if ( ! $agent_user ) return;

		if ( $new_status !== 'publish' && ! $agent_user->is_disabled() ) {
			$agent_user->deactivate();
		} else if ( $new_status == 'publish' && ! $agent_user->is_active() ) {
			$agent_user->change_status( $agent_user::STATUS_ACTIVE );
		}
	}
}
add_action( 'transition_post_status', 'es_agent_change_status', 10, 3 );

/**
 * @param $login
 * @param $user
 */
function es_agent_check_status_on_login( $login, $user ) {
	if ( ! empty( $user->ID ) && user_can( $user, 'agent' ) ) {
		$agent = es_get_agent_user( $user->ID );

		if ( $agent->status == $agent::STATUS_DISABLED ) {
			$message = __( 'The account is not activated. Please contact the site support.', 'es' );
			es_clear_flash( 'authenticate' );
			es_set_flash( 'authenticate', $message, 'error' );
			wp_logout();
		}
	}
}
add_action( 'wp_login', 'es_agent_check_status_on_login', 7, 2 );

/**
 * @return void
 */
function es_login_errors() {
	$flashes = es_get_flash_instance( 'authenticate' );
	$messages = $flashes->get_messages();

	global $error;

	if ( $messages ) {
		foreach ( $messages as $message ) {
			$error = $message[0];
		}
		$flashes->clean_container();
	}
}
add_action('login_head','es_login_errors');

/**
 * @param $str
 *
 * @return string
 */
function es_sanitize_title_intl( $str ) {
	$chars = array(
		"Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
		"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
		"Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
		"З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
		"М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
		"С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
		"Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
		"Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
		"а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
		"е"=>"e","ё"=>"yo","ж"=>"zh",
		"з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
		"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
		"ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
		"ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		"—"=>"-","«"=>"","»"=>"","…"=>""
	);

	return strtr( $str, $chars );
}

add_filter( 'sanitize_title', 'es_sanitize_title_intl', 8 );

/**
 * @param $plugin
 */
function es_demo_content_redirect( $plugin ) {
	$page = filter_input( INPUT_GET, 'page' );
	if ( $plugin == ES_PLUGIN_BASENAME && $page != 'tgmpa-install-plugins' ) {
		$checked = ! empty( $_POST['checked'] ) ? $_POST['checked'] : array();
		if ( es_need_migration() ) {
			exit ( wp_redirect( 'admin.php?page=es_migration' ) );
		} else if ( ! es_is_demo_executed() && count( $checked ) <=1 ) {
			exit ( wp_redirect( 'admin.php?page=es_demo' ) );
		}
	}
}
add_action( 'activated_plugin', 'es_demo_content_redirect' );

/**
 * WP Multilang Support.
 *
 * @param $config
 *
 * @return mixed
 */
function es_wpm_load_config( $config ) {

	$config['post_types']['properties'] = array();
	$config['post_types']['agent'] = array();
	$config['post_types']['agency'] = array();

	return $config;
}
add_filter( 'wpm_load_config', 'es_wpm_load_config' );

/**
 * Fluh rewrite rules when slug for properties post type is changed.
 *
 * @return void
 */
function es_flush_rewrite_rules() {
	if ( ! get_option( 'es_flush_executed' ) ) {
		flush_rewrite_rules();
		update_option( 'es_flush_executed', 1 );
	}
}
add_action( 'init', 'es_flush_rewrite_rules', 999 );

/**
 * @param $post_types
 *
 * @return mixed
 */
function es_et_builder_third_party_post_types( $post_types ) {
	$post_types[] = 'properties';
	$post_types[] = 'agent';
	$post_types[] = 'agency';
	return $post_types;
}
add_filter( 'et_builder_third_party_post_types', 'es_et_builder_third_party_post_types' );

/**
 * @param $value
 *
 * @return mixed
 */
function es_et_get_option_divi_thumbnails( $value ) {
	if ( is_singular( es_builders_supported_post_types() ) ) {
		return false;
	}

	return $value;
}
add_filter( 'et_get_option_et_divi_divi_thumbnails', 'es_et_get_option_divi_thumbnails' );

/**
 * @param $query
 */
function es_agents_query_alter( $query ) {
	if ( ! function_exists( 'wp_get_current_user' ) ) {
		include( ABSPATH . "wp-includes/pluggable.php" );
	}

	if ( current_user_can( 'agent' ) && is_admin() && defined( 'DOING_AJAX' ) && ! DOING_AJAX ) {
		/** @var WP_Query $query */
		$query->set( 'author', get_current_user_id() );
	}
}
add_action( 'pre_get_posts', 'es_agents_query_alter' );

/**
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function es_request_mark_as_read( $new_status, $old_status, $post ) {
	if ( es_is_request( $post->ID ) ) {
		$request = es_get_request( $post->ID );

		if ( $new_status !== 'publish' ) {
			$request->set_as_viewed();
		}
	}
}
add_action( 'transition_post_status', 'es_request_mark_as_read', 10, 3 );

/**
 * @param $agent_id int Post Agent ID
 */
function es_attach_agent_listings_to_agency( $agent_id ) {
	if ( es_is_agent( $agent_id ) ) {
		$agent = es_get_agent( $agent_id );

		if ( $agent->has_agency() && $agent->post_status == 'publish' ) {
			global $wpdb;
			$properties_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='es_property_agent_id' AND meta_value='%s'", $agent_id ) );

			if ( $properties_ids ) {
				foreach ( $properties_ids as $property_id ) {
					$property = es_get_property( $property_id );
					$property->save_field_value( 'agency_id', $agent->agency_id );
				}
			}
		}
	}
}

/**
 * @param $property_id
 */
function es_attach_property_agent_listings_to_agency( $property_id ) {
	$property = es_get_property( $property_id );

	if ( $agents = $property->agent_id ) {
		foreach ( $agents as $agent_id ) {
			$agent = es_get_agent( $agent_id );

			if ( $agent->has_agency() ) {
				$property->save_field_value( 'agency_id', $agent->agency_id );
			}
		}
	}
}

/**
 * @param $agency_id
 */
function es_attach_agency_agent_listings_to_agency( $agency_id ) {
	$agency = es_get_agency( $agency_id );
	$agents = $agency->get_agents();
	if ( $agency->post_status == 'publish' && $agents ) {
		foreach ( $agents as $agent ) {
			es_attach_agent_listings_to_agency( $agent->get_id() );
		}
	}
}

add_action( 'save_post_agent', 'es_attach_agent_listings_to_agency' );
add_action( 'save_post_properties', 'es_attach_property_agent_listings_to_agency' );
add_action( 'save_post_agency', 'es_attach_agency_agent_listings_to_agency' );

/**
 * @param $fields
 *
 * @return mixed
 */
function es_handle_alt_description( $fields ) {
	$id = get_the_ID();
	$post = get_post( $id );

	if ( $id && es_get_entity_by_id( $id ) && in_array( $post->post_type, es_builders_supported_post_types() ) ) {
		$elementor_editor_mode = es_is_elementor_builder_enabled( $id );
		$divi_builder = function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $id );

		if ( $elementor_editor_mode || $divi_builder ) {
			$fields['alternative_description'] = array(
				'type' => 'editor',
				'tab_machine_name' => 'description',
				'order' => -8,
				'label' => __( 'Alt Description', 'es' ),
				'editor_id' => 'alternative_description',
				'disable_on_frontend' => true,
				'frontend_form_name' => __( 'Description', 'es' ),
			);

			$screen = filter_input( INPUT_GET, 'screen' );

			if ( $screen == 'edit-property' && ! is_admin() ) {
				$elementor_editor_mode = get_post_meta( $id, '_elementor_edit_mode', true );

				if ( $elementor_editor_mode == 'builder' ) {
					$fields['alternative_description']['disable_on_frontend'] = false;
					$fields['post_content']['disable_on_frontend'] = true;
				}
			}
		}

		if ( ! $elementor_editor_mode && ! $divi_builder ) {
			unset( $fields['alternative_description'] );
		}
	}

	return $fields;
}
add_filter( 'es_property_default_fields', 'es_handle_alt_description' );
add_filter( 'es_agent_default_fields', 'es_handle_alt_description' );
add_filter( 'es_agency_default_fields', 'es_handle_alt_description' );

/**
 * @return void
 */
function es_activation_handler() {
	update_option( 'es_flush_executed', 0 );
}
add_action( 'es_activation', 'es_activation_handler' );

/**
 * @param $lostpassword_url
 * @param $redirect
 *
 * @return mixed
 */
function es_alter_lostpassword_url( $lostpassword_url ) {
	if ( es_post( 'es_user_login' ) ) {
		$lostpassword_url = add_query_arg( 'auth_item', 'reset-form', es_post( '_wp_http_referer' ) );
	}
	return $lostpassword_url;
}
add_filter( 'lostpassword_url', 'es_alter_lostpassword_url', 10, 1 );

/**
 * Alter agent post title.
 *
 * @param $title
 * @param $id
 *
 * @return mixed|string
 */
function es_agent_alter_title( $title, $id ) {
	if ( $id && get_post_type( $id ) == 'agent' ) {
		$agent = es_get_agent( $id );

		if ( $agent_user = $agent->get_user_entity() ) {
			$title = $agent_user->get_full_name();
		}
	}

	return $title;
}
add_filter( 'the_title', 'es_agent_alter_title', 10, 2 );

/**
 * @param $tags
 * @param $context
 *
 * @return mixed
 */
function es_alter_wpkses_post_tags( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'title' => true,
			'allow' => true,
		);
	}

	return $tags;
}

add_filter( 'wp_kses_allowed_html', 'es_alter_wpkses_post_tags', 10, 2 );

/**
 * @param $mimes
 *
 * @return mixed
 */
function es_ttf_mime_type( $mimes ) {
	$mimes['ttf'] = 'font/ttf';

	return $mimes;
}
add_filter( 'upload_mimes', 'es_ttf_mime_type' );

/**
 * @param $data
 * @param $file
 * @param $filename
 * @param $mimes
 * @param $real_mime
 *
 * @return mixed
 */
function es_font_correct_filetypes( $data, $file, $filename, $mimes ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$wp_file_type = wp_check_filetype( $filename, $mimes );

	if ( 'ttf' === $wp_file_type['ext'] ) {
		$data['ext'] = 'ttf';
		$data['type'] = 'font/ttf';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'es_font_correct_filetypes', 10, 4 );

/**
 * @param $post_id
 */
function es_pmxi_attach_images( $post_id ) {
	if ( es_is_property( $post_id ) ) {
		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'posts_per_page' => -1,
			'fields' => 'ids',
		) );

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $order => $attachment_id ) {
				if ( ! get_post_meta( $attachment_id, 'es_attachment_type', true ) ) {
					update_post_meta( $attachment_id, 'es_attachment_type', 'gallery' );
					update_post_meta( $attachment_id, 'es_attachment_order', $order );
				}
			}
		}
	}
}
add_action( 'pmxi_saved_post', 'es_pmxi_attach_images', 10, 1 );

/**
 * @param $post_id
 */
function es_pmxi_save_video( $post_id ) {
	if ( es_is_property( $post_id ) ) {
		$video_iframe = get_post_meta( $post_id, 'es_property_video_iframe' );
		$video_url = get_post_meta( $post_id, 'es_property_video_url' );

		if ( $video_iframe || $video_url ) {
			$value = array(
				'video_url'    => $video_url,
				'video_iframe' => $video_iframe,
				'video_file'   => '',
			);

			$property = es_get_property( $post_id );
			$property->save_field_value( 'video', $value );
		}
	}
}
add_action( 'pmxi_saved_post', 'es_pmxi_save_video', 10, 1 );

/**
 * @param $post_id
 */
function es_pmxi_save_address_components( $post_id ) {
	if ( es_is_property( $post_id ) ) {
		$property = es_get_property( $post_id );
		$components = array();
		$address_components_fields = array( 'city', 'province', 'state', 'country' );
		foreach ( $address_components_fields as $field ) {
			$field_info = $property::get_field_info( $field );
			$value = $property->{$field};
			if ( ! empty( $field_info['address_component'] ) && ! empty( $value ) ) {
				$component = new stdClass();
				$component->types = array( $field_info['address_component'] );

				if ( is_numeric( $value ) ) {
					$component->term_id = $value;
				} else {
					$component->long_name = $value;
				}

				$components[] = $component;
			}
		}
		if ( ! empty( $components ) ) {
			$components = json_encode( $components, JSON_UNESCAPED_UNICODE );
			$property->save_field_value( 'address_components', $components );
		}
	}
}
add_action( 'pmxi_saved_post', 'es_pmxi_save_address_components', 10, 1 );

/**
 * @param $wp_query WP_Query
 */
function es_set_entities_per_page_query( $wp_query ) {
	$post_types = es_builders_supported_post_types();

	if ( ! is_admin() ) {
		foreach ( $post_types as $post_type ) {
			if ( $wp_query->is_post_type_archive( $post_type ) && $wp_query->is_main_query() ) {
				$name = $post_type == 'properties' ? 'properties_per_page' : 'agency_agents_per_page';
				$wp_query->set( 'posts_per_page', ests( $name ) );
			}
		}
	}
}
add_action( 'pre_get_posts', 'es_set_entities_per_page_query', 20 );

/**
 * Remove admin bar for agents.
 */
function es_remove_admin_bar() {
	if ( current_user_can( 'agent' ) ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'es_remove_admin_bar' );

/**
 * @param $fields
 *
 * @return mixed
 */
function es_disable_openhouse_fields( $fields ) {
	if ( es_is_property_default_section_deactivated( 'open-house' ) ) {
		$fields['is_open_house']['disable_on_frontend'] = true;
		$fields['is_appointment_only']['disable_on_frontend'] = true;
		$fields['appointments']['disable_on_frontend'] = true;
	}
	return $fields;
}
add_filter( 'es_property_default_fields', 'es_disable_openhouse_fields' );

/**
 * @param $fields
 *
 * @return array
 */
function es_fb_alter_properties_range_fields( $fields ) {
	$instance = es_get_fields_builder_instance();
	$items = $instance::get_items();

	if ( ! empty( $items ) && ! empty( $fields ) ) {
		foreach ( $items as $field => $field_info ) {
			if ( ! empty( $field_info['search_settings']['range'] ) && ! in_array( $field, $fields ) ) {
				$fields[] = $field;
			}
		}
	}

	return $fields;
}
add_filter( 'es_get_properties_range_fields', 'es_fb_alter_properties_range_fields' );

/**
 * @param $link
 * @param $term
 * @param $taxonomy
 *
 * @return mixed
 */
function es_alter_term_link( $link, $term, $taxonomy ) {
	if ( ! ests( 'is_default_archive_template_enabled' ) ) {
		$taxonomies = get_object_taxonomies( 'properties' );

		if ( in_array( $taxonomy, $taxonomies ) ) {
			$search_page = es_get_page_url( 'search_results' );

			if ( $search_page ) {
				$link = add_query_arg( array(
					$taxonomy => array( $term->term_id )
				), $search_page );
			}
		}
	}

	return $link;
}
add_filter( 'term_link', 'es_alter_term_link', 10, 3 );

/**
 * @param $post_id
 */
function es_set_property_sort_labels( $post_id ) {
	global $wpdb;

	if ( ! is_numeric( $post_id ) ) {
		$post = get_post( $post_id );
		$post_id = $post->ID;
	}

	if ( $post_id ) {
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id='" . $post_id . "' AND meta_key LIKE 'es_property_sort_%'" );
		$terms = get_terms( array(
			'taxonomy'   => 'es_label',
			'hide_empty' => false,
		) );

		if ( $terms ) {
			foreach( $terms as $term ) {
				if ( has_term( $term->slug, 'es_label', $post_id ) ) {
					update_post_meta( $post_id, 'es_property_sort_' . $term->slug, 1 );
				} else {
					update_post_meta( $post_id, 'es_property_sort_' . $term->slug, 0 );
				}
			}
		}
	}
}
add_action( 'es_after_save_property', 'es_set_property_sort_labels' );

/**
 * Set back url for single property page.
 */
function es_search_back_url() {
	if ( ! is_admin() && es_get( 'es' ) && empty( $GLOBALS['search_url'] ) ) {
		$GLOBALS['search_url'] = es_get_current_url();
	}
}
add_action( 'init', 'es_search_back_url' );
