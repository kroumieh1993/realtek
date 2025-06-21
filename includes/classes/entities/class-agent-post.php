<?php

/**
 * Class Es_Agent_Post
 *
 * @property $agency_id int
 * @property $user_id int
 * @property $avatar_id int
 * @property $status int
 * @property $rating int
 * @property $communication_method int
 * @property $position string
 * @property $user_email string
 * @property $post_title string
 * @property $post_status string
 * @property $contacts array
 */
class Es_Agent_Post extends Es_Post implements Es_Emailed_Entity {

	/**
	 * @var Es_User
	 */
	protected $_related_entity;

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'ID' => array(
				'system' => true,
				'label' => __( 'Agent ID', 'es' ),
			),
			'post_title' => array(
				'label' => __( 'Name *', 'es' ),
				'system' => true,
				'attributes' => array(
					'required' => 'required'
				),
				'order' => 20,
			),
			'post_status' => array(
				'system' => true,
			),
			'type' => array(
				'type' => 'select',
				'default_value' => 'agent',
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
			'user_email' => array(
				'label' => __( 'Email', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'contacts',
				'section_machine_name' => 'contacts',
				'type' => 'email',
				'system' => true,
				'related_entity' => true,
				'attributes' => array(
					'required' => 'required',
					'class' => 'js-es-user-email'
				),
				'before' => '<div class="es-preferred-wrapper">',
				'order' => 150,
			),
			'communication_method' => array(
				'tab_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'contacts',
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
				'order' => 155,
			),
			'has_user' => array(
				'label' => __( 'Create user account', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'disable_on_frontend' => true,
				'type' => 'switcher',
				'order' => 160,
				'attributes' => array(
					'data-toggle-container' => '#es-user-pwd',
					'class' => 'js-es-has-user',
				)
			),
			'first_name' => array(
				'before' => '<div id="es-user-pwd">',
				'label' => __( 'First name', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'system' => true,
				'related_entity' => true,
				'type' => 'text',
				'order' => 170,
			),
			'last_name' => array(
				'label' => __( 'Last name', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'system' => true,
				'related_entity' => true,
				'type' => 'text',
				'order' => 175,
			),
			'user_password' => array(
				'disable_on_frontend' => true,
				'label' => __( 'Password', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'minlength' => 8,
					'required' => 'required',
					'class' => 'js-es-user-pwd'
				),
				'system' => true,
				'related_entity' => true,
				'order' => 180,
				'type' => 'password',
				'skeleton' => "{before}
                                   <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                       <label for='{id}'>{label}{caption}<div class='es-input__wrap'>{input}</div>{description}</label>
                                   </div>
                               {after}",
				'description' => "<ul class='es-field__validate-list'>
                                <li class='es-validate-item es-validate-item__contain'>" . __( 'Can\'t contain the name or email address', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__length'>" . __( 'At least 8 characters', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__char'>" . __( 'Contains a number or symbol', 'es' ) . "</li>
                            </ul>",
			),

			'status' => array(
				'after' => '</div>',
				'disable_on_frontend' => true,
				'label' => __( 'User status', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'order' => 185,
				'related_entity' => true,
				'type' => 'radio-bordered',
				'default_value' => Es_User::STATUS_ACTIVE,
				'options' => array(
					Es_User::STATUS_ACTIVE => __( 'Active', 'es' ),
					Es_User::STATUS_DISABLED => __( 'Deactivated', 'es' ),
					Es_User::STATUS_UNAPPROVED => __( 'Unapproved', 'es' )
				),
			),

			'agency_id' => array(
				'label' => __( 'Agency', 'es' ),
				'tab_machine_name' => 'professional-info',
				'section_machine_name' => 'professional-info',
				'disable_on_frontend' => true,
				'formatter' => 'post-link',
				'type' => 'select',
				'order' => 60,
				'options_callback' => 'es_get_agencies_list',
				'attributes' => array(
					'placeholder' => __( 'Choose agency', 'es' ),
				),
			),
			'position' => array(
				'label' => __( 'Position', 'es' ),
				'tab_machine_name' => 'professional-info',
				'type' => 'text',
				'order' => 70,
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
				'frontend_tab_machine_name' => 'professional-info',
				'taxonomy' => true,
				'search_support' => true,
				'attributes' => array(
					'multiple' => 'multiple',
					'placeholder' => false,
					'class' => 'js-es-select2',
				),

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
								'name' => 'es_agent[contacts][{#index}][phone][code]',
							),
							'default_value' => ests( 'country' )
						),
						'tel_config' => array(
							'type' => 'tel',
							'attributes' => array(
								'id' => 'es-field-tel-{#index}',
								'name' => 'es_agent[contacts][{#index}][phone][tel]',
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
				'section_machine_name' => 'contacts',
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

			'social_links' => array(
				'label' => __( 'Social links', 'es' ),
				'section_machine_name' => 'contacts',
				'formatter' => 'social_links',
				'order' => 180,
				'default_value' => 1,
			),

			'post_content' => array(
				'type' => 'textarea',
				'label' => __( 'Describe this agent as professional', 'es' ),
				'system' => true,
				'frontend_name' => false,
				'section_machine_name' => 'description',
//				'tab_machine_name' => 'description',
				'use_formatter_label' => false,
				'show_more_label' => __( 'Show all', 'es' ),
				'enable_counter' => true,
				'attributes' => array(
					'maxlength' => 500,
					'rows' => 5,
				),
			),

			'video_link' => array(
				'label' => __( 'Link to agent\'s video from YouTube or Vimeo', 'es' ),
				'tab_machine_name' => 'video',
				'section_machine_name' => 'video',
				'formatter' => 'video',
				'type' => 'url',
				'use_formatter_label' => false,
			),

			'user_id' => array(
				'label' => __( 'User', 'es' ),
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

			'mls_update_date' => array(
				'label' => __( 'MLS Update date', 'es' ),
			),

			'profile_id' => array(
				'label' => __( 'MLS Profile ID', 'es' ),
			),

			'featured_image' => array(
				'label' => __( 'Agent logo', 'es' ),
				'rets_objectable' => true,
			),
		);

		$networks = es_get_social_networks_list();

		$order = 190;

		foreach ( $networks as $network => $label ) {
			$order += 10;

			$fields[ $network ] = array(
				'label' => sprintf( __( '%s profile link' ), $label ),
				'type' => 'url',
				'order' => $order,
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

			$fields[ $key ]['name'] = ! isset( $config['name'] ) && ! empty( $fields[ $key ]['label'] ) ? $fields[ $key ]['label'] : '';
		}

		return apply_filters( 'es_agent_default_fields', $fields );
	}

	/**
	 * Check related user entity.
	 *
	 * @return bool|WP_User
	 */
	public function has_user_entity() {
		return $this->user_id && get_user_by( 'id', $this->user_id );
	}

	/**
	 * Check related user entity.
	 *
	 * @return bool|Es_User
	 */
	public function get_user_entity() {
		if ( empty( $this->_related_entity ) ) {
			$this->_related_entity = $this->has_user_entity() ?
				es_get_entity( 'agent_user', $this->user_id ) : null;
		}

		return $this->_related_entity;
	}

	/**
	 * @return bool
	 */
	public function has_agency() {
		$has_agency = es_is_agency( $this->agency_id );

		return apply_filters( 'es_has_' . static::get_entity_name() . '_agency', $has_agency, $this );
	}

	/**
	 * Return user agency.
	 *
	 * @return Es_Agency|null
	 */
	public function get_agency() {
		$agency = $this->has_agency() ? es_get_agency( $this->agency_id ) : null;
		return apply_filters( 'es_get_' . static::get_entity_name() . '_agency', $agency, $this );
	}

	/**
	 * @return string|void
	 */
	public function get_entity_prefix() {
		return 'es_agent_';
	}

	/**
	 * @return mixed|string
	 */
	public static function get_post_type_name() {
		return 'agent';
	}

	/**
	 * @return string|null
	 */
	public static function get_entity_name() {
		return 'agent';
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

	/**
	 * @return mixed|string|void
	 */
	public function get_user_status() {
		if ( ! $this->has_user_entity() ) {
			$status = __( 'Not exist', 'es' );
		} else {
			$status = $this->status ? __( 'Active' ) : __( 'Deativated', 'es' );
		}

		return apply_filters( 'es_get_user_status', $status, $this );
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
			$data['entity_id'] = $post_id;

			$user_id = $entity->save_related_entity( $data );

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}

			$entity->save_fields( $data );
		}

		return $post_id;
	}

	/**
	 * @param $data
	 *
	 * @return void
	 */
	public function save_fields( $data ) {
		$data['entity_id'] = $this->get_id();

		if ( ! empty( $data['has_user'] ) ) {
			$status = $this->save_related_entity( $data );
			if ( is_wp_error( $status ) ) {
				$this->delete_field_value( 'has_user' );
			}
		} else if ( empty( $data['has_user'] ) && ! empty( $data['user_id'] ) ) {
			$this->save_field_value( 'user_id', $data['user_id'] );
			$this->save_field_value( 'has_user', 1 );
			$agent = es_get_agent_user( $data['user_id'] );
			$agent->save_field_value( 'post_id', $this->get_id() );
		}

		if ( ! empty( $data['agency_id'] ) ) {
			global $wpdb;
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => 'es_agency_agent_id', 'meta_value' => $this->get_id() ) );

			add_post_meta( $data['agency_id'], 'es_agency_agent_id', $this->get_id() );
		}

		parent::save_fields( $data );
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 */
	public function save_field_value( $field, $value ) {
		$finfo = static::get_field_info( $field );
		$related_entity = $this->get_user_entity();

		if ( ! empty( $finfo['system'] ) ) {
			if ( ! empty( $finfo['related_entity'] ) ) {
				if ( $related_entity ) {
					$related_entity->save_field_value( $field, $value );
				} else {
					if ( $field !== 'user_password' ) {
						parent::save_field_value( $field, $value );
					}
				}
			} else {
				if ( $this->{ $field } != $value ) {
					wp_update_post( array( 'ID' => $this->get_id(), $field => $value ) );
				}
			}
		} else {
			if ( ! empty( $finfo['related_entity'] ) ) {
				if ( $related_entity ) {
					$related_entity->save_field_value( $field, $value );
				}
			} else {
				parent::save_field_value( $field, $value );
			}
		}
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {
		$finfo = static::get_field_info( $name );

		if ( ! empty( $finfo['related_entity'] ) ) {
			$entity = $this->get_user_entity();

			return $entity ? $entity->{$name} : $this->get_field_value( $name );
		}

		return parent::__get( $name );
	}

	/**
	 * @param $name
	 *
	 * @return mixed|void
	 */
	public function get_field_value( $field ) {
		$f_info = static::get_field_info( $field );

		if ( ! empty( $f_info['related_entity'] ) ) {
			$related_entity = $this->get_user_entity();

			if ( $related_entity ) {
				$value = $related_entity->{$field};
			} else {
				$type = static::get_entity_type();
				$single = ! empty( $f_info['is_single_meta'] ) || ! isset( $f_info['is_single_meta'] );

				// get_user_meta, get_post_meta functions call.
				$value = call_user_func(
					"get_{$type}_meta",
					$this->get_id(),
					$this->get_entity_prefix() . $field,
					$single
				);

				$value = ( ( is_string( $value ) && ! strlen( $value ) ) || ( ! is_string( $value ) && empty( $value ) ) ) && isset( $f_info['default_value'] ) ? $f_info['default_value'] : $value;
			}
		} else {
			$value = parent::get_field_value( $field );
		}

		return $value;
	}

	/**
	 * @param $data
	 *
	 * @return int|WP_Error
	 */
	public function save_related_entity( $data ) {
		$user_data = array(
			'role' => 'agent',
			'user_pass' => $data['user_password'],
			'user_login' => $data['user_email'],
			'user_email' => $data['user_email'],
			'first_name' => ! empty( $data['first_name'] ) ? $data['first_name'] : '',
			'last_name' => ! empty( $data['last_name'] ) ? $data['last_name'] : '',
		);

		if ( ! empty( $data['entity_id'] ) ) {
			$entity = new static( $data['entity_id'] );
			$user_data['ID'] = $entity->user_id;

			if ( ! empty( $user_data['ID'] ) ) {
				if ( ! empty( $data['user_password'] ) ) {
					$user_data['user_pass'] = wp_hash_password( $data['user_password'] );
				} else {
					unset( $user_data['user_pass'] );
				}
			}
		}

		$user_id = wp_insert_user( $user_data );

		if ( is_numeric( $user_id ) ) {
			$this->save_field_value( 'user_id', $user_id );

			$agent_user = es_get_agent_user( $user_id );
			$agent_user->save_fields( $data );
			$agent_user->save_field_value( 'post_id', $data['entity_id'] );

			if ( has_post_thumbnail( $data['entity_id'] ) ) {
				$agent_user->save_field_value( 'avatar_id', get_post_thumbnail_id( $data['entity_id'] ) );
			}
		}

		return $user_id;
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
		return es_get_the_agent_avatar( $this->get_id(), $size );
	}

	/**
	 * @return mixed|void
	 */
	public function get_preferred_contact_config() {
		$config = array();

		if ( $this->communication_method && $this->user_email ) {
			$config = array(
				'type' => 'mail',
				'value' => $this->user_email,
				'label' => $this->user_email
			);
		} else if ( ! empty( $this->contacts ) ) {
			foreach ( $this->contacts as $contact ) {
				if ( ! empty( $contact['communication_method'] ) && ! empty( $contact['phone'] ) ) {
					$tel = is_string( $contact['phone'] ) ? $contact['phone'] : $contact['phone']['tel'];
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
	 * @return int[]|WP_Post[]
	 */
	public function get_property_ids() {
		return get_posts( array(
			'post_type' => 'properties',
			'post_status' => 'publish',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'es_property_agent_id',
					'value' => $this->get_id()
				)
			),
		) );
	}

	/**
	 * @return int
	 */
	public function get_property_qty() {
		$query_args = apply_filters( 'es_' . static::get_entity_name() . '_get_active_num_query', array(
			'post_type' => 'properties',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'es_property_agent_id',
					'value' => $this->get_id()
				)
			),
		) );

		$query = new WP_Query( $query_args );

		return apply_filters( 'es_' . static::get_entity_name() . '_property_qty', $query->found_posts );
	}

	/**
	 * @return string
	 */
	public function get_email() {
		return $this->user_email;
	}
}
