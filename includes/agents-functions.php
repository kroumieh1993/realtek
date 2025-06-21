<?php

/**
 * @param $entity_id int
 *
 * @return int
 */
function es_calculate_entity_reviews_meta( $entity_id ) {
    $comments = get_comments( array(
        'post_id' => $entity_id,
        'status' => 'approve',
        'fields' => 'ids',
    ) );

	$sum_rating = array(
        'count' => ! empty( $comments ) ? count( $comments ) : 0,
        'rating' => 0
    );

    if ( ! empty( $comments ) ) {
        $counter = 0;
	    $sum_rating['rating'] = 0;

        foreach ( $comments as $comment_id ) {
            $rating = get_comment_meta( $comment_id, 'es_review_rating', true );
            if ( strlen( $rating ) ) {
                $counter++;
	            $sum_rating['rating'] += $rating;
            }
        }

        if ( $sum_rating ) {
            $sum_rating['rating'] = $sum_rating['rating'] / $counter;
        }
    }

    return apply_filters( 'es_calculate_entity_reviews_meta', $sum_rating, $entity_id );
}

if ( ! function_exists( 'es_get_agencies_query_args' ) ) {

	/**
	 * Search agents method.
	 *
	 * @param array $args .
	 *
	 * @return mixed|void
	 */
	function es_get_agencies_query_args( $args = array() ) {
		$args = apply_filters( 'es_get_agencies_atts', es_parse_args( $args, array(
			'query'    => array(
				'post_type'      => 'agency',
				'posts_per_page' => 10,
				'post_status'    => 'publish',
			),
			'fields'   => array(),
			'settings' => array(),
		) ) );

		$query_args = $args['query'];

		if ( ! empty( $args['fields'] ) ) {
			foreach ( $args['fields'] as $field => $value ) {
				$finfo = es_agent_get_field_info( $field );
				if ( ! isset( $value ) || ! $finfo ) continue;

				if ( $field == 'type' ) continue;


				if ( 'keywords' == $field ) {
					$meta_query[ $field ]['relation'] = 'OR';

					$value = ! is_array( $value ) ? explode( ',', $value ) : $value;

					foreach ( $value as $keyword ) {
						$meta_query[ $field ][] = array(
							'key' => 'es_agency_keywords',
							'value' => $keyword,
							'compare' => 'LIKE'
						);
					}

					continue;
				} else if ( ! empty( $finfo['taxonomy'] ) && ! empty( $value ) ) {
					$tax_query[ $field ] = array(
						'taxonomy' => $field,
						'terms' => is_array( $value ) ? $value : explode( ',', $value ),
						'field' => 'id'
					);
				} else {
					if ( ! $value ) continue;

					$meta_query[ $field ] = array(
						'key' => 'es_agency_' . $field,
						'value' => $value,
					);
				}
			}
		}

		$sort = ! empty( $args['fields']['sort'] ) ? $args['fields']['sort'] : ests( 'agencies_default_sorting_option' );

		if ( $sort ) {
			switch ( $sort ) {
				case 'newest':
					$query_args['orderby'] = 'modified';
					$query_args['order'] = 'DESC';
					break;

				case 'highest_rating':
					$query_args['orderby'] = 'meta_value_num';
					$query_args['meta_key'] = 'es_agency_rating';
					$query_args['order'] = 'DESC';
					break;

				case 'reviews':
					$query_args['orderby'] = 'meta_value_num';
					$query_args['meta_key'] = 'es_agency_reviews_count';
					$query_args['order'] = 'DESC';
					break;

				default:

			}
		}

		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		return apply_filters( 'es_get_agencies_query_args', $query_args, $args );
	}
}

