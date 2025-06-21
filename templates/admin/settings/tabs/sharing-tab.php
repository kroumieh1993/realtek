<h2><?php _e( 'Sharing', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'is_link_sharing_enabled', array(
        'label' => __( 'Enable sharing with link', 'es' ),
        'type' => 'switcher',
    ) );

    es_settings_field_render( 'is_social_sharing_enabled', array(
        'label' => __( 'Enable sharing via social networks', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-sharing-container',
        )
    ) ); ?>

    <div id="es-sharing-container">
        <?php es_settings_field_render( 'social_networks', array(
            'label' => __( 'Select options', 'es' ),
            'type' => 'checkboxes',
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'is_pdf_enabled', array(
        'label' => __( 'Enable sharing with PDF', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
	        'data-toggle-container' => '#es-pdf-container'
        )
    ) ); ?>

    <div id="es-pdf-container">
        <?php es_settings_field_render( 'pdf_flyer_layout', array(
            'label' => __( 'PDF flyer layout', 'es' ),
            'type' => 'radio-bordered',
        ) );

        es_settings_field_render( 'is_pdf_map_enabled', array(
            'label' => __( 'Enable map in PDF', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
	            'data-toggle-container' => '#es-pdf-map-container'
            )
        ) ); ?>

        <div id="es-pdf-map-container">
            <?php es_settings_field_render( 'pdf_logo_attachment_id', array(
	            'label' => __( 'PDF Logo image', 'es' ),
	            'type' => 'images',
	            'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
	            'button_label' => __( 'Upload image', 'es' ),
            ) );

            es_settings_field_render( 'pdf_qr', array(
	            'label' => __( 'Enable QR Code', 'es' ),
	            'type' => 'switcher',
            ) );

            es_settings_field_render( 'pdf_phone', array(
                'label' => __( 'PDF Phone', 'es' ),
                'type' => 'text'
            ) );

            es_settings_field_render( 'pdf_email', array(
                'label' => __( 'PDF Email', 'es' ),
                'type' => 'text'
            ) );

            es_settings_field_render( 'pdf_address', array(
                'label' => __( 'PDF Address', 'es' ),
                'type' => 'text'
            ) );

            es_settings_field_render( 'pdf_map_zoom', array(
                'label' => __( 'Map zoom', 'es' ),
                'type' => 'number',
            ) ); ?>
        </div>
    </div>
</div>
