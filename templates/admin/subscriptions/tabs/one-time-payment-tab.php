<div class="es-fields-wrap">
    <form method="post" class="js-es-settings-form">
        <?php es_settings_field_render( 'is_otp_enabled', array(
            'type' => 'switcher',
            'label' => __( 'Enable one time payment', 'es' ),
            'attributes' => array(
                'data-toggle-container' => '#es-otp-fields',
            ),
        ) ); ?>

        <div id="es-otp-fields">
            <div class="es-field-row">
                <?php es_settings_field_render( 'otp_basic_price', array(
                    'label' => __( 'Basic listing price', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'placeholder' => ests_label( 'currency_sign' ),
                        'min' => 0,
                        'step' => 'any'
                    ),
                ) ); ?>

                <?php es_settings_field_render( 'otp_basic_min_count', array(
                    'label' => __( 'Min default count', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'min' => 1,
                        'step' => 1
                    ),
                ) ); ?>
            </div>

            <div class="es-field-row">
                <?php es_settings_field_render( 'otp_featured_price', array(
                    'label' => __( 'Featured listing price', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'placeholder' => ests_label( 'currency_sign' ),
                        'min' => 0,
                        'step' => 'any'
                    ),
                ) ); ?>

                <?php es_settings_field_render( 'otp_featured_min_count', array(
                    'label' => __( 'Min default count', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'min' => 0
                    ),
                ) ); ?>
            </div>

            <?php es_settings_field_render( 'otp_is_basic_bonus_enabled', array(
                'type' => 'switcher',
                'label' => __( 'Insert bonus for basic listings', 'es' ),
                'attributes' => array(
                    'data-toggle-container' => '#es-free-listings',
                ),
            ) ); ?>

            <div class="es-field-row" id="es-free-listings">
                <?php es_settings_field_render( 'otp_free_featured_count', array(
                    'label' => __( 'FREE featured listings', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'placeholder' => ests_label( 'currency_sign' ),
                        'step' => 1,
                        'min' => 1,
                    ),
                ) ); ?>

                <?php es_settings_field_render( 'otp_free_basic_count', array(
                    'label' => __( 'per how many basic listings', 'es' ),
                    'type' => 'number',
                    'attributes' => array(
                        'min' => 0,
                        'step' => 1,
                    ),
                ) ); ?>
            </div>
        </div><?php

        wp_nonce_field( 'es_save_settings' ); ?>

        <input type="hidden" name="action" value="es_save_settings"/>
        <input type="submit" style="margin-top: 24px;" class="es-btn es-btn--primary es-btn--large es-btn--save js-es-save-settings" value="<?php _e( 'Save changes', 'es' ); ?>"/>
    </form>
</div>
