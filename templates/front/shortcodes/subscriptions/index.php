<div class="es-wrap">
    <?php $redirect_action = es_clean( filter_input( INPUT_GET, 'redirect_action' ) );
    $types = es_subscriptions_get_payment_types();
    $screen = sanitize_text_field( filter_input( INPUT_GET, 'screen' ) );

    if ( $screen === 'checkout' ) :
        include es_locate_template( 'front/shortcodes/subscriptions/checkout.php' );
    else :
        if ( ! empty( $types ) ) : $screen = $screen ? $screen : array_key_first( $types ); ?>
            <div class="es-subscriptions-checkout content-font">
                <?php if ( $redirect_action == 'sign-up' ) : ?>
                    <span class="es-icon es-icon_check-mark es-heading-icon es-secondary-color"></span>
                    <h2 class="heading-font es-heading-title">
                        <?php _e( ests( 'subscription_tr_signup_title' ), 'es' ); ?>
                    </h2>
                    <?php if ( $subtitle = ests( 'subscription_tr_signup_subtitle' ) ) : ?>
                        <p class="es-heading-subtitle"><?php echo $subtitle; ?></p>
                    <?php endif; ?>
                <?php else : ?>
                    <h2 class="heading-font es-heading-title">
                        <?php _e( ests( 'subscription_tr_general_title' ), 'es' ); ?>
                    </h2>
	                <?php if ( $subtitle = ests( 'subscription_tr_general_subtitle' ) ) : ?>
                        <p class="es-heading-subtitle"><?php echo $subtitle; ?></p>
	                <?php endif; ?>
                <?php endif; ?>

                <?php if ( is_array( $types ) && count( $types ) > 1 ) {
	                es_framework_field_render( 'type', array(
		                'type' => 'radio-bordered',
		                'options' => $types,
		                'value' => $screen,
		                'items_wrapper' => "<div class='es-field-row es-field-row--center'>{items}</div>",
		                'attributes' => array(
			                'class' => 'js-es-switch-payment-type'
		                ),
	                ) );
                }

                include es_locate_template( 'front/shortcodes/subscriptions/otp-form.php' );
                include es_locate_template( 'front/shortcodes/subscriptions/plans-form.php' ); ?>
            </div>
        <?php endif;
    endif; ?>
</div>
