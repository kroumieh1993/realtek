<?php

/**
 * Class Es_Agency.
 *
 * @property $avatar_id int
 * @property $agent_id int
 * @property $address string
 * @property $post_title string
 * @property $post_status string
 * @property $contacts array
 * @property $communication_method string
 * @property $email string
 */
class Es_Agency extends Es_Post implements Es_Emailed_Entity {

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'ID' => array(
				'system' => true,
				'label' => __( 'Agency ID', 'es' ),
			),
			'post_title' => array(
				'label' => __( 'Name *', 'es' ),
				'system' => true,
				'attributes' => array(
					'required' => 'required'
				),
				'order' => 20
			),
			'post_status' => array(
				'system' => true,
				'label' => __( 'Post status', 'es' )
			),
			'type' => array(
				'type' => 'select',
				'default_value' => 'agency',
				'search_support' => true,
				'options' => array(
					'agent' => __( 'Agent', 'es' ),
					'agency' => __( 'Agency', 'es' ),
				),
				'search_settings' => array(
					'type' => 'select',
					'label' => false,
					'values' => array(
						'agent' => __( 'Agent', 'es' ),
						'agency' => __( 'Agency', 'es' ),
					),
				),
			),
			'email' => array(
				'label' => __( 'Email *', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'section_machine_name' => 'contacts',
				'attributes' => array(
					'required' => 'required'
				),
				'type' => 'email',
				'before' => '<div class="es-preferred-wrapper">',
				'order' => 30,
			),
			'communication_method' => array(
				'tab_machine_name' => 'basic-facts',
				'type' => 'radio',
				'value' => 'email',
				'enable_hidden_input' => true,
				'options' => array(
					'email' => __( 'Preferred communication method', 'es' ),
				),
				'attributes' => array(
					'class' => 'js-es-preferred-radio',
				),
				'after' => '</div>',
				'order' => 35,
			),
			'licenses' => array(
				'label' => __( 'Real estate license', 'es' ),
				'tab_machine_name' => 'professional-info',
				'section_machine_name' => 'professional-info',
				'type' => 'repeater',
				'formatter' => 'single-repeater',
				'enable_hidden_input' => true,
				'add_button_label' => __( 'Add one more license', 'es' ),
				'delete_button' => "<a href='#' class='js-es-repeater__delete-item es-btn--delete'>
										<span class='es-icon es-icon_trash'></span>
									</a>",
				'fields' => array(
					'license' => array(
						'type' => 'text',
					),
				),
				'default_value' => array(
					array(
						'license' => ''
					)
				),
				'order' => 80,
			),
			'es_service_area' => array(
				'label' => __( 'Service areas', 'es' ),
				'section_machine_name' => 'professional-info',
				'taxonomy' => true,
				'search_support' => true,

				'search_settings' => array(
					'type' => 'select',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_service_area', true ),
					),
					'label' => false,
					'attributes' => array(
						'multiple' => 'multiple',
						'data-placeholder' => __( 'Service area', 'es' ),
					),
				),
				'type' => 'select',
			),
			'rating' => array(
				'label' => __( 'Rating', 'es' ),
				'type' => is_admin() ? 'hidden' : 'rating',
				'description' => __( 'Choose a rating', 'es' ),
				'order' => 110,
				'default_value' => 0,
				'tab_machine_name' => 'basic-facts',
			),
			'reviews_count' => array(
				'label' => __( 'Reviews count', 'es' ),
				'default_value' => 0,
				'tab_machine_name' => 'basic-facts',
				'type' => 'hidden',
			),
			'agent_id' => array(
				'label' => __( 'Agent', 'es' ),
				'tab_machine_name' => 'team',
				'type' => 'entities-list',
				'is_single_meta' => false,
				'options_callback' => 'es_get_agents_list',
				'ajax-item-action' => 'es_get_agent_list_item',
				'items_callback' => 'es_get_the_agents_config',
				'attributes' => array(
					'placeholder' => ' ',
					'data-request' => es_esc_json_attr( array(
						'action' => 'get_select2_agents',
						'_ajax_nonce' => wp_create_nonce( 'get_select2_agents' )
					) ),
					'data-placeholder' => __( 'Start type agent name', 'es' ),
					'data-formatNoMatches' => __( 'Agent not found', 'es' ),
				),
			),
			'contacts' => array(
				'label' => __( 'Contacts', 'es' ),
				'frontend_name' => __( 'Phone', 'es' ),
				'type' => 'repeater',
				'tab_machine_name' => 'contacts',
				'formatter' => 'phones',
				'section_machine_name' => 'contacts',
				'add_button_label' => __( 'Add one more phone', 'es' ),
				'order' => 150,
				'enable_hidden_input' => true,
				'use_formatter_label' => true,
				'default_value' => array(
					array(
						'phone' => '',
						'communication_method' => '',
						'is_whatsapp_enabled' => '',
					)
				),
				'fields' => array(
					'phone' => array(
						'label' => __( 'Phone', 'es' ),
						'type' => 'phone',
						'is_country_code_disabled' => ests( 'is_tel_code_disabled' ),
						'codes' => es_esc_json_attr( ests_values( 'phone_codes' ) ),
						'icons' => es_esc_json_attr( ests_values( 'country_icons' ) ),
						'code_config' => array(
							'options' => ests_values( 'country' ),
							'attributes' => array(
								'id' => 'es-field-code-{#index}',
								'name' => 'es_agency[contacts][{#index}][phone][code]',
							),
							'default_value' => ests( 'country' )
						),
						'tel_config' => array(
							'attributes' => array(
								'id' => 'es-field-tel-{#index}',
								'name' => 'es_agency[contacts][{#index}][phone][tel]',
							),
						),
						'before' => '<div class="es-preferred-wrapper">',
					),
					'communication_method' => array(
						'type' => 'radio',
						'options' => array(
							'1' => __( 'Preferred communication method', 'es' ),
						),
						'attributes' => array(
							'class' => 'js-es-preferred-radio',
						),
						'after' => '</div>',
					),
					'is_whatsapp_enabled' => array(
						'label' => __( 'Create WhatsApp link', 'es' ),
						'type' => 'switcher',
					),
				),
			),
			'website' => array(
				'label' => __( 'Website', 'es' ),
				'tab_machine_name' => 'contacts',
				'section_machine_name' => 'professional-info',
				'type' => 'repeater',
				'formatter' => 'links',
				'enable_hidden_input' => true,
				'add_button_label' => __( 'Add one more link', 'es' ),
				'delete_button' => "<a href='#' class='js-es-repeater__delete-item es-btn--delete'>
										<span class='es-icon es-icon_trash'></span>
									</a>",
				'fields' => array(
					'website' => array(
						'type' => 'url',
					),
				),
				'default_value' => array(
					array(
						'website' => ''
					)
				),
				'order' => 160,
			),
			'address' => array(
				'label' => __( 'Address', 'es' ),
				'tab_machine_name' => 'contacts',
				'section_machine_name' => 'contacts',
				'type' => 'text',
				'order' => 170,
			),

