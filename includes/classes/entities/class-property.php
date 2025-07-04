<?php

/**
 * Class Es_Property.
 *
 * @property bool $is_open_house
 * @property bool $is_address_disabled
 * @property string $price
 * @property float $latitude
 * @property float $longitude
 * @property string $call_for_price
 * @property string $price_per_sqft
 * @property string $is_manual_address
 * @property array $gallery
 * @property array $video
 * @property int $state State term ID.
 * @property int $city City term ID.
 * @property int $province Province term ID.
 * @property int $country Country term ID.
 * @property int $agency_id Country term ID.
 * @property string $postal_code
 * @property int[] $agent_id
 */
class Es_Property extends Es_Post {

    /**
	 * Return entity prefix string.
	 *
	 * @return string
	 */
	public function get_entity_prefix() {
		return 'es_property_';
	}

    /**
     * @return string|null
     */
    public static function get_entity_name() {
        return 'property';
    }

	/**
	 * @return bool
	 */
	public function has_agency() {
		$has_agency = es_is_agency( $this->agency_id );

		return apply_filters( 'es_has_' . static::get_entity_name() . '_agency', $has_agency, $this );
	}

	/**
	 * @return Es_Agency
	 */
    public function get_agency() {
    	return es_get_agency( $this->agency_id );
    }

