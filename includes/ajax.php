<?php

add_action( 'wp_ajax_es_save_field', 'es_ajax_save_field' );

/**
 * Save field via ajax.
 *
 * @return void
 */
function es_ajax_save_field() {
	if ( check_ajax_referer( 'es_save_field', 'save_field_nonce', false ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			$field = sanitize_key( filter_input( INPUT_POST, 'field' ) );
			$value = es_clean( filter_input( INPUT_POST, 'value' ) );
			$container = filter_input( INPUT_POST, 'container' );

			if ( 'estatik-settings' == $container ) {
				ests_save_option( $field, $value );
			}
		}
	}

	wp_die();
}

add_action( 'wp_ajax_es_get_terms_creator', 'es_ajax_get_terms_creator' );

/**
 * Send terms creator markup via ajax.
 *
 * @return void
 */
function es_ajax_get_terms_creator() {

	if ( check_ajax_referer( 'es_get_terms_creator', 'nonce', false ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			$taxonomy = sanitize_key( filter_input( INPUT_GET, 'taxonomy' ) );
			$type = es_clean( filter_input( INPUT_GET, 'type' ) );

			if ( $creator = es_get_terms_creator_factory( $taxonomy, $type ) ) {
				$creator->render();
			}
		}
	}

	wp_die();
}

/**
 * Return dependencies location fields values.
 *
 * @return void
 */
function es_ajax_get_locations() {
    if ( check_ajax_referer( 'es_get_locations', 'nonce', false ) ) {
        $parent_id = es_clean( filter_input( INPUT_GET, 'dependency_id' ) );
        $types = es_clean( filter_input( INPUT_GET, 'types', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) );
        $address_components = es_get_address_components_container();
        $components = $address_components::get_locations( $types, $parent_id );

        wp_die( json_encode( apply_filters( 'es_ajax_get_locations', $components, $parent_id, $types ) ) );
    }
}
add_action( 'wp_ajax_es_get_locations', 'es_ajax_get_locations' );
add_action( 'wp_ajax_nopriv_es_get_locations', 'es_ajax_get_locations' );

/**
 * Add / Delete item to / from wishlist via ajax.
 *
 * @return string
 */
function es_ajax_wishlist_action() {
    $post_id = es_clean( filter_input( INPUT_POST, 'post_id' ) );
    $entity = es_clean( filter_input( INPUT_POST, 'entity' ) );

    if ( $post_id ) {
        $wishlist = es_get_wishlist_instance( $entity );

        if ( $wishlist->has( $post_id ) ) {
            $wishlist->remove( $post_id );
            wp_die( json_encode( es_success_ajax_response( __( 'Item successfully removed from wishlist', 'es' ) ) ) );
        } else {
            $wishlist->add( $post_id );
            wp_die( json_encode( es_success_ajax_response( __( 'Item successfully added to wishlist', 'es' ) ) ) );
        }
    } else {
        wp_die( json_encode( es_error_ajax_response( __( 'Something wrong. Please contact the support.', 'es' ) ) ) );
    }
}
add_action( 'wp_ajax_es_wishlist_action', 'es_ajax_wishlist_action' );
add_action( 'wp_ajax_nopriv_es_wishlist_action', 'es_ajax_wishlist_action' );

/**
 * Add item to compare list via ajax.
 *
 * @return string
 */
function es_ajax_compare_action() {
	$post_id = es_clean( filter_input( INPUT_POST, 'post_id' ) );

	if ( $post_id ) {
		if ( es_is_property( $post_id ) ) {
			$compare = es_get_compare_instance();

			if ( $compare->is_auth_required() && ! is_user_logged_in() ) {
				wp_die( json_encode( es_error_ajax_response( __( 'Auth is required for compare listings.', 'es' ) ) ) );
			} else {
				if ( $compare->has( $post_id ) ) {
					wp_die( json_encode( es_error_ajax_response( __( 'Item already in compare list.', 'es' ) ) ) );
				} else {
					if ( ! $compare->can_add() ) {
						wp_die( json_encode( es_error_ajax_response( sprintf(
							__( 'Sorry, but you can compare only %s properties.', 'es' ), ests( 'compare_max_entities_num' )
						) ) ) );
					} else {
						$compare->add( $post_id );

						wp_die( json_encode(array(
							'compare_page_url' => es_get_page_url( 'compare' ),
							'message' => __( 'Item successfully added to compare list', 'es' ),
							'status' => 'success',
							'item_id' => $post_id,
						) ) );
					}
				}
			}
		} else {
			wp_die( json_encode( es_error_ajax_response( __( 'Invalid property to compare.', 'es' ) ) ) );
		}
	} else {
		wp_die( json_encode( es_error_ajax_response( __( 'Something wrong. Please contact the support.', 'es' ) ) ) );
	}
}
add_action( 'wp_ajax_es_compare_action', 'es_ajax_compare_action' );
add_action( 'wp_ajax_nopriv_es_compare_action', 'es_ajax_compare_action' );

