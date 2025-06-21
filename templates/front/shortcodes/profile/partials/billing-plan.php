<?php /** @var $subscription Es_User_Subscription */ ?>
<h3 class="es-section__title heading-font"><?php _e( 'Your plan', 'es' ); ?></h3>
<?php if ( $subscription instanceof Es_User_Subscription ) :
    $order = $subscription->get_order(); ?>
    <div style="display: flex; justify-content: space-between;">
        <div>
            <span class="es-subtitle" style="margin-right: 8px;">
                <?php echo $subscription->get_title(); ?>
            </span>
            <?php if ( ! empty( $subscription->plan['is_free_plan_enabled'] ) ) : ?>
                <span class="es-badge"><?php _e( 'Free', 'es' ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $subscription->plan['is_free_trial_enabled'] ) ) : ?>
                <span class="es-badge"><?php _e( 'Trial', 'es' ); ?></span>
            <?php endif; ?>
        </div>
        <div>
            <?php if ( $order && $order->is_subscription && $order->payment_method ) : ?>
                <?php if ( ! $order->is_cancelled()  ) : ?>
                <a href="<?php echo add_query_arg( '_subscription_nonce', wp_create_nonce( 'es_subscription_cancel' ) ); ?>"
                   class="es-cancel-link es-secondary-color"
                   data-confirm-button="<?php _e( 'Cancel plan', 'es' ); ?>" data-confirm-message="<?php _e( 'Once you cancel it, your active listings will become unpublished when subscription period ends and until you subscribe again.', 'es' ); ?>" data-confirm-title="<?php _e( 'Cancel your Plan?', 'es' ); ?>">
                    <?php _e( 'Cancel plan', 'es' ); ?>
                </a>
                <?php elseif ( $order->is_suspended() ) : ?>
                    <a href="<?php echo add_query_arg( '_subscription_nonce', wp_create_nonce( 'es_subscription_renew' ) ); ?>"
                       class="es-cancel-link es-secondary-color">
			            <?php _e( 'Restore purchase', 'es' ); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( $pricing_url = es_get_page_url( 'pricing' ) ) : ?>
                <a href="<?php echo $pricing_url; ?>" class="es-btn es-btn--secondary es-btn--bordered"><?php _e( 'Upgrade Now', 'es' ); ?></a>
            <?php endif; ?>
        </div>
    </div>

	<ul class="es-info-list">
		<li>
			<span class="es-info-list__title"><?php _e( 'Basic listings', 'es' ); ?>:</span>
			<span class="es-info-list__value">
                <?php if ( $subscription->basic_listings_count ) : ?>
                    <b><?php echo $subscription->published_listings_count - $subscription->published_featured_listings_count; ?></b> /
                    <?php echo $subscription->basic_listings_count < 0 ? __( 'Unlimited', 'es' ) : $subscription->basic_listings_count; ?>
                <?php else : ?>
                    <b>-</b> / -
                <?php endif; ?>
            </span>
		</li>
		<li>
			<span class="es-info-list__title"><?php _e( 'Featured listings:', 'es' ); ?></span>
			<span class="es-info-list__value">
                <?php if ( $subscription->featured_listings_count ) : ?>
                    <b><?php echo $subscription->published_featured_listings_count; ?></b> /
                    <?php echo $subscription->featured_listings_count < 0 ? __( 'Unlimited', 'es' ) : $subscription->featured_listings_count; ?>
                <?php else : ?>
                    <b>-</b> / -
                <?php endif; ?>
            </span>
		</li>
	</ul>
<?php else : ?>
	<p class="es-subtitle"><?php _e( 'No plan yet', 'es' ); ?></p>
	<p><?php _e( 'Pick a plan that best fits your needs.', 'es' ); ?></p>
	<?php if ( $url = es_get_page_url( 'pricing' ) ) : ?>
		<a href="<?php echo $url; ?>" class="es-btn es-btn--secondary es-btn--bordered">
			<?php _e( 'Choose Your Plan', 'es' ); ?>
		</a>
	<?php endif; ?>
<?php endif;