    /**
	 * Return entities fields list.
	 *
	 * @return array
	 */
	public static function get_default_fields() {
        $max_upload_size = wp_max_upload_size();
        if ( ! $max_upload_size ) {
            $max_upload_size = 0;
        }

		$fields = array(
			'date_added' => array(
				'fb_settings' => array(
					'disable_edit' => true,
				),
				'label' => __( 'Date added', 'es' ),
				'section_machine_name' => 'basic-facts',
                'is_visible' => ests( 'is_date_added_enabled' ),
                'formatter' => 'date_added',
			),
			'es_location' => array(
				'label' => __( 'Location', 'es' ),
				'taxonomy' => true,
				'compare_support' => false,
			),
			'es_tag' => array(
				'label' => __( 'Tags', 'es' ),
				'taxonomy' => true,
			),
			'es_category' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_tab_field' => true,
				),
				'search_settings' => array(
				    'type' => ests( 'search_es_category_field_mode' ),
				    'values_callback' => array(
				        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_category', true ),
                    ),
					'attributes' => array(
						'multiple' => ests( 'search_es_category_field_mode' ) == 'checkboxes-bordered' ? 'multiple' : false,
						'data-placeholder' => __( 'All categories', 'es' ),
					),
                ),
				'type' => 'radio-bordered',
				'label' => __( 'Category', 'es' ),
                'taxonomy' => true,
				'is_pdf_visible' => true,
				'search_support' => true,
				'section_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'basic-facts',
				'frontend_form_name' => __( 'What kind of property are you marketing?', 'es' ),
			),
            'es_rent_period' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_tab_field' => true,
				),
				'search_settings' => array(
				    'type' => ests( 'search_es_rent_period_field_mode' ),
				    'values_callback' => array(
				        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_rent_period', true ),
                    ),
				    'attributes' => array(
					    'data-placeholder' => __( 'All', 'es' ),
					    'multiple' => ests( 'search_es_rent_period_field_mode' ) == 'checkboxes-bordered' ? 'multiple' : false,
				    ),
                ),
				'type' => 'radio-bordered',
                'taxonomy' => true,
				'label' => __( 'Rent Period', 'es' ),
				'search_support' => true,
				'section_machine_name' => 'basic-facts',
				'is_pdf_visible' => true,
			),
			'es_type' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_tab_field' => true,
				),
                'search_settings' => array(
                    'type' => ests( 'search_es_type_field_mode' ),
                    'values_callback' => array(
                        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_type', true ),
                    ),
                    'attributes' => array(
                        'data-placeholder' => __( 'All home types', 'es' ),
                        'multiple' => ests( 'search_es_type_field_mode' ) == 'checkboxes-bordered' ? 'multiple' : false,
                    ),
                ),
				'type' => 'radio-bordered',
                'taxonomy' => true,
				'label' => __( 'Type', 'es' ),
                'search_support' => true,
				'is_pdf_visible' => true,
				'section_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'basic-facts',
				'frontend_form_name' => __( 'What is your property type?', 'es' ),
			),
			'es_status' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_tab_field' => true,
				),
				'search_settings' => array(
					'type' => ests( 'search_es_status_field_mode' ),
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_status', true ),
					),
					'attributes' => array(
						'data-placeholder' => __( 'All statuses', 'es' ),
						'multiple' => ests( 'search_es_status_field_mode' ) == 'checkboxes-bordered' ? 'multiple' : false,
					),
				),
				'type' => 'radio-bordered',
				'taxonomy' => true,
				'label' => __( 'Status', 'es' ),
				'search_support' => true,
				'is_pdf_visible' => true,
				'section_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'basic-facts',
				'frontend_form_name' => __( 'What is your property status?', 'es' ),
			),
			'bedrooms' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
                'formatter' => 'beds',
				'is_single_page_formatter_disabled' => true,
				'is_pdf_visible' => true,
                'search_settings' => array(
                    'type' => 'radio-bordered',
                    'attributes' => array(
                        'data-single_unit' => __( 'bed', 'es' ),
                        'data-plural_unit' => __( 'beds', 'es' ),
                    )
                ),
                'search_support' => true,
				'type' => 'incrementer',
				'admin_type' => 'incrementer',
				'label' => __( 'Bedrooms', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'before' => is_admin() ? "<h3>" . __( 'Basic facts', 'es' ) . "</h3>" : '',
			),
			'bathrooms' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'formatter' => 'baths',
				'is_single_page_formatter_disabled' => true,
				'is_pdf_visible' => true,
				'label' => __( 'Bathrooms', 'es' ),
				'type' => 'incrementer',
				'admin_type' => 'incrementer',
                'search_settings' => array(
                    'type' => 'radio-bordered',
                    'attributes' => array(
                        'data-single_unit' => __( 'bath', 'es' ),
                        'data-plural_unit' => __( 'baths', 'es' ),
                    )
                ),
                'search_support' => true,
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
			),
			'half_baths' => array(
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				'label' => __( 'Half baths', 'es' ),
				'type' => 'incrementer',
				'admin_type' => 'incrementer',
                'search_settings' => array(
                    'type' => 'radio-bordered',
                ),
                'formatter' => 'half_baths',
				'is_single_page_formatter_disabled' => true,
                'search_support' => true,
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'is_pdf_visible' => true,
			),
			'total_rooms' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
                'search_support' => true,
				'type' => 'incrementer',
				'admin_type' => 'incrementer',
				'label' => __( 'Total rooms', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'is_pdf_visible' => true,
			),
			'floors' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'formatter' => 'floors',
				'is_single_page_formatter_disabled' => true,
                'search_support' => true,
                'search_settings' => array(
                    'type' => 'range',
                ),
				'type' => 'incrementer',
				'admin_type' => 'incrementer',
				'label' => __( 'Floors', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'is_pdf_visible' => true,
			),
			'floor_level' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'incrementer',
                'search_support' => true,
                'search_settings' => array(
                    'type' => 'range',
                ),
				'admin_type' => 'incrementer',
				'label' => __( 'Floor level', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'is_pdf_visible' => true,
			),
			'area' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'area',
                'search_settings' => array(
                    'type' => 'range',
                ),
                'use_formatter_label' => true,
                'search_support' => true,
				'formatter' => 'area',
				'is_pdf_visible' => true,
				'label' => is_admin() ? sprintf( __( 'Area, %s', 'es' ), ests_label( 'area_unit' ) ) : __( 'Area', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
			),
			'lot_size' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'lot_size',
                'search_settings' => array(
                    'type' => 'range',
                ),
                'use_formatter_label' => true,
                'search_support' => true,
				'formatter' => 'lot_size',
                'label' => is_admin() ? sprintf( __( 'Lot size, %s', 'es' ), ests_label( 'lot_size_unit' ) ) : __( 'Lot size', 'es' ),
				'section_machine_name' => 'basic-facts',
                'tab_machine_name' => 'basic-facts',
				'after' => '<div class="es-field-break"></div>',
				'is_pdf_visible' => true,
			),
			'year_built' => array(
				'label' => __( 'Year built', 'es' ),
				'type' => 'number',
				'search_support' => true,
				'section_machine_name' => 'basic-facts',
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'placeholder' => __( 'Unknown', 'es' ),
				),
				'is_pdf_visible' => true,
			),
			'year_remodeled' => array(
				'label' => __( 'Year remodeled', 'es' ),
				'type' => 'number',
				'search_support' => true,
				'section_machine_name' => 'basic-facts',
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'placeholder' => __( 'Unknown', 'es' ),
				),
				'after' => '<div class="es-field-break"></div>',
				'is_pdf_visible' => true,
			),
			'is_open_house' => array(
				'label' => __( 'Open house', 'es' ),
				'admin_type' => 'switcher',
				'type' => 'checkbox',
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'data-toggle-container' => '.es-field__is_appointment_only'
				),
				'description' => __( 'Scheduled period of time in which property designated to be open for viewing by potential clients', 'es' ),
				'after' => '<div class="es-field-break"></div>',
				'order' => 200,
				'compare_support' => false,
			),
			'is_appointment_only' => array(
				'label' => __( 'By appointment only', 'es' ),
				'admin_type' => 'switcher',
				'type' => 'checkbox',
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'data-toggle-container' => '.es-field__appointments'
				),
				'after' => '<div class="es-field-break"></div>',
				'order' => 201,
				'compare_support' => false,
			),
			'appointments' => array(
				'label' => __( 'Open House', 'es' ),
				'type' => 'repeater',
				'item_wrapper' => "<div class='es-field-row es-field-row--3 js-es-repeater-item es-repeater-item'>%s{delete}</div>",
				'add_button_label' => __( 'Add open house', 'es' ),
				'delete_button' => "<span class='js-es-repeater__delete-item es-repeater__delete-item es-icon es-icon_trash'></span>",
                'section_machine_name' => 'open-house',
			    'add_button' => "<button type='button' class='js-es-repeater__add-item es-btn es-btn--default es-btn--add-item es-btn--third es-btn--small'>
								<span class='es-icon es-icon_plus'></span>
								{button_label}
							</button>",
                'tab_machine_name' => 'basic-facts',
				'formatter' => 'appointments',
				'enable_hidden_input' => true,
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				// Ignore <ul> wraper on single property page.
				'ignore_field_wrapper' => true,
				'fields' => array(
					'date' => array(
						'label' => _x( 'Date', 'open house date', 'es' ),
						'type' => 'date',
						'attributes' => array(
							'placeholder' => ests( 'date_format' ),
						),
					),
					'start_time' => array(
						'label' => _x( 'Start time', 'open house time', 'es' ),
						'type' => 'select',
						'options' => es_get_times_array(),
					),
					'end_time' => array(
						'label' => _x( 'End time', 'open house time', 'es' ),
						'type' => 'select',
						'options' => es_get_times_array(),
					),
				),
				'order' => 202,
				'compare_support' => false,
			),
			'rooms' => array(
				'fb_settings' => array(
					'disable_tab_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'repeater',
				'label' => __( 'Rooms', 'es' ),
				'hide_name_in_pdf' => true,
				'enable_hidden_input' => true,
				'tab_machine_name' => 'rooms',
				'is_pdf_visible' => true,
				'section_machine_name' => 'rooms',
				'formatter' => 'rooms',
				'compare_support' => false,
				'fields' => array(
					'type' => array(
						'before' => "<div class='es-row'><div class='es-col'>",
						'type' => 'text',
						'label' => __( 'Room type', 'es' ),
						'after' => '</div>',
						'is_table_col' => true,
					),
					'dimensions' => array(
						'type' => 'text',
						'label' => __( 'Dimensions', 'es' ),
						'before' => '<div class="es-col">',
						'after' => '</div>',
						'is_table_col' => true,
					),
					'area' => array(
						'type' => 'text',
						'label' => __( 'Area', 'es' ),
						'before' => '<div class="es-col">',
						'after' => '</div>',
						'is_table_col' => true,
					),
					'level' => array(
						'type' => 'text',
						'label' => __( 'Level', 'es' ),
						'before' => '<div class="es-col">',
						'after' => '</div>',
						'is_table_col' => true,
					),
					'length' => array(
						'type' => 'text',
						'label' => __( 'Length', 'es' ),
						'before' => '<div class="es-col">',
						'after' => '</div>',
						'is_table_col' => true,
					),
					'width' => array(
						'type' => 'text',
						'label' => __( 'Width', 'es' ),
						'before' => '<div class="es-col">',
						'after' => '</div></div>',
						'is_table_col' => true,
					),
				),
			),
			'assign_entity_type' => array(
				'label' => __( 'Who will be in charge of the property? *', 'es' ),
				'type' => 'checkboxes-bordered',
				'tab_machine_name' => 'basic-facts',
				'disable_on_frontend' => true,
				'compare_support' => false,
				'attributes' => array(
					'class' => 'js-es-assign-entity-type',
				),
				'default_value' => '',
				'options' => array(
					'agent' => __( 'Agent', 'es' ),
					'agency' => __( 'Agency', 'es' ),
					'' => __( 'None', 'es' ),
				),
				'items_attributes' => array(
					'agent' => array(
						'attributes' => array(
							'data-toggle-container' => '.es-assign--agent-list'
						)
					),
					'agency' => array(
						'attributes' => array(
							'data-toggle-container' => '.es-assign--agency-list'
						)
					),
				),
			),

			'agency_id' => array(
				'label' => __( 'Agency', 'es' ),
				'type' => 'select',
				'tab_machine_name' => 'basic-facts',
				'disable_on_frontend' => ! current_user_can( 'administrator' ),
				'options_callback' => 'es_get_agencies_list',
				'before' => '<div class="es-assign--agency-list js-es-assign">',
				'after' => ' </div>',
				'attributes' => array(
					'class' => 'js-es-agency',
					'data-placeholder' => __( 'Choose agency', 'es' ),
				),
				'search_support' => true,
				'search_settings' => array(
					'values_callback' => array(
						'callback' => 'es_get_agencies_list',
					),
					'attributes' => array(
						'data-placeholder' => __( 'Choose agency', 'es' ),
					),
				),
			),

			'agent_id' => array(
				'label' => __( 'Agent', 'es' ),
				'tab_machine_name' => 'basic-facts',
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				'type' => 'entities-list',
				'is_single_meta' => false,
				'disable_on_frontend' => ! current_user_can( 'administrator' ),
                'before' => ' <div class="es-assign--agent-list js-es-assign">
 								<input type="hidden" name="es_property[agent_id]" value=""/>',
                'after' => ' </div>',
				'options_callback' => 'es_get_agents_list',
				'ajax-item-action' => 'es_get_agent_list_item',
				'items_callback' => 'es_get_the_agents_config',
				'search_support' => true,
				'attributes' => array(
					'data-placeholder' => __( 'Start type agent name', 'es' ),
					'data-noResults' => __( 'Agent not found', 'es' ),
				),
				'search_settings' => array(
					'before' => '',
					'after' => '',
					'type' => 'select',
					'values_callback' => array(
						'callback' => 'es_get_agents_list',
					),
					'attributes' => array(
						'data-placeholder' => __( 'Choose agent', 'es' ),
					),
				),
			),

            'ID' => array(
                'system' => true,
                'label' => __( 'ID', 'es' ),
            ),
			'post_title' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'enable_counter' => true,
				'compare_support' => false,
				'attributes' => array(
					'maxlength' => 100
				),
				'system' => true,
				'type' => 'text',
				'caption' => __( 'Attract potential clients with a listing title that highlights what makes your property special.', 'es' ),
				'label' => __( 'Property title', 'es' ),
				'frontend_tab_machine_name' => 'description',
				'order' => -10
			),
			'post_status' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'label' => __( 'Post status', 'es' ),
				'compare_support' => false,
				'system' => true,
				'type' => 'select',
				'name' => __( 'Status', 'es' ),
				'search_settings' => array(
					'type' => 'select',
					'values_callback' => array(
						'callback' => 'get_post_statuses',
					),
					'attributes' => array(
						'data-placeholder' => __( 'All statuses', 'es' ),
					),
				),
				'search_support' => false,
			),
			'post_excerpt' => array(
				'system' => true,
				'label' => __( 'Short description', 'es' ),
				'compare_support' => true,
			),
			'post_content' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
					'disable_tab_field' => true,
				),
				'compare_support' => false,
                'system' => true,
				'use_media' => false,
				'type' => 'editor',
				'caption' => __( 'Write a quick summary of your property. You can highlight what’s special about your home, the 
neighborhood, etc.', 'es' ),
				'label' => __( 'Description', 'es' ),
				'section_machine_name' => 'description',
				'show_more_label' => __( 'Show all description', 'es' ),
				'frontend_tab_machine_name' => 'description',
				'enable_counter' => true,
				'attributes' => array(
					'maxlength' => 500,
					'rows' => 5,
				),
				'order' => -8
			),
			'es_label' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'checkboxes',
				'compare_support' => false,
                'search_support' => true,
				'tab_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'description',
				'label' => __( 'Labels', 'es' ),
				'taxonomy' => true,
				'order' => is_admin() ? 160 : -6,
				'search_settings' => array(
					'type' => 'checkboxes-bordered',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_label', true ),
					),
					'attributes' => array(
						'multiple' => true,
					),
				),
			),
			'price' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'price',
                'search_support' => true,
				'formatter' => 'price',
				'label' => __( 'Price', 'es' ),
				'is_visible' => 0,
				'frontend_tab_machine_name' => 'description',
				'tab_machine_name' => 'basic-facts',
                'attributes' => array(
                    'placeholder' => ests_label( 'currency_sign' ) ? ests_label( 'currency_sign' ) : ests( 'currency' ),
                ),
				'before' => is_admin() ? "<h3>" . __( 'Set property price', 'es' ) . "</h3>" : '',
				'order' => is_admin() ? 1 : 10,
			),
			'price_per_sqft' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'price',
				'formatter' => 'price-area',
				'area_unit' => 'sq_ft',
				'label' => __( 'Price per sqft', 'es' ),
                'section_machine_name' => 'basic-facts',
				'tab_machine_name' => 'basic-facts',
				'frontend_tab_machine_name' => 'description',
                'attributes' => array(
	                'placeholder' => ests_label( 'currency_sign' ) ? ests_label( 'currency_sign' ) : ests( 'currency' ),
                ),
				'order' => is_admin() ? 2 : 12,
			),
			'price_note' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'type' => 'text',
				'is_visible' => 0,
				'label' => __( 'Price note', 'es' ),
				'frontend_tab_machine_name' => 'description',
				'tab_machine_name' => 'basic-facts',
				'attributes' => array(
					'placeholder' => __( 'E.g. Start From', 'es' ),
				),
				'order' => is_admin() ? 3 : 16,
			),
			'call_for_price' => array(
				'fb_settings' => array(
					'disable_name_edit' => true,
					'disable_type_edit' => true,
				),
				'compare_support' => false,
				'admin_type' => 'switcher',
				'type' => 'checkbox',
				'label' => __( 'Call for price', 'es' ),
				'frontend_tab_machine_name' => 'description',
				'tab_machine_name' => 'basic-facts',
				'default_value' => 0,
				'search_support' => 0,
				'is_visible' => 0,
				'order' => is_admin() ? 4 : 14,
				'after' => '<div class="es-field-break"></div>'
			),
			'is_manual_address' => array(
				'label' => __( 'Is manual address', 'es' ),
				'type' => 'hidden',
				'search_support' => 0,
				'is_visible' => 0,
				'compare_support' => false,
			),
			'is_address_disabled' => array(
				'label' => __( 'Hide address from clients', 'es' ),
				'admin_type' => 'switcher',
				'type' => 'checkbox',
				'is_address_field' => true,
				'tab_machine_name' => 'location',
				'is_tab_static_field' => true,
				'compare_support' => false,
			),
			'address' => array(
				'fb_settings' => array(),
				'label' => __( 'Address', 'es' ),
				'type' => 'text',
                'search_support' => true,
				'tab_machine_name' => 'location',
				'is_address_field' => true,
				'attributes' => array(
					'placeholder' => ests( 'address_search_placeholder' ),
                    'class' => 'js-es-autocomplete-address',
				),
                'search_settings' => array(
                    'attributes' => array(
                        'class' => 'js-es-address',
                    )
                ),
				'is_tab_static_field' => true,
			),
			'country' => array(
				'fb_settings' => array(
					'disable_type_edit' => true
				),
				'type' => 'select',
				'label' => __( 'Country / Region', 'es' ),
                'search_support' => true,
				'tab_machine_name' => 'location',
				'section_machine_name' => 'location',
				'is_visible' => false,
                'address_component' => 'country',
				'formatter' => 'country',
				'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-select2-locations js-es-location',
                    'data-placeholder' => __( 'Choose country', 'es' ),
                    'data-initialize' => 1,
                    'data-address-components' => es_esc_json_attr( array( 'country' ) ),
                    'data-dependency-fields' => es_esc_json_attr( array( 'state', 'province' ) ),
                ),
                'search_settings' => array(
                    'wrapper_class' => 'js-es-field__country',
                    'attributes' => array(
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Choose country', 'es' ),
                        'data-address-components' => es_esc_json_attr( array( 'country' ) ),
                        'data-dependency-fields' => es_esc_json_attr( array( 'state' ) ),
                        'data-value' => es_clean( filter_input( INPUT_GET, 'country' ) ),
                    )
                ),
				'is_tab_static_field' => true,
			),
			'state' => array(
				'fb_settings' => array(
					'disable_type_edit' => true
				),
				'type' => 'select',
				'label' => __( 'State', 'es' ),
                'search_support' => true,
				'tab_machine_name' => 'location',
				'section_machine_name' => 'location',
				'is_visible' => false,
                'address_component' => 'administrative_area_level_1',
                'formatter' => 'state',
				'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-select2-locations js-es-location',
                    'data-placeholder' => __( 'Choose state', 'es' ),
                    'data-initialize' => 2,
                    'data-address-components' => es_esc_json_attr( array( 'administrative_area_level_1' ) ),
                    'data-dependency-fields' =>  es_esc_json_attr( array( 'city', 'province' ) ),
                ),
                'search_settings' => array(
                    'wrapper_class' => 'js-es-field__state',
                    'attributes' => array(
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Choose state', 'es' ),
                        'data-address-components' => es_esc_json_attr( array( 'administrative_area_level_1' ) ),
                        'data-dependency-fields' =>  es_esc_json_attr( array( 'city', 'province' ) ),
                        'data-value' => es_clean( filter_input( INPUT_GET, 'state' ) ),
                    )
                ),
				'is_tab_static_field' => true,
			),
			'province' => array(
				'fb_settings' => array(
					'disable_type_edit' => true
				),
				'type' => 'select',
				'label' => __( 'Province', 'es' ),
				'search_support' => true,
				'tab_machine_name' => 'location',
				'section_machine_name' => 'location',
				'is_visible' => false,
                'formatter' => 'province',
				'address_component' => 'administrative_area_level_2',
				'is_address_field' => true,
				'attributes' => array(
					'class' => 'js-es-select2-locations js-es-location',
					'data-placeholder' => __( 'Choose province', 'es' ),
					'data-initialize' => 3,
					'data-address-components' => es_esc_json_attr( array( 'administrative_area_level_2' ) ),
					'data-dependency-fields' =>  es_esc_json_attr( array( 'city' ) ),
				),
                'search_settings' => array(
                    'wrapper_class' => 'js-es-field__province',
                    'attributes' => array(
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Choose province', 'es' ),
                        'data-address-components' => es_esc_json_attr( array( 'administrative_area_level_2' ) ),
                        'data-dependency-fields' =>  es_esc_json_attr( array( 'city' ) ),
                        'data-value' => es_clean( filter_input( INPUT_GET, 'province' ) ),
                    )
                ),
				'is_tab_static_field' => true,
			),
			'city' => array(
				'fb_settings' => array(
					'disable_type_edit' => true
				),
				'type' => 'select',
				'label' => __( 'City / Department', 'es' ),
                'search_support' => true,
                'formatter' => 'city',
				'tab_machine_name' => 'location',
				'section_machine_name' => 'location',
				'is_visible' => false,
                'address_component' => 'locality',
                'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-select2-locations js-es-location',
                    'data-placeholder' => __( 'Choose city', 'es' ),
                    'data-address-components' => es_esc_json_attr( array( 'locality' ) ),
                    'data-initialize' => 4,
                ),
                'search_settings' => array(
					'multiple' => true,
                    'wrapper_class' => 'js-es-field__city',
                    'attributes' => array(
	                    'multiple' => 'multiple',
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Choose city', 'es' ),
                        'data-address-components' => es_esc_json_attr( array( 'locality' ) ),
                        'data-value' => es_clean( filter_input( INPUT_GET, 'city' ) ),
                    ),
                ),
				'is_tab_static_field' => true,
			),
			'postal_code' => array(
				'fb_settings' => array(
					'disable_type_edit' => true
				),
				'type' => 'text',
				'label' => __( 'Zip / Postal Code', 'es' ),
                'search_support' => true,
				'tab_machine_name' => 'location',
				'section_machine_name' => 'location',
				'is_visible' => false,
				'address_component' => 'postal_code',
				'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-location',
                    'data-address-components' => es_esc_json_attr( array( 'postal_code' ) ),
                ),
				'is_tab_static_field' => true,
			),
			'latitude' => array(
				'fb_settings' => array(),
				'type' => 'text',
				'label' => __( 'Latitude', 'es' ),
				'tab_machine_name' => 'location',
				'search_support' => 0,
				'is_visible' => 0,
				'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-latitude',
	                'placeholder' => __( 'Ex. 63.524073', 'es' ),
                ),
				'is_tab_static_field' => true,
				'compare_support' => false,
			),
			'longitude' => array(
				'fb_settings' => array(),
				'type' => 'text',
				'label' => __( 'Longitude', 'es' ),
				'tab_machine_name' => 'location',
				'search_support' => 0,
				'is_visible' => 0,
				'is_address_field' => true,
                'attributes' => array(
                    'class' => 'js-es-longitude',
                    'placeholder' => __( 'Ex. -62.149905', 'es' ),
                ),
				'is_tab_static_field' => true,
				'compare_support' => false,
			),
			'address_components' => array(
                'type' => 'hidden',
                'label' => __( 'Address components', 'es' ),
                'search_support' => 0,
                'is_visible' => 0,
                'attributes' => array(
                    'class' => 'js-es-address-components'
                ),
                'compare_support' => false,
            ),
			'es_neighborhood' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
                'search_settings' => array(
                    'type' => 'checkboxes',
                    'values_callback' => array(
                        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_neighborhood', true ),
                    ),
                ),
                'type' => 'checkboxes',
                'taxonomy' => true,
                'label' => __( 'Neighborhoods', 'es' ),
                'search_support' => true,
                'section_machine_name' => 'location',
				'frontend_tab_machine_name' => 'location',
				'is_pdf_visible' => true,
			),
			'gallery' => array(
				'fb_settings' => array(),
				'label' => __( 'Photo gallery', 'es' ),
				'type' => 'media',
                'description' => sprintf( __( 'One file must be less than %s. Allowed Extensions: %s.', 'es' ),
                    esc_html( size_format( $max_upload_size ) ), 'jpg, jpeg, png, gif' ),
				'admin_type' => 'images',
				'uploader_type' => 'images',
				'tab_machine_name' => 'media',
				'button_label' => __( 'Upload photos', 'es' ),
				'is_visible' => 0,
				'attributes' => array(
					'multiple' => true,
				),
                'enable_hidden_input' => true,
				'compare_support' => false,
			),

			'documents' => array(
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				'label' => __( 'Documents', 'es' ),
				'type' => 'media',
                'description' => sprintf( __( 'One file must be less than %s. Allowed Extensions: %s.', 'es' ),
                    esc_html( size_format( $max_upload_size ) ), 'pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx' ),
				'formatter' => 'document',
				'uploader_type' => 'files',
				'tab_machine_name' => 'media',
				'section_machine_name' => 'documents',
				'button_label' => __( 'Upload files', 'es' ),
				'attributes' => array(
					'multiple' => true,
				),
                'enable_hidden_input' => true,
				'compare_support' => false,
			),
			'floor_plans' => array(
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				'label' => __( 'Floor plans', 'es' ),
				'type' => 'media',
                'description' => sprintf( __( 'One file must be less than %s. Allowed Extensions: %s.', 'es' ),
                    esc_html( size_format( $max_upload_size ) ), 'jpg, jpeg, png, gif' ),
                'formatter' => 'image',
				'admin_type' => 'images',
				'uploader_type' => 'images',
				'tab_machine_name' => 'media',
                'section_machine_name' => 'floors_plans',
				'button_label' => __( 'Upload images', 'es' ),
				'attributes' => array(
					'multiple' => true,
				),
                'enable_hidden_input' => true,
				'compare_support' => false,
				'is_pdf_visible' => true,
			),
			'video' => array(
				'label' => __( 'Video', 'es' ),
				'fb_settings' => array(
					'disable_type_edit' => true,
				),
				'type' => 'text',
				'tab_machine_name' => 'media',
				'section_machine_name' => 'video',
				'formatter' => 'video',
				'attributes' => array(
					'placeholder' => __( 'Link to your video from YouTube or Vimeo', 'es' ),
				),
				'compare_support' => false,
			),
			'es_amenity' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Amenities', 'es' ),
                'taxonomy' => true,
                'search_settings' => array(
                    'type' => 'checkboxes',
                    'visible_items' => ests( 'is_amenities_collapse_enabled' ) ? 6 : false,
                    'show_more_label' => __( 'Show all amenities', 'es' ) . '<span class="es-icon es-icon_chevron-bottom"></span>',
                    'values_callback' => array(
                        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_amenity', true ),
                    ),
                ),
                'search_support' => true,
				'type' => 'checkboxes',
				'section_machine_name' => 'features',
                'frontend_tab_machine_name' => 'features',
				'is_pdf_visible' => true,
			),
			'es_feature' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Features', 'es' ),
                'taxonomy' => true,
                'search_settings' => array(
                    'type' => 'checkboxes',
                    'visible_items' => ests( 'is_features_collapse_enabled' ) ? 6 : false,
                    'show_more_label' => __( 'Show all features', 'es' ) . '<span class="es-icon es-icon_chevron-bottom"></span>',
                    'values_callback' => array(
                        'callback' => 'es_get_terms_list',
                        'args' => array( 'es_feature', true ),
                    ),
                ),
                'search_support' => true,
				'type' => 'checkboxes',
				'section_machine_name' => 'features',
                'frontend_tab_machine_name' => 'features',
				'is_pdf_visible' => true,
			),
			'es_floor_covering' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Floor covering', 'es' ),
                'search_support' => true,
				'type' => 'checkboxes',
				'taxonomy' => true,
				'section_machine_name' => 'building-details',
				'frontend_tab_machine_name' => 'building-details',
				'is_pdf_visible' => true,
				'search_settings' => array(
					'type' => 'checkboxes',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_floor_covering', true ),
					),
				),
			),
			'es_basement' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Basement', 'es' ),
                'search_support' => true,
				'type' => 'radio',
				'taxonomy' => true,
				'section_machine_name' => 'building-details',
				'is_pdf_visible' => true,
				'frontend_tab_machine_name' => 'building-details',
				'search_settings' => array(
					'type' => 'checkboxes',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_basement', true ),
					),
				),
			),
			'es_exterior_material' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Exterior material', 'es' ),
                'search_support' => true,
				'taxonomy' => true,
				'type' => 'checkboxes',
				'section_machine_name' => 'building-details',
				'is_pdf_visible' => true,
				'frontend_tab_machine_name' => 'building-details',
				'search_settings' => array(
					'type' => 'checkboxes',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_exterior_material', true ),
					),
				),
			),
			'es_roof' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Roof', 'es' ),
                'search_support' => true,
				'taxonomy' => true,
				'type' => 'checkboxes',
				'section_machine_name' => 'building-details',
				'is_pdf_visible' => true,
				'frontend_tab_machine_name' => 'building-details',
				'search_settings' => array(
					'type' => 'checkboxes',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_roof', true ),
					),
				),
			),
			'es_parking' => array(
				'fb_settings' => array(
					'disable_tab_field' => true,
				),
				'label' => __( 'Parking', 'es' ),
                'search_support' => true,
				'taxonomy' => true,
				'type' => 'checkboxes',
				'section_machine_name' => 'building-details',
				'is_pdf_visible' => true,
				'frontend_tab_machine_name' => 'building-details',
				'search_settings' => array(
					'type' => 'checkboxes',
					'values_callback' => array(
						'callback' => 'es_get_terms_list',
						'args' => array( 'es_parking', true ),
					),
				),
			),
            'keywords' => array(
                'label' => __( 'Keywords', 'es' ),
                'search_support' => true,
                'type' => 'select',
                'multiple' => true,
                'search_settings' => array(
                    'attributes' => array(
                        'type' => 'select',
                        'data-placeholder' => __( 'E.g. great view...', 'es' ),
                        'multiple' => 'multiple',
                    )
                ),
                'compare_support' => false,
            ),
		);

		if ( ! ests( 'is_agencies_enabled' ) ) {
			unset( $fields['agency_id'] );
			unset( $fields['assign_entity_type']['options']['agency'] );
		}

		if ( ! ests( 'is_agents_enabled' ) ) {
			unset( $fields['agent_id'] );
			unset( $fields['assign_entity_type']['options']['agent'] );
		}

		if ( ! ests( 'is_agents_enabled' ) && ! ests( 'is_agencies_enabled' ) ) {
			unset( $fields['assign_entity_type'] );
		}

		foreach ( array( 'bedrooms', 'bathrooms', 'area', 'lot_size', 'half_baths', 'floor', 'floor_level' ) as $field ) {
		    if ( ests( "search_{$field}_list" ) ) {
		        $values = explode( ',', ests( "search_{$field}_list" ) );
                $fields[ $field ]['search_settings']['values'] = array_combine( $values, $values );
            }

		    if ( ests( "is_search_{$field}_range_enabled" ) ) {
                $fields[ $field ]['search_settings']['range'] = true;
                if ( ests( "search_{$field}_list" ) ) {
                    $fields[ $field ]['search_settings']['range_label'] = sprintf( __( 'Or %s range', 'es' ), strtolower( $fields[ $field ]['label'] ) );
                } else {
                    $fields[ $field ]['search_settings']['range_label'] = $fields[ $field ]['label'];
                }

                if ( ests( "search_min_{$field}_list" ) ) {
                    $values = explode( ',', ests( "search_min_{$field}_list" ) );
                    $fields[ $field ]['search_settings']['values_min'] = array_combine( $values, $values );
                }

                if ( ests( "search_max_{$field}_list" ) ) {
                    $values = explode( ',', ests( "search_max_{$field}_list" ) );
                    $fields[ $field ]['search_settings']['values_max'] = array_combine( $values, $values );
                }
            }
        }

		$order = 10;

		foreach ( $fields as $field => $config ) {
			$fields[ $field ]['mls_import_support'] = 1;

			if ( ! isset( $fields[ $field ]['order'] ) ) {
				$fields[ $field ]['order'] = $order;
			}

			if ( ! isset( $fields[ $field ]['compare_support'] ) ) {
				$fields[ $field ]['compare_support'] = 1;
			}

			if ( ! isset( $config['is_visible'] ) ) {
				$fields[ $field ]['is_visible'] = 1;
				$fields[ $field ]['is_visible_for'] = array( 'all_users' );
			}

			$order += 10;
		}

		return apply_filters( 'es_property_default_fields', $fields );
	}

    /**
     * @param string $field
     * @param mixed $value
     */
	public function save_field_value( $field, $value ) {

	    $field_info = static::get_field_info( $field );

		if ( $field == 'assign_entity_type' && ! empty( $value ) ) {
			if ( ! in_array( 'agent', $value ) ) {
				$this->delete_field_value( 'agent_id' );
			}

			if ( ! in_array( 'agency', $value ) ) {
				$this->delete_field_value( 'agency_id' );
			}
		}

		if ( $field == 'agent_id' ) {
			$assign = $this->assign_entity_type ? $this->assign_entity_type : array();

			if ( ! in_array( 'agent', $assign ) ) {
				$this->delete_field_value( $field );
			} else {
				parent::save_field_value( $field, $value );
			}

			return true;
		}

		if ( $field == 'agency_id' ) {
			$assign = $this->assign_entity_type ? $this->assign_entity_type : array();

			if ( ! in_array( 'agency', $assign ) ) {
				$this->delete_field_value( $field );
			} else {
				parent::save_field_value( $field, $value );
			}

			return true;
		}

	    if ( ! empty( $field_info['type'] ) && 'media' == $field_info['type'] ) {
	        $media = $this->{$field};

	        if ( ! empty( $media ) ) {
	            foreach ( $media as $attachment_id ) {
                    wp_update_post( array(
                        'ID' => $attachment_id,
                        'post_parent' => 0,
                    ) );
                }
            }

	        if ( ! empty( $value ) ) {
				$value = is_string( $value ) ? array( $value ) : $value;
	            $value = array_filter( $value );
				$order = 0;

	            foreach ( $value as $key => $attachment_id ) {
	                if ( filter_var( $attachment_id , FILTER_VALIDATE_URL ) !== false ) {
	                    $attachment_id = es_upload_file_by_url( $attachment_id, $this->get_id() );
                    }

	                if ( ! is_wp_error( $attachment_id ) ) {
	                    $value[ $key ] = $attachment_id;

                        wp_update_post( array(
                            'ID' => $attachment_id,
                            'post_parent' => $this->get_id()
                        ) );

                        update_post_meta( $attachment_id, 'es_attachment_order', $order++ );
                        update_post_meta( $attachment_id, 'es_attachment_type', $field );
                    }
                }
            }

	        if ( 'gallery' == $field ) {
                $featured_image_id = reset( $value );
                if ( $value ) {
                    set_post_thumbnail( $this->get_id(), $featured_image_id );
                } else {
                    delete_post_thumbnail( $this->get_id() );
                }
            }

	        return;
        }

	    if ( 'address_components' == $field && $value ) {
            $repo = es_get_address_components_container();
            $components = is_array( $value ) ? $value : json_decode( stripslashes( $value ) );
            $repo::save_property_components( $components, $this->get_id() );
        }

        parent::save_field_value( $field, $value );
    }

    /**
     * @param $field
     *
     * @return mixed|void
     */
    public function get_field_value( $field ) {
        $field_info = static::get_field_info( $field );
        $entity = static::get_entity_name();

        if ( $field == 'date_added' ) {
            $date = get_the_time( 'U' );
            return apply_filters( "es_{$entity}_get_field_value", $date, $field, $this );
        }

        if ( ! empty( $field_info['address_component'] ) ) {
            $terms = get_terms( array(
                'object_ids' => $this->get_id(),
                'taxonomy' => 'es_location',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'type',
                        'value' => $field_info['address_component'],
                    )
                )
            ) );

            if ( $terms && ! is_wp_error( $terms ) ) {
                $terms = max($terms);
                return apply_filters( "es_{$entity}_get_field_value", $terms, $field, $this );
            }
        }

        if ( ! empty( $field_info['type'] ) && $field_info['type'] == 'media' ) {
            $value = get_posts( array(
                'fields' => 'ids',
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $this->get_id(),
                'meta_key' => 'es_attachment_order',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'es_attachment_type',
                        'value' => $field,
                    )
                ),
            ) );

            return apply_filters( "es_{$entity}_get_field_value", $value, $field, $this );
        }

        return parent::get_field_value( $field );
    }

	/**
	 * @return array|mixed|stdClass[]
	 */
    public static function get_fields() {
	    $fields_builder = es_get_fields_builder_instance();
	    return $fields_builder::get_items( 'property' );
    }

	/**
     * Save meta for entity.
     *
     * @param $data
     */
    public function save_fields( $data ) {
	    $address_components_fields = array( 'city', 'province', 'state', 'country' );

        if ( empty( $data['address_components'] ) ) {
            $components = array();
            foreach ( $address_components_fields as $field ) {
                $field_info = static::get_field_info( $field );
                if ( ! empty( $field_info['address_component'] ) && ! empty( $data[ $field ] ) ) {
                    $component = new stdClass();
                    $component->types = array( $field_info['address_component'] );

                    if ( is_numeric( $data[ $field ] ) ) {
                        $component->term_id = $data[ $field ];
                    } else {
                        $component->long_name = $data[ $field ];
                    }

                    $components[] = $component;
                }
            }
            $data['address_components'] = json_encode( $components, JSON_UNESCAPED_UNICODE );
        } else {
			$address_components = json_decode( stripslashes( $data['address_components'] ) );
			$property = es_get_property( $this->get_id() );

			foreach ( $address_components_fields as $location_field ) {
				if ( ! empty( $data[ $location_field ] ) && $data[ $location_field ] != $property->{$location_field} ) {
					$field = static::get_field_info( $location_field );

					if ( is_numeric( $data[ $location_field ] ) && term_exists( (int) $data[ $location_field ], 'es_location' ) ) {
						$term = get_term_by( 'term_id', $data[ $location_field ], 'es_location' );
						$data[ $location_field ] = $term->name;
					}

					$type_isset = false;

					foreach ( $address_components as $key => $component ) {
						if ( ! empty( $field['address_component'] ) && ! empty( $component->types ) && in_array( $field['address_component'], $component->types ) ) {
							$address_components[ $key ]->long_name = $data[ $location_field ];
							$address_components[ $key ]->short_name = $data[ $location_field ];
							$type_isset = true;
						}
					}

					if ( ! $type_isset ) {
						$address_components[] = (object) array(
							'long_name' => $data[ $location_field ],
							'short_name' => $data[ $location_field ],
							'type' => array( $field['address_component'] )
						);
					}
				}
			}

			$data['address_components'] = json_encode( $address_components, JSON_UNESCAPED_UNICODE );
        }

        parent::save_fields( $data );
    }

	/**
	 * Check property featured label.
	 *
	 * @return bool
	 */
    public function is_featured() {
    	$featured_id = es_get_featured_term_id();

    	return $featured_id ? has_term( (int) $featured_id, 'es_label', $this->get_id() ) : false;
    }

    /**
	 * @return bool
	 */
	public function is_open_house() {
		return (bool) $this->is_open_house;
	}

	/**
     * @return mixed|string
     */
    public static function get_post_type_name() {
        return 'properties';
    }
}