/**
 * Delete property from compare list.
 */
function es_ajax_compare_delete_action() {
	$post_id = es_clean( filter_input( INPUT_POST, 'post_id' ) );

	if ( $post_id ) {
		if ( es_is_property( $post_id ) ) {
			$compare = es_get_compare_instance();

			if ( $compare->has( $post_id ) ) {
				$compare->remove( $post_id );
				wp_die( json_encode( es_success_ajax_response( __( 'The property successfully removed.', 'es' ) ) ) );
			} else {
				wp_die( json_encode( es_error_ajax_response( __( 'The property already deleted.', 'es' ) ) ) );
			}
		}
	} else {
		wp_die( json_encode( es_error_ajax_response( __( 'Invalid property to delete.', 'es' ) ) ) );
	}
}
add_action( 'wp_ajax_es_compare_delete_action', 'es_ajax_compare_delete_action' );
add_action( 'wp_ajax_nopriv_es_compare_delete_action', 'es_ajax_compare_delete_action' );

/**
 * Return single property content-archive item via ajax.
 *
 * @return void
 */
function es_ajax_get_property_item() {
    $post_id = intval( es_post( 'post_id' ) );
    $response = array( 'status' => 'error' );

    if ( $post_id && get_post_status( $post_id ) == 'publish' && get_post_type( $post_id ) == 'properties' ) {
        $query = new WP_Query( array(
            'post_type' => 'properties',
            'p' => $post_id
        ) );

	    // Generate back to search link.
	    if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		    $GLOBALS['search_url'] = $_SERVER['HTTP_REFERER'];
	    }

        if ( $query->have_posts() ) {
            ob_start();
            echo '<div class="es-listings es-listings--hfm es-listings--grid-1">';
            while ( $query->have_posts() ) {
                $query->the_post();
                es_load_template( 'front/property/content-archive.php', array(
                    'ignore_wrapper' => true,
	                'target_blank' => 'target="_blank"',
                ) );
            }
            echo "</div>";
            wp_reset_postdata();
            $content = ob_get_clean();

            $response = array(
                'status' => 'success',
                'content' => $content,
            );
        }
    }

    wp_die( json_encode( apply_filters( 'es_ajax_get_property_item', $response, $post_id ) ) );
}
add_action( 'wp_ajax_es_get_property_item', 'es_ajax_get_property_item' );
add_action( 'wp_ajax_nopriv_es_get_property_item', 'es_ajax_get_property_item' );

if ( ! function_exists( 'es_ajax_search_address_components' ) ) {

    /**
     * Search address autocomplete ajax handler.
     *
     * @return void
     */
    function es_ajax_search_address_components() {
        $query = es_clean( filter_input( INPUT_GET, 'q' ) );
        if ( strlen( $query ) > 2 ) {
            $results_addresses_components = get_terms( array(
                'taxonomy' => 'es_location',
                'name__like' => $query,
                'fields' => 'id=>name',
                'number' => apply_filters( 'es_address_autocomplete_terms_number', 5 ),
            ) );

			$posts = get_posts( array(
				'posts_per_page' => 5,
				'post_status' => 'publish',
				'post_type' => 'properties',
				'meta_query' => array(
					array(
						'key' => 'es_property_address',
						'value' => $query,
						'compare' => 'LIKE',
					),
				),
			) );

            ob_start();
            es_load_template( 'front/shortcodes/search/partials/autocomplete.php', array(
                'addresses' => $results_addresses_components,
	            'posts' => $posts,
            ) );

            $response = array(
                'status' => 'success',
                'content' => ob_get_clean()
            );

            $response = apply_filters( 'es_address_autocomplete_response', json_encode( $response ) );

            wp_die( $response );
        }
    }
}
add_action( 'wp_ajax_es_search_address_components', 'es_ajax_search_address_components' );
add_action( 'wp_ajax_nopriv_es_search_address_components', 'es_ajax_search_address_components' );

