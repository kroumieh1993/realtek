<?php if ( ! empty( $types['otp'] ) ) : ?>
	<div class="es-otp-container js-es-subscription-container es-hidden">
		<form action="" method="post">
            <input name="payment_type" type="hidden" value="otp"/>
            <input name="payment_method" type="hidden" value="paypal"/>
            <?php wp_nonce_field( 'es_submit_payment', 'es_submit_payment' ); ?>
			<?php $skeleton = "{before}
                        <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                           <label for='{id}'><div class='es-field__label-caption'>{label}{caption}</div><div class='es-field__incrementer-wrap'>{input}</div></label>
                        </div>
                    {after}";

			$free_featured_count = ests( 'otp_free_featured_count' );
			$free_per_basic_count = ests( 'otp_free_basic_count' );

			if ( ests( 'otp_is_basic_bonus_enabled' ) && $free_per_basic_count && $free_featured_count ) {
				es_framework_field_render( 'free_featured_count', array(
					'type' => 'hidden',
					'value' => $free_featured_count,
					'attributes' => array(
						'class' => 'js-es-free-featured-count'
					),
				) );

				es_framework_field_render( 'free_basic_count', array(
					'type' => 'hidden',
					'value' => $free_per_basic_count,
					'attributes' => array(
						'class' => 'js-es-per-basic-count'
					),
				) );
			}

			$basic_price = ests( 'otp_basic_price' );
			$featured_price = ests( 'otp_featured_price' );

			if ( $basic_price ) {
				es_framework_field_render( 'basic_listings_count', array(
					'type' => 'incrementer',
					'label' => __( 'Basic listings', 'es' ),
					'caption' => ests( 'otp_subscription_tr_basic_listings_description' ),
					'attributes' => array(
						'min' => ests( 'otp_basic_min_count' ),
						'class' => 'js-es-basic-listings-count js-es-calculate-total',
						'data-value-container' => '.js-es-basic-total',
						'data-value-default' => '-',
						'data-value-suffix' => ' x ' . es_format_value( $basic_price, 'price' ),
						'data-price' => $basic_price
					),
					'value' => ests( 'otp_basic_min_count' ),
					'skeleton' => $skeleton
				) );
			}

			if ( $featured_price ) :
				es_framework_field_render( 'featured_listings_count', array(
					'type' => 'incrementer',
					'label' => __( 'Featured listings', 'es' ),
					'caption' => ests( 'otp_subscription_tr_featured_listings_description' ),
					'skeleton' => $skeleton,
					'attributes' => array(
						'class' => 'js-es-featured-listings-count js-es-calculate-total',
						'data-value-container' => '.js-es-featured-total',
						'data-value-default' => '-',
						'data-value-suffix' => ' x ' . es_format_value( $featured_price, 'price' ),
						'min' => ests( 'otp_featured_min_count' ),
						'data-price' => $featured_price
					),
					'value' => ests( 'otp_featured_min_count' ),
				) );
			endif; ?>

			<div class="es-total-container">
				<?php if ( $basic_price ) : ?>
					<p class="es-listings-total">
						<span><?php _e( 'Basic listing', 'es' ); ?></span>
						<span class="js-es-basic-total">-</span>
					</p>
				<?php endif; ?>
				<?php if ( $featured_price ) : ?>
					<p class="es-listings-total">
						<span><?php _e( 'Featured listing', 'es' ); ?></span>
						<span class="js-es-featured-total">-</span>
					</p>
				<?php endif; ?>
				<?php if ( $basic_price ) : ?>
					<p class="es-listings-total">
						<span></span>
						<span class="js-es-free-featured-total"></span>
					</p>
				<?php endif; ?>

                <b class="es-total heading-font"><?php _ex( 'Total:', 'subscriptions total label', 'es' ); ?> <span class="js-es-total"></span></b>
			</div>

            <?php if ( get_current_user_id() && current_user_can( 'agent' ) ) : ?>
                <button type="submit" class="es-btn es-btn--primary es-btn--buy"><?php _e( 'Buy now', 'es' ); ?></button>
            <?php else : ?>
                <?php if ( $url = es_get_page_url( 'login' ) ) : ?>
                    <a href="<?php echo add_query_arg( array(
	                    'redirect_url', rawurlencode( es_get_current_url() ),
	                    'auth_item' => 'agent-register-form',
                    ), $url ); ?>" class="es-btn es-btn--primary es-btn--buy"><?php _e( 'Sign up as agent', 'es' ); ?></a>
                <?php else : ?>
                    <a href="#es-authentication-popup" class="es-btn es-btn--primary es-btn--buy js-es-popup-link"><?php _e( 'Sign up as agent', 'es' ); ?></a>
                <?php endif; ?>
            <?php endif; ?>
		</form>
	</div>
<?php endif;
