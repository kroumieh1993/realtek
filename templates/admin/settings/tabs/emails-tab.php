<h2><?php echo _x( 'Emails', 'plugin settings', 'es' ); ?></h2>

<?php es_settings_field_render( 'email_logo_attachment_id', array(
    'label' => __( 'Email logo image', 'es' ),
    'type' => 'images',
    'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
    'button_label' => __( 'Upload image', 'es' ),
) ); ?>

<div class="es-settings-fields es-settings-fields--emails es-settings-fields--600">
	<?php es_settings_field_render( 'email_from', array(
		'label' => __( 'Sender email', 'es' ),
		'type' => 'email',
	) );

	es_settings_field_render( 'email_from_name', array(
		'label' => __( 'Sender name', 'es' ),
		'type' => 'text',
	) );

	es_settings_field_render( 'is_request_info_email_from_enabled', array(
		'label' => __( 'Enable FROM email header', 'es' ),
		'type' => 'switcher',
        'description' => __( 'For request info forms only.', 'es' ),
	) );

    foreach ( es_get_email_types_list() as $email_id => $email_class ) :
        if ( $email_id == 'saved_search' ) continue;
        /** @var Es_Email $email_instance */
        $email_instance = new $email_class();
        $is_disableable = $email_instance::is_disableable();
        $tokens = $email_instance->get_tokens();
        $description = $tokens ? __( 'Allowed tokens are: ' ) . implode( ', ', array_keys( $tokens ) ) : ''; ?>
        <div class='es-accordion js-es-accordion'>
            <div class='es-accordion__head'>
                <h3>
                    <?php _e( $email_instance::get_label() ); ?>
                    <?php if ( $is_disableable && ! $email_instance::is_active() ) : ?>
                        <div class="es-label es-label--orange" style="margin-left: 1rem;position: relative;top: -1px;"><?php _e( 'Disabled', 'es' ); ?></div>
                    <?php else : ?>
                        <div class="es-label es-label--green" style="margin-left: 1rem;position: relative;top: -1px;"><?php _e( 'Enabled', 'es' ); ?></div>
                    <?php endif; ?>
                </h3>
                <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                    <span class='es-icon es-icon_chevron-bottom'></span>
                </button>
            </div>
            <div class='es-accordion__body'>
	            <?php if ( $is_disableable ) : ?>
		            <?php es_settings_field_render( 'is_' . $email_id . '_enabled', array(
			            'type' => 'switcher',
			            'label' => __( 'Enable email', 'es' ),
		            ) ); ?>
	            <?php endif; ?>

                <?php es_settings_field_render( $email_id . '_email_subject', array(
                    'type' => 'text',
                    'label' => __( 'Subject', 'es' ),
                ) );

                es_settings_field_render( $email_id . '_email_content', array(
	                'type' => 'editor',
	                'label' => __( 'Body', 'es' ),
                    'description' => $description
                ) ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