/**
 * Save search actions.
 *
 * @return void
 */
function es_ajax_save_search() {
	$response = array(
		'status' => 'error',
		'message' => __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ),
	);

	if ( check_ajax_referer( 'es_save_search', 'nonce' ) ) {
		$data = es_array_filter_recursive( es_clean( $_POST ), null, true );
		unset( $data['action'], $data['nonce'] );

		if ( ! empty( $data ) && is_array( $data ) ) {
			$data = apply_filters( 'es_save_search_saving_fields', $data );
			$data['update_type'] = 'none';

			$post_id = wp_insert_post( array(
				'post_type' => 'saved_search',
				'post_status' => 'private',
				'post_title' => '',
				'post_author' => get_current_user_id(),
			), true );

			if ( ! is_wp_error( $post_id ) ) {
				$saved_search = es_get_saved_search( $post_id );
				$saved_search->save_fields( $data );

				$response = es_simple_ajax_response( __( 'Search saved', 'es' ), 'success' );
			} else {
				$response = es_simple_ajax_response( $post_id->get_error_message(), 'error' );
			}
		} else {
			$response = es_simple_ajax_response( __( 'Search params are empty. Please fill search fields.', 'es' ), 'error' );
		}
	}

	wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_save_search', 'es_ajax_save_search' );

/**
 * Remove saved search via ajax.
 *
 * @return void
 */
function es_ajax_remove_saved_search() {
    if ( wp_verify_nonce( es_get_nonce( 'nonce' ), 'es_remove_saved_search' ) ) {
        $post_id = es_decode( es_clean( filter_input(INPUT_POST, 'hash' ) ) );
        if ( is_array( $post_id ) && ! empty( $post_id[0] ) ) {
        	$post_id = $post_id[0];
        }
        $saved_search = get_post( $post_id );
        if ( $post_id && $saved_search->post_author == get_current_user_id() ) {
            $saved_search = es_get_saved_search( $post_id );
            $saved_search->delete( true );
            $response = es_success_ajax_response( __( 'Successfully deleted.', 'es' ) );
        } else {
            $response = es_error_ajax_response( __( 'Invalid saved search.', 'es' ) );
        }
    } else {
        $response = es_error_ajax_response( __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ) );
    }

    wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_remove_saved_search', 'es_ajax_remove_saved_search' );

/**
 * Return listings via ajax response.
 *
 * @return void
 */
function es_ajax_get_listings() {
	$attributes = es_get( 'hash', false ) ? es_get( 'hash', false ) : es_post( 'hash', false );
    $attributes = es_decode( $attributes );
    $need_reload_map = es_get( 'reload_map' ) ? es_get( 'reload_map' ) : es_post( 'reload_map' );
    $attributes['_ajax_mode'] = true;
    $attributes['_ignore_coordinates'] = ! $need_reload_map;

    // Generate back to search link.
	if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$GLOBALS['search_url'] = $_SERVER['HTTP_REFERER'];
	}

    /** @var Es_My_Listing_Shortcode $shortcode */
    $shortcode = es_get_shortcode_instance( 'es_my_listing', $attributes );

    $response = array(
        'status' => 'success',
        'message' => $shortcode->get_content(),
    );

    $query_args = $shortcode->get_query_args();

    if ( $need_reload_map ) {
		if ( ! empty( $attributes['map_show'] ) && $attributes['map_show'] == 'all' ) {
			$query_args['posts_per_page'] = -1;
		}
	    $response['coordinates'] = es_properties_get_markers( $query_args );
    }

    $response['loop_uid'] = $attributes['loop_uid'];
    $response['reload_map'] = $need_reload_map;

    wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_get_listings', 'es_ajax_get_listings' );
add_action( 'wp_ajax_nopriv_get_listings', 'es_ajax_get_listings' );

