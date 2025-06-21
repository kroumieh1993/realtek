<h3><?php _e( 'PayPal Settings', 'es' ); ?></h3>

<div class="es-fields-wrap">
    <form method="post" class="js-es-settings-form">
        <?php es_settings_field_render( 'is_paypal_payment_method_enabled', array(
            'label' => __( 'Enable PayPal payment method', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
                'data-toggle-container' => '#es-paypal-settings'
            )
        ) ); ?>

        <div id="es-paypal-settings">
            <?php es_settings_field_render( 'paypal_mode', array(
                'label' => __( 'PayPal mode', 'es' ),
                'type' => 'select',
                'options' => array(
                    'sandbox' => __( 'Sandbox (test mode)', 'es' ),
                    'live' => __( 'Live', 'es' ),
                )
            ) );

            es_settings_field_render( 'paypal_client_id', array(
                'label' => __( 'Client ID', 'es' ),
                'type' => 'text',
            ) );

            es_settings_field_render( 'paypal_client_secret', array(
                'label' => __( 'Client Secret', 'es' ),
                'type' => 'text',
            ) ); ?>
        </div>

        <?php wp_nonce_field( 'es_save_settings' ); ?>

        <input type="hidden" name="action" value="es_save_settings"/>
        <input type="submit" style="margin-top: 24px;" class="es-btn es-btn--primary es-btn--large es-btn--save js-es-save-settings" value="<?php _e( 'Save changes', 'es' ); ?>"/>
    </form>
</div>