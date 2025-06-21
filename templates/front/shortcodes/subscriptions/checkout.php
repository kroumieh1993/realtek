<?php
$plan = es_get_subscription_plan( es_get( 'plan' ) );
$price_period = es_get( 'period' );
$suff = es_price_suff( $price_period );
$amount = es_format_value( $plan->{$price_period . '_price'}, 'price', array( 'suff' => ' / ' . $suff ) );

if ( ! $plan->get_wp_entity() )
    wp_die( __( 'Plan doesn\'t exist.', 'es' ) );

if ( ! current_user_can( 'agent' ) || ! get_current_user_id() )
    wp_die( __( 'You are not authorized for access this page', 'es' ) ); ?>

<div class="es-checkout content-font">
    <h2 class="heading-font"><?php _e( 'Payment details', 'es' ); ?></h2>

    <p>
        <b><?php echo __( $plan->name, 'es' ); ?></b>
        <?php if ( ! $plan->is_free_plan_enabled ) : ?>
            <b><?php echo $amount; ?></b>
        <?php endif; ?>
    </p>
    <?php if ( $plan->is_basic_listings_limited ) : ?>
        <p class="es-checkout__info"><?php printf( _n( '%s basic listing', '%s basic listings', $plan->basic_listings_limit ), $plan->basic_listings_limit ); ?></p>
    <?php else : ?>
        <p class="es-checkout__info"><?php _e( 'Unlimited basic listings', 'es' ); ?></p>
    <?php endif; ?>

    <?php if ( $plan->is_featured_listings_limited ) : ?>
        <p class="es-checkout__info"><?php printf( _n( '%s featured listing', '%s featured listings', $plan->featured_listings_limit ), $plan->featured_listings_limit ); ?></p>
    <?php else : ?>
        <p class="es-checkout__info"><?php _e( 'Unlimited featured listings', 'es' ); ?></p>
    <?php endif; ?>

    <form method="post">
        <?php
//        if ( ! $plan->is_free_plan_enabled ) :
//            es_framework_field_render( 'is_automatic_payment', array(
//                'label' => __( 'Automatic payment every month', 'es' ),
//                'type' => 'switcher',
//                'value' => 1,
//                'attributes' => array(
//                    'value' => 1,
//                ),
//            ) );
//        endif; ?>

        <div class="es-checkout__bottom">
	        <?php if ( ! $plan->is_free_plan_enabled ) : ?>
                <b class="es-total heading-font"><?php _ex( 'Total:', 'subscriptions total label', 'es' ); ?> <span><?php echo $amount; ?></span></b>
            <?php endif; ?>

	        <?php if ( get_current_user_id() ) : ?>
		        <?php if ( ! $plan->is_free_plan_enabled ) : ?>
                    <button type="submit" class="es-btn es-btn--primary es-btn--buy">
				        <?php _e( 'Pay now', 'es' ); ?>
                    </button>
		        <?php else : ?>
                    <button type="submit" class="es-btn es-btn--primary es-btn--buy">
				        <?php _e( 'Activate', 'es' ); ?>
                    </button>
		        <?php endif; ?>
            <?php else : ?>
                <?php if ( $url = es_get_page_url( 'login' ) ) : ?>
                    <a href="<?php echo add_query_arg( 'redirect_url', rawurlencode( es_get_current_url() ), $url ); ?>" class="es-btn es-btn--primary es-btn--buy"><?php _e( 'Buy now', 'es' ); ?></a>
                <?php else : ?>
                    <a href="#es-authentication-popup" class="es-btn es-btn--primary es-btn--buy js-es-popup-link">
				        <?php _e( 'Pay now', 'es' ); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <input type="hidden" name="plan_id" value="<?php esc_attr_e( $plan->get_id() ); ?>"/>
            <input type="hidden" name="period" value="<?php esc_attr_e( $price_period ); ?>"/>
            <input name="payment_type" type="hidden" value="plans"/>
            <input name="payment_method" type="hidden" value="paypal-subscriptions"/>
            <?php wp_nonce_field( 'es_submit_payment', 'es_submit_payment' ); ?>
        </div>
    </form>
</div>