/**
 * Ajax agents search
 */
function es_ajax_get_agents() {
	$attributes = es_get( 'hash' ) ? es_get( 'hash' ) : es_post( 'hash' );
	$attributes = es_decode( $attributes );
	$attributes['_ajax_mode'] = true;

	// Generate back to search link.
	if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$GLOBALS['search_url'] = $_SERVER['HTTP_REFERER'];
	}

	$shortcode_name = ! empty( $attributes['type'] ) && $attributes['type'] == 'agency' ?
		'es_my_agencies' : 'es_my_agents';

	/** @var Es_My_Listing_Shortcode $shortcode */
	$shortcode = es_get_shortcode_instance( $shortcode_name, $attributes );

	$response = array(
		'status' => 'success',
		'message' => $shortcode->get_content(),
	);

	wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_get_agent', 'es_ajax_get_agents' );
add_action( 'wp_ajax_nopriv_get_agent', 'es_ajax_get_agents' );
add_action( 'wp_ajax_get_agency', 'es_ajax_get_agents' );
add_action( 'wp_ajax_nopriv_get_agency', 'es_ajax_get_agents' );

/**
 * Search for properties locations.
 *
 * @return void
 */
function es_ajax_get_select2_locations() {
	if ( check_ajax_referer( 'get_select2_locations' ) && current_user_can( 'manage_options' ) ) {
		$s = es_get('s' );
		$result = array( 'results' => array() );

		if ( $s ) {
			$terms = get_terms( array( 'fields' => 'id=>name', 'name__like' => $s, 'taxonomy' => 'es_location', 'hide_empty' => false, ) );
			if ( $terms ) {
				foreach ( $terms as $term_id => $name ) {
					$result['results'][] = array( 'id' => $term_id, 'text' => $name );
				}
			}
		}

		wp_die( json_encode( $result ) );
	}
}
add_action( 'wp_ajax_get_select2_locations', 'es_ajax_get_select2_locations' );

/**
 * @return void
 */
function es_ajax_get_select2_agents() {
	if ( check_ajax_referer( 'get_select2_agents' ) && current_user_can( 'manage_options' ) ) {
		$agency_id = intval( filter_input( INPUT_GET, 'agency_id' ) );
		$s = filter_input( INPUT_GET, 's' );
		$result = array();

//		if ( ! $agency_id && ! $s ) {
//			wp_die( 0 );
//		}

		$query_args = es_get_agents_query_args( array(
			'fields' => array(
				'agency_id' => $agency_id,
				'keywords' => $s,
				'sort' => 'newest',
			),
		) );

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				$result[] = array( 'id' => get_the_ID(), 'text' => get_the_title() );
			}

			wp_reset_postdata();
		} else {
			$result[] = array( 'id' => '', 'text' => '' );
		}

		$response_format = es_get( 'response_format' );

		if ( $response_format == 'select2' ) {
			if ( ! empty( $result ) ) {
				$new_result['results'] = $result;
			}
			wp_die( json_encode( $new_result ) );
		} else {
			wp_die( json_encode( es_success_ajax_response( $result ) ) );
		}
	}
}
add_action( 'wp_ajax_get_select2_agents', 'es_ajax_get_select2_agents' );

/**
 * @return void
 */
function es_ajax_get_agent_list_item() {
	if ( current_user_can( 'manage_options' ) ) {
		$post_id = sanitize_text_field( filter_input( INPUT_GET, 'value' ) );

		if ( es_is_agent( $post_id ) ) {
			$agent = es_get_agent( $post_id );

			$result = array(
				'id' => $post_id,
				'text' => get_the_title( $post_id ),
				'subtitle' => $agent->position,
				'image' => es_get_the_agent_avatar( $post_id ),
			);

			wp_die( json_encode( $result ) );
		}
	}
}
add_action( 'wp_ajax_es_get_agent_list_item', 'es_ajax_get_agent_list_item' );

/**
 * Return contact form via ajax for contact agent/agency.
 *
 * @return void
 */