			'post_content' => array(
				'label' => __( 'Describe this agency as professional', 'es' ),
				'system' => true,
				'frontend_name' => false,
				'section_machine_name' => 'description',
				'use_formatter_label' => false,
				'show_more_label' => __( 'Show all', 'es' ),
			),

			'video_link' => array(
				'label' => __( 'Link to agency\'s video from YouTube or Vimeo', 'es' ),
				'tab_machine_name' => 'video',
				'section_machine_name' => 'video',
				'formatter' => 'video',
				'type' => 'url',
				'use_formatter_label' => false,
			),

			'keywords' => array(
				'label' => __( 'Keywords', 'es' ),
				'search_support' => true,
				'type' => 'text',
				'search_settings' => array(
					'label' => false,
					'attributes' => array(
						'placeholder' => _x( 'Name', 'agent search name field', 'es' ),
					)
				)
			),
		);

		$networks = es_get_social_networks_list();

		foreach ( $networks as $network => $label ) {
			$fields[ $network ] = array(
				'label' => sprintf( __( '%s profile link' ), $label ),
				'type' => 'url',
				'tab_machine_name' => 'social-networks',
			);
		}

		foreach ( $fields as $key => $field ) {
			if ( ! isset( $field['frontend_name'] ) && ! empty( $field['label'] ) ) {
				if ( ! isset( $fields[ $key ]['frontend_name'] ) ) {
					$fields[ $key ]['frontend_name'] = str_replace( ' *', '', $field['label'] );
				}

				if ( ! isset( $fields[ $key ]['use_formatter_label'] ) ) {
					$fields[ $key ]['use_formatter_label'] = true;
				}
			}
		}

		return apply_filters( 'es_agency_default_fields', $fields );
	}

	/**
	 * @return string
	 */
	public function get_entity_prefix() {
		return 'es_agency_';
	}

	/**
	 * @return Es_Agent_Post[]
	 */
	public function get_agents() {
		$entities = array();

		if ( $this->agent_id ) {
			foreach ( $this->agent_id as $agent_id ) {
				if ( ! es_is_agent( $agent_id ) ) continue;

				$entities[] = es_get_agent( $agent_id );
			}
		}

		return apply_filters( 'es_agency_get_agents', $entities );
	}

	/**
	 * @return mixed|string
	 */
	public static function get_post_type_name() {
		return 'agency';
	}

	/**
	 * @return string|null
	 */
	public static function get_entity_name() {
		return 'agency';
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_status() {
		$statuses = apply_filters( 'es_' . static::get_entity_name() . '_get_statuses_list', array(
			'publish' => __( 'Active', 'es' ),
			'draft' => __( 'Inactive', 'es' ),
			'trash' => __( 'Trashed', 'es' ),
		) );

		return ! empty( $statuses[ $this->post_status ] ) ? $statuses[ $this->post_status ] : __( 'None', 'es' );
	}

	public function get_rating() {
		return $this->rating;
	}

	/**
	 * @param string $size
	 *
	 * @return mixed|void
	 */
	public function get_avatar( $size = 'thumbnail' ) {
		return es_get_the_agency_avatar( $this->get_id(), $size );
	}

	/**
	 * @return mixed|void
	 */
	public function get_preferred_contact_config() {
		$config = array();

		if ( $this->communication_method && $this->email ) {
			$config = array(
				'type' => 'mail',
				'value' => $this->email,
				'label' => $this->email
			);
		} else if ( ! empty( $this->contacts ) && is_array( $this->contacts ) ) {
			foreach ( $this->contacts as $contact ) {
				if ( ! empty( $contact['communication_method'] ) && ! empty( $contact['phone'] ) ) {
					$tel = is_string( $contact['phone'] ) ? $contact['phone'] :  $contact['phone']['tel'];
					$config = array(
						'type'=> ! empty( $contact['is_whatsapp_enabled'] ) ? 'whatsapp' : 'tel',
						'label' => ! empty( $contact['is_whatsapp_enabled'] ) ?
							'<span class="es-icon es-icon_whatsapp"></span><span>' . __( 'WhatsApp message', 'es' ) . '</span>' : $tel,
						'value' => $tel,
					);
					break;
				}
			}
		}

		return apply_filters( 'es_get_preferred_contact_config', $config, $this );
	}

	/**
	 * @return bool
	 */
	public function has_avatar() {
		return ! empty( $this->avatar_id );
	}

    /**
     * @param $data
     * @return mixed|void
     */
	public function save_fields( $data ) {
	    if ( ! empty( $data['agent_id'] ) ) {
	        global $wpdb;
	        $wpdb->delete( $wpdb->postmeta, array( 'es_agent_agency_id' => $this->get_id() ) );

	        foreach ( $data['agent_id'] as $agent_id ) {
	            $agent = es_get_agent( $agent_id );
	            $agent->save_field_value( 'agency_id', $this->get_id() );
            }
        }

        parent::save_fields( $data );
    }

    /**
	 * @param $data
	 *
	 * @return int|WP_Error
	 */
	public static function save( $data ) {
		$post_arr = array(
			'post_title' => $data['post_title'],
			'post_name' => sanitize_title( $data['post_title'] ),
			'post_type' => static::get_post_type_name(),
			'post_status' => 'publish',
		);

		if ( ! empty( $data['entity_id'] ) ) {
			$post_arr['ID'] = $data['entity_id'];
		}

		$post_id = wp_insert_post( $post_arr, true );

		if ( ! is_wp_error( $post_id ) ) {
			$entity = new static( $post_id );

			if ( ! empty( $_FILES['avatar'] ) ) {
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
				}

				$attachment_id = media_handle_sideload( $_FILES['avatar'], -1 );

				if ( $attachment_id ) {
					if ( ! $attachment_id instanceof WP_Error ) {
						$data['avatar_id'] = $attachment_id;
					}
				}
			}

			$entity->save_fields( $data );
		}

		return $post_id;
	}

	/**
	 * @return int
	 */
	public function get_property_qty() {
		$qty = 0;

		$query_args = apply_filters( 'es_' . static::get_entity_name() . '_get_active_num_query', array(
			'post_type' => 'properties',
			'post_status' => 'publish',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'es_property_agency_id',
					'value' => $this->get_id()
				)
			),
		) );

		$ids = get_posts( $query_args );
		$ids = $ids ? $ids : array();

		if ( $agents = $this->get_agents() ) {
			foreach ( $agents as $agent ) {
				if ( $properties_ids = $agent->get_property_ids() ) {
					$ids = array_merge( $ids, $properties_ids );
				}
			}
		}

		if ( $ids ) {
			$ids = array_unique( $ids );
			$qty = $ids ? count( $ids ) : $qty;
		}

		return apply_filters( 'es_' . static::get_entity_name() . '_property_qty', $qty );
	}

	/**
	 * @return string
	 */
	public function get_email() {
		return $this->email;
	}
}
