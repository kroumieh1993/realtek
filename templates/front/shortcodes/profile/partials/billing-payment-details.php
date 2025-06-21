<?php /** @var $subscription Es_User_Subscription */ ?>
<h3 class="es-section__title heading-font"><?php _e( 'Payment Details', 'es' ); ?></h3>
<div class="es-section__content">
    <?php if ( ! $subscription || $subscription && empty( $subscription->get_order()->payment_method ) ) : ?>
        <p><?php printf( __( 'Looks like you don\'t have a payment method. If you %s or purchase %s, you\'ll see your payment method here.', 'es' ), $upgrade_plan_url, $adding_new_home ); ?></p>
    <?php else : $order = $subscription->get_order(); ?>
        <ul class="es-info-list">
            <li>
                <span class="es-info-list__title"><?php _e( 'Payment method', 'es' ); ?>:</span>
                <span class="es-info-list__value">
                    <?php if ( stristr( $order->payment_method, 'paypal' ) ) : ?>
                        <img src="<?php echo es_public_img_path( 'paypal.svg' ); ?>" alt="PayPal logo"/>
                        <?php if ( $order->payer_email ) : ?>
                            <?php echo $order->payer_email; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            </li>
            <?php if ( ! empty( $order->period ) ) : ?>
                <li>
                    <span class="es-info-list__title"><?php _e( 'Payment frequency', 'es' ); ?>:</span>
                    <span class="es-info-list__value">
                        <?php echo $order->get_period_label() ; ?>
                    </span>
                </li>
            <?php endif; ?>
            <?php if ( $order->amount ) : ?>
                <li>
                    <span class="es-info-list__title">
                        <?php if ( $order->is_subscription ) :
                            _e( 'Next transaction amount', 'es' );
                        else :
                            _e( 'Transaction amount', 'es' );
                        endif; ?>:
                    </span>
                    <span class="es-info-list__value">
                        <?php echo es_format_value( $order->amount, 'price' ); ?>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>
