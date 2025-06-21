<h3><?php _e( 'Switcher Subscriptions / One-time payment', 'es' ); ?></h3>

<div class="es-fields-wrap">
    <form method="post" class="js-es-settings-form">
        <?php es_settings_field_render( 'subscription_tr_subscriptions_btn', array(
            'label' => _x( 'Subscriptions', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_otp_btn', array(
            'label' => _x( 'One-time payment', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) ); ?>

        <h3><?php _e( 'General text', 'es' ); ?></h3>

        <?php es_settings_field_render( 'subscription_tr_general_title', array(
            'label' => _x( 'Title', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_general_subtitle', array(
            'label' => _x( 'Subtitle', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_featured_listings_description', array(
	        'label' => _x( 'Features listings subtitle', 'es', 'es' ),
	        'type' => 'text'
        ) );

        es_settings_field_render( 'otp_subscription_tr_basic_listings_description', array(
	        'label' => _x( 'One time payment basic listings subtitle', 'es', 'es' ),
	        'type' => 'text'
        ) );

        es_settings_field_render( 'otp_subscription_tr_featured_listings_description', array(
	        'label' => _x( 'One time payment featured listings subtitle', 'es', 'es' ),
	        'type' => 'text'
        ) ); ?>

        <h3><?php _e( 'Text after signing up', 'es' ); ?></h3>

        <?php es_settings_field_render( 'subscription_tr_signup_title', array(
            'label' => _x( 'Title', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_signup_subtitle', array(
            'label' => _x( 'Subtitle', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) ); ?>

        <h3><?php _e( 'Text after hitting listings with Subscriptions / One-time payment options', 'es' ); ?></h3>

        <?php es_settings_field_render( 'subscription_tr_hl_with_subscriptions_title', array(
            'label' => _x( 'Title', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_hl_with_subscriptions_subtitle', array(
            'label' => _x( 'Subtitle', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) ); ?>

        <h3><?php _e( 'Text after hitting listings with One-time payment option', 'es' ); ?></h3>

        <?php es_settings_field_render( 'subscription_tr_hl_with_otp_title', array(
            'label' => _x( 'Title', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_hl_with_otp_subtitle', array(
            'label' => _x( 'Subtitle', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) ); ?>

        <h3><?php _e( 'Text after hitting listings', 'es' ); ?></h3>

        <?php es_settings_field_render( 'subscription_tr_hl_title', array(
            'label' => _x( 'Title', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        es_settings_field_render( 'subscription_tr_hl_subtitle', array(
            'label' => _x( 'Subtitle', 'subscriptions translations texts tab', 'es' ),
            'type' => 'text'
        ) );

        wp_nonce_field( 'es_save_settings' ); ?>

        <input type="hidden" name="action" value="es_save_settings"/>
        <input type="submit" style="margin-top: 24px;" class="es-btn es-btn--primary es-btn--large es-btn--save js-es-save-settings" value="<?php _e( 'Save changes', 'es' ); ?>"/>
    </form>
</div>