if ( ! function_exists( 'es_get_agents_query_args' ) ) {

	/**
	 * Search agents method.
	 *
	 * @param array $args .
	 *
	 * @return mixed|void
	 */
	function es_get_agents_query_args( $args = array() ) {
		$args = apply_filters( 'es_get_agents_atts', es_parse_args( $args, array(
			'query'    => array(
				'post_type'      => 'agent',
				'posts_per_page' => 10,
				'post_status'    => 'publish',
			),
			'fields'   => array(),
			'settings' => array(),
		) ) );

		$query_args = $args['query'];

		if ( ! empty( $args['fields'] ) ) {
		    foreach ( $args['fields'] as $field => $value ) {
		        $finfo = es_agent_get_field_info( $field );
			    if ( ! isset( $value ) || ! $finfo  ) continue;

		        if ( $field == 'type' ) continue;

			    if ( 'keywords' == $field ) {
				    $meta_query[ $field ]['relation'] = 'OR';

				    $value = ! is_array( $value ) ? explode( ',', $value ) : $value;

				    foreach ( $value as $keyword ) {
					    $meta_query[ $field ][] = array(
						    'key' => 'es_agent_keywords',
						    'value' => $keyword,
						    'compare' => 'LIKE'
					    );
				    }

				    continue;
			    } else if ( ! empty( $finfo['taxonomy'] ) && ! empty( $value ) ) {
				    $tax_query[ $field ] = array(
					    'taxonomy' => $field,
					    'terms' => is_array( $value ) ? $value : explode( ',', $value ),
					    'field' => 'id'
				    );
			    } else {
				    if ( ! $value ) continue;

				    $meta_query[ $field ] = array(
					    'key' => 'es_agent_' . $field,
					    'value' => $value,
				    );
                }
            }
        }

		$sort = ! empty( $args['fields']['sort'] ) ? $args['fields']['sort'] : ests( 'agents_default_sorting_option' );

        if ( $sort ) {
            switch ( $sort ) {
                case 'newest':
                    $query_args['orderby'] = 'modified';
                    $query_args['order'] = 'DESC';
                    break;

                case 'highest_rating':
                    $query_args['orderby'] = 'meta_value_num';
                    $query_args['meta_key'] = 'es_agent_rating';
                    $query_args['order'] = 'DESC';
                    break;

                case 'reviews':
                    $query_args['orderby'] = 'meta_value_num';
                    $query_args['meta_key'] = 'es_agent_reviews_count';
                    $query_args['order'] = 'DESC';
                    break;

                default:

            }
        }


		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
        }

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
        }

		return apply_filters( 'es_get_agents_query_args', $query_args, $args );
	}
}

if ( ! function_exists( 'es_entities_navbar' ) ) {

	/**
	 * Display properties navbar.
	 */
	function es_entities_navbar( $args ) {
		es_load_template( 'front/entity/partials/navbar.php', $args );
	}
}
add_action( 'es_entities_navbar', 'es_entities_navbar' );

if ( ! function_exists( 'es_entities_layouts' ) ) {

	/**
	 * Display layouts buttons on listings page.
	 *
	 * @return void
	 */
	function es_entities_layouts( $args ) {
        $template_path = "front/{$args['type']}/partials/layout-buttons.php";

        if ( file_exists( es_locate_template( $template_path ) ) ) {
	        es_load_template( $template_path, $args );
        }
	}
}
add_action( 'es_entities_layouts', 'es_entities_layouts' );

if ( ! function_exists( 'es_entities_sort_dropdown' ) ) {

	/**
	 * Display sorting dropdown.
	 *
	 * @param $sort
	 * @return void
	 */
	function es_entities_sort_dropdown( $sort, $args ) {
        if ( ! empty( $args['type'] ) && ( $plular_entity_name = es_get_entity_plural_name( $args['type'] ) ) ) {
	        $sorting = ests( $plular_entity_name . '_sorting_options' );

	        if ( ! empty( $sorting ) ) : ?>
                <div class="es-form">
			        <?php es_framework_field_render( 'sort', array(
				        'type' => 'select',
				        'value' => $sort,
				        'options' => ests_selected( $plular_entity_name . '_sorting_options' ),
				        'label' => __( 'Sort by', 'es' ),
				        'attributes' => array(
					        'class' => 'js-es-sort'
				        )
			        ) ); ?>
                </div>
	        <?php endif;
        }
	}
}
add_action( 'es_entities_sort_dropdown', 'es_entities_sort_dropdown', 10, 2 );

if ( ! function_exists( 'es_the_agent_control' ) ) {

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	function es_the_agent_control( $args = array() ) {
		$args = es_parse_args( $args, array(
			'show_sharing' => true,
			'is_full' => true,
			'wishlist_confirm' => false,
			'entity' => 'agent',
			'entity_plural' => 'agents',
		) );
		extract( $args );
		include es_locate_template( 'front/partials/entity-control.php' );
	}
}
add_action( 'es_agent_control', 'es_the_agent_control', 10 );