function es_ajax_contact_request_form() {
	$entity_id = filter_input( INPUT_GET, 'entity_id' );
	$response = es_error_ajax_response( __( 'Invalid contact entity', 'es' ) );

	$entity = es_get_entity_by_id( $entity_id );

	if ( $entity instanceof Es_Emailed_Entity && ( $email = $entity->get_email() ) ) {
		$title = sprintf( _x( 'Contact %s', 'agent | agency contact form title', 'es' ), get_the_title( $entity_id ) );
		$request_form = es_get_shortcode_instance( 'es_request_form', array(
			'layout' => 'popup',
			'background' => '#fff',
			'post_id' => $entity_id,
			'message' => '',
			'recipient_type' => Es_Request_Form_Shortcode::SEND_OTHER,
			'custom_email' => $email,
			'color' => '#263238',
			'button_text' => __( 'Send message', 'es' ),
			'title' => call_user_func( 'es_get_the_' . $entity::get_post_type_name() . '_avatar', $entity_id ) . $title,
		) );
		$request_form = sprintf( "<div class='es-magnific-popup es-magnific-popup--contact es-magnific-popup--%s'>%s</div>", $entity::get_entity_name(), $request_form->get_content() );
		$response = es_success_ajax_response( $request_form );
	}

	wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_contact_request_form', 'es_ajax_contact_request_form' );
add_action( 'wp_ajax_nopriv_es_contact_request_form', 'es_ajax_contact_request_form' );

function es_ajax_management_delete_property_popup() {
	if ( check_ajax_referer( 'es_management_delete_property_popup', '_nonce' ) ) {
		$property_id = intval( filter_input( INPUT_GET, 'property_id' ) );

		if ( current_user_can( 'delete_post', $property_id ) ) {
			$query = new WP_Query( array(
				'post_type' => 'properties',
				'p' => $property_id
			) );

			if ( $query->have_posts() ) {
				ob_start();
				echo '<div class="es-listings es-listings--grid-1">';
				while ( $query->have_posts() ) {
					$query->the_post();
					es_load_template( 'front/property/content-archive.php', array(
						'ignore_wrapper' => true,
						'target_blank' => 'target="_blank"',
					) );
				}
				echo "</div>";
				wp_reset_postdata();
				$content = ob_get_clean();
				$title = __( "Delete the home from your listings?", 'es' );

				$content = "<div class='es-magnific-popup es-magnific-popup--delete-action'>
					<h4>{$title}</h4>
					<div class='es-magnific-listing'>
						{$content}
						<div class='es-magnific-buttons'>
							<a href='#' data-trigger-click='.mfp-close' class='es-btn es-btn--default'>" . __( 'Cancel', 'es' ) . "</a>
							<a href='" . es_get_action_post_link( $property_id, 'delete' ) . "' class='es-btn es-btn--secondary'><span class='es-icon es-icon_trash'></span>" . __( 'Delete home', 'es' ) . "</a>
						</div>
					</div>
				</div>";

				$response = apply_filters( 'es_ajax_management_delete_property_response', array(
					'status' => 'success',
					'message' => $content,
				) );

				wp_die( json_encode( $response ) );
			}
		}
	}
}
add_action( 'wp_ajax_es_management_delete_property_popup', 'es_ajax_management_delete_property_popup' );

/**
 * @return void
 */
function es_ajax_saved_search_change_period() {
	$response = es_error_ajax_response( __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ) );

	if ( check_ajax_referer( 'es_saved_search_change_period' ) ) {
		$flashes = es_get_flash_instance( 'saved-search' );
		$data = es_clean( $_POST );
		$data = es_parse_args( $data, array(
			'saved_search_id' => null,
			'update_type' => 'weekly'
		) );

		if ( es_is_saved_search( $data['saved_search_id'] ) ) {
			$saved_search = es_get_saved_search( $data['saved_search_id'] );
			$saved_search->save_field_value( 'update_type', $data['update_type'] );
			es_set_flash( 'saved-search', __( 'Successfully saved', 'es' ), 'success' );

			$response = array(
				'status' => 'success',
				'message' => $flashes->get_messages_markup()
			);
		} else {
			es_set_flash( 'saved-search', __( 'Invalid entity', 'es' ), 'error' );
			$response = array(
				'status' => 'error',
				'message' => $flashes->get_messages_markup()
			);
		}
	}

	wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_saved_search_change_period', 'es_ajax_saved_search_change_period' );