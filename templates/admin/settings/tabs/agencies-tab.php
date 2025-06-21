<h2><?php echo _x( 'Agencies', 'plugin settings', 'es' ); ?></h2>

<?php es_settings_field_render( 'is_agencies_enabled', array(
	'before' => '<div class="es-settings-fields es-settings-fields--agencies es-settings-fields--max-width">',
	'label' => __( 'Enable agencies', 'es' ),
	'type' => 'switcher',
	'attributes' => array(
		'data-toggle-container' => '#es-agency-fields-container'
	),
	'after' => '</div>',
) ); ?>

<div id="es-agency-fields-container">
	<div class="es-settings-fields es-settings-fields--agencies es-settings-fields--max-width">
		<?php es_settings_field_render( 'default_agency_avatar_attachment_id', array(
			'label' => __( 'Default agency profile photo', 'es' ),
			'type' => 'images',
			'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
			'button_label' => __( 'Upload image', 'es' ),
		) ); ?>
		<?php es_settings_field_render( 'agencies_per_page', array(
			'label' => __( 'Agencies number per page', 'es' ),
			'type' => 'number',
		) );

		es_settings_field_render( 'is_agency_rating_enabled', array(
			'label' => __( 'Show agency rating', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'is_agency_comments_enabled', array(
			'label' => __( 'Show comments', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'agency_comments_per_page', array(
			'label' => __( 'Comments number per page', 'es' ),
			'type' => 'number',
		) );

		es_settings_field_render( 'is_agency_commenting_enabled', array(
			'label' => __( 'Enable commenting', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'agency_listings_per_page', array(
			'label' => __( 'Active listings number per page', 'es' ),
			'type' => 'number',
		) );

		es_settings_field_render( 'is_agencies_sorting_enabled', array(
			'label' => __( 'Enable sorting', 'es' ),
			'type' => 'switcher',
			'attributes' => array(
				'data-toggle-container' => '#es-agencies-sorting-container',
			),
		) ); ?>

		<div id="es-agencies-sorting-container" class="es-hidden">
			<?php es_settings_field_render( 'agencies_sorting_options', array(
				'label' => __( 'Sort options', 'es' ),
				'type' => 'checkboxes',
			) );

			es_settings_field_render( 'agencies_default_sorting_option', array(
				'label' => __( 'Default sort options', 'es' ),
				'type' => 'select',
			) ); ?>
		</div>

		<?php es_settings_field_render( 'is_agencies_wishlist_enabled', array(
			'label' => __( 'Enable wishlist', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'is_agencies_sharing_enabled', array(
			'label' => __( 'Enable sharing', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'is_agency_member_since_enabled', array(
			'label' => __( 'Show member since', 'es' ),
			'type' => 'switcher',
		) );

		es_settings_field_render( 'is_agency_contact_form_enabled', array(
			'label' => __( 'Enable Contact form on Agency page', 'es' ),
			'type' => 'switcher',
			'attributes' => array(
				'data-toggle-container' => '#es-agency-contact-btn'
			),
		) );

		es_settings_field_render( 'is_agency_contact_button_enabled', array(
            'before' => "<div id='es-agency-contact-btn'>",
			'label' => __( 'Enable Contact button', 'es' ),
			'type' => 'switcher',
            'after' => '</div>'
		) ); ?>
	</div>
</div>