if ( ! function_exists( 'es_the_entity_control' ) ) {

	/**
	 * @param array $args
	 */
    function es_the_entity_control( $args = array() ) {
        $post_id = ! empty( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
        $entity = es_get_entity_by_id( $post_id );

	    $args = es_parse_args( $args, array(
		    'show_sharing' => true,
		    'show_wishlist' => true,
		    'is_full' => true,
		    'wishlist_confirm' => false,
		    'entity' => $entity::get_entity_name(),
		    'entity_plural' => es_get_entity_plural_name( $entity::get_entity_name() ),
	    ) );
	    extract( $args );
	    include es_locate_template( 'front/partials/entity-control.php' );
    }
}
add_action( 'es_entity_control', 'es_the_entity_control', 10 );

if ( ! function_exists( 'es_the_agent_agency_breadcrumbs' ) ) {

	/**
	 * Display breadcrumbs on single agent page.
	 *
	 * @param int $post_id
	 */
	function es_the_agent_agency_breadcrumbs( $post_id = 0 ) {

	}
}
add_action( 'es_agent_agency_breadcrumbs', 'es_the_agent_agency_breadcrumbs' );

if ( ! function_exists( 'es_the_single_entity_layout' ) ) {

	/**
	 * @param $post_id
	 */
	function es_the_single_entity_layout( $post_id = 0 ) {
        $entity = es_get_entity_by_id( $post_id );

        if ( $entity instanceof Es_Entity ) {
	        $entity_name = $entity::get_entity_name();
	        $layout = apply_filters( "es_single_{$entity_name}_current_layout", 'base', $post_id );

	        es_load_template( sprintf( "front/entity/layout/%s.php", $layout ), array(
		        'entity_name' => $entity::get_entity_name(),
		        'entity_name_plural' => es_get_entity_plural_name( $entity_name ),
	        ) );
        }
	}
}
add_action( 'es_single_agent_layout', 'es_the_single_entity_layout' );
add_action( 'es_single_agency_layout', 'es_the_single_entity_layout' );

if ( ! function_exists( 'es_the_entity_share_popup' ) ) {

	/**
	 * Render shares popup.
	 *
	 * @return void
	 */
	function es_the_entity_share_popup() {
        $entity = es_get_entity_by_id( get_the_ID() );

		es_load_template( 'front/popup/share.php', array(
			'entity_plural' => es_get_entity_plural_name( $entity::get_entity_name() ),
            'title' => sprintf( __( 'Share this %s', 'es' ), $entity::get_entity_name() ),
		) );
	}
}
add_action( 'es_after_single_content', 'es_the_entity_share_popup' );

add_action( 'es_single_entity_section', 'es_the_entity_section' );

if ( ! function_exists( 'es_the_entity_section_content' ) ) {

	/**
	 * Return section content.
	 *
	 * @param $section
	 * @param int $post_id
	 *
	 * @return mixed|void
	 */
	function es_the_entity_section_content( $section, $post_id = 0 ) {
		$content = null;
        $entity = es_get_entity_by_id( $post_id );

		if ( ! empty( $section['machine_name'] ) ) {

			switch ( $section['machine_name'] ) {
				case 'video':
					$video = $entity->video_link;
					if ( ! empty( $video ) ) {
						$content = wp_oembed_get( esc_url( $video ) );
					}
					break;

				default:
					$content = es_get_the_section_fields_html( $section, $post_id );
					break;
			}
		}

		return apply_filters( 'es_the_entity_section_content', $content, $section, $post_id );
	}
}

if ( ! function_exists( 'es_agent_listings' ) ) {

	/**
     * Render agent listings fro single agent page.
     *
	 * @return bool|void
	 */
    function es_agent_listings() {
        if ( ! es_is_agent( get_the_ID() ) ) return false;

        $attributes = apply_filters( 'es_agent_listings_attributes', array(
	        'disable_navbar' => true,
	        'layout' => 'grid-3',
	        'agent_id' => get_the_ID(),
	        'posts_per_page' => ests( 'agent_listings_per_page' ),
	        'show_categories' => true,
	        'ignore_search' => true,
	        'setup_postdata_post_id' => get_the_ID(),
        ) );

        $listings = es_get_shortcode_instance( 'es_my_listing', $attributes );

        echo $listings->get_content();
    }
}
add_action( 'es_agent_listings', 'es_agent_listings' );

if ( ! function_exists( 'es_agency_listings' ) ) {

	/**
     * Render agent listings fro single agent page.
     *
	 * @return bool|void
	 */
    function es_agency_listings() {
        if ( ! es_is_agency( get_the_ID() ) ) return false;

        $attributes = apply_filters( 'es_agency_listings_attributes', array(
	        'disable_navbar' => true,
	        'layout' => 'grid-3',
	        'agency_id' => get_the_ID(),
	        'posts_per_page' => ests( 'agency_listings_per_page' ),
	        'show_categories' => true,
	        'ignore_search' => true,
	        'setup_postdata_post_id' => get_the_ID(),
        ) );

        $listings = es_get_shortcode_instance( 'es_my_listing', $attributes );

        echo $listings->get_content();
    }
}
add_action( 'es_agency_listings', 'es_agency_listings' );

if ( ! function_exists( 'es_entity_reviews' ) ) {

	/**
	 * @return void
	 */
    function es_entity_reviews() {
        // If comments are open or we have at least one comment, load up the comment template.
	    if ( comments_open() || get_comments_number() ) {
		    global $post;

            if ( ! empty( $post ) ) {
                if ( ests( 'is_' . $post->post_type . '_comments_enabled' ) ) {
	                add_filter( 'comments_template', 'es_get_comments_template_path' );

	                comments_template();
                }
            }
	    }

	    add_filter( 'comments_open', '__return_false' );
    }
}
add_action( 'es_entity_reviews', 'es_entity_reviews' );

if ( ! function_exists( 'es_get_the_agent_description_section_title' ) ) {

	/**
	 * @return string
	 */
    function es_get_the_agent_description_section_title() {
        return sprintf( _x( 'About %s', 'frontend agent section title', 'es' ), get_the_title() );
    }
}

if ( ! function_exists( 'es_get_the_agent_listings_section_title' ) ) {

	/**
	 * @return string
	 */
	function es_get_the_agent_listings_section_title() {
		return sprintf( _x( 'Listings by %s', 'frontend agent section title', 'es' ), get_the_title() );
	}
}

if ( ! function_exists( 'es_get_the_entity_request_form_section_title' ) ) {

	/**
	 * @return string
	 */
	function es_get_the_entity_request_form_section_title() {
		return sprintf( _x( 'Contact %s', 'request form title', 'es' ), get_the_title() );
	}
}

if ( ! function_exists( 'es_get_the_agent_reviews_section_title' ) ) {

	/**
	 * @return string
	 */
	function es_get_the_agent_reviews_section_title() {
		if ( $number = get_comments_number() ) {
            return sprintf( _n( '%s Review', '%s Reviews', $number ), $number );
        } else {
            return __( 'No Reviews yet', 'es' );
        }
	}
}

if ( ! function_exists( 'es_get_agencies_list' ) ) {

	/**
	 * @return int[]|WP_Post[]
	 */
	function es_get_agencies_list() {
	    $result = array();

		$posts =  get_posts( array(
			'post_type' => Es_Agency::get_post_type_name(),
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby'     => 'title',
			'order'       => 'ASC',
		) );

		if ( $posts ) {
			$result = wp_list_pluck( $posts, 'post_title', 'ID' );
		}

		return apply_filters( 'es_get_agencies_list', $result );
	}
}

/**
 * @return array|bool
 */
function es_get_agents_list() {
    global $wpdb;

    $agents = $wpdb->get_results( "SELECT post_title, ID FROM {$wpdb->posts} WHERE post_type='agent' AND post_status='publish' ORDER BY post_title", ARRAY_A );

	if ( ! empty( $agents ) ) {
		$agents = wp_list_pluck( $agents, 'post_title', 'ID' );
	}

	return apply_filters( 'es_get_agents_list', $agents );
}

/**
 * @param $agent_id
 */
function es_agent_preferred_contact_render( $agent_id ) {
    $entity = es_get_agent( $agent_id );
    if ( $contact_config = $entity->get_preferred_contact_config() ) : ?>
        <div class="es-preferred-contact es-preferred-contact--<?php echo $contact_config['type']; ?>">
            <a target="_blank" href="<?php echo es_get_contact_link( $contact_config, $entity ); ?>"><?php echo $contact_config['label']; ?></a>
        </div>
    <?php endif;
}

/**
 * @return void
 */
function es_agency_our_team_section_content() {
    $shortcode = es_get_shortcode_instance( 'es_my_agents', array(
        'layout' => 'grid',
        'disable_navbar' => true,
        'posts_per_page' => ests( 'agency_agents_per_page' ),
        'show_page_title' => false,
        'agency_id' => get_the_ID(),
        'setup_postdata_post_id' => get_the_ID(),
    ) );

    $query = $shortcode->get_query();

	echo $query->have_posts() ? $shortcode->get_content() : '';
}
add_action( 'es_agency_our_team_section_content', 'es_agency_our_team_section_content' );

/**
 * @param $post_id
 * @param $section
 */
function es_entity_request_form_section( $post_id ) {
    $entity = es_get_entity_by_id( $post_id );

    if ( $entity instanceof Es_Emailed_Entity && ( $email = $entity->get_email() ) ) {
	    $config = array(
		    'message' => '',
		    'title' => es_get_the_entity_request_form_section_title(),
		    'post_id' => $entity->get_id(),
		    'recipient_type' => Es_Request_Form_Shortcode::SEND_OTHER,
		    'custom_email' => $email
        );

	    $shortcode = es_get_shortcode_instance( 'es_request_form', $config );

	    if ( $shortcode instanceof Es_Shortcode ) {
		    echo $shortcode->get_content();
	    }
    }
}
add_action( 'es_entity_request_form_section', 'es_entity_request_form_section', 10, 1 );

function es_user_get_default_image_url_avatar_pdf() {
    return ES_PLUGIN_URL . 'public/img/avatar.png';
}