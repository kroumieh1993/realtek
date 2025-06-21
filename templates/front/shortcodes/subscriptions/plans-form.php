<?php if ( ! empty( $types['plans'] ) ) : ?>
	<div class="es-plans-container js-es-plans-container js-es-subscription-container">
		<?php es_framework_field_render( 'period', array(
			'type' => 'switcher',
			'label' => __( 'Billed monthly', 'es' ),
			'description' => __( 'annually', 'es' ),
            'attributes' => array(
                'class' => 'js-es-switch-plan-period'
            ),
		) ); ?>

		<div class="es-pricing">
            <div class="es-row-names">
                <div class="es-row-name es-row-name--basic">
                    <b><?php _e( 'Basic listings', 'es' ); ?></b>
                </div>
                <div class="es-row-name es-row-name--featured">
                    <b><?php _e( 'Featured listings', 'es' ); ?></b>
                    <?php if ( $d = ests( 'subscription_tr_featured_listings_description' ) ) : ?>
                        <p><?php echo $d; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="es-plans-list">
                <?php foreach ( ests( 'plans' ) as $key => $plan ) : $plan = es_get_subscription_plan( $plan['ID'] ); ?>
                    <div class="<?php es_plan_classes( $plan ); ?>" <?php echo $plan->is_highlighted ? "style='background-color: {$plan->block_color}'" : ''; ?>>
                        <div class="es-plan__head">
                            <?php if ( $plan->is_label_active && $plan->label_text ) : ?>
                                <div class="es-badge" style="background-color: <?php echo $plan->label_color; ?>">
                                    <?php echo $plan->label_text; ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="es-plan__title heading-font"><?php echo $plan->name; ?></h3>

                            <?php if ( $plan->is_free_plan_enabled ) : ?>
                                <b class="es-plan__price"><?php _e( 'Totally free', 'es' ); ?></b>
                            <?php else : ?>
                                <?php foreach ( array( 'monthly', 'annual' ) as $price_period ) : $suff = es_price_suff( $price_period ); ?>
                                    <?php if ( $plan->{ $price_period . '_price' } ) : ?>
                                        <b class="js-es-plan__price es-plan__price es-plan__price--<?php echo $price_period; ?>">
                                            <?php echo es_format_value( $plan->{ $price_period . '_price' }, 'price', array( 'suff' => ' / ' . $suff ) ); ?>
                                        </b>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="es-plan__content">
                            <?php foreach ( array( 'basic', 'featured' ) as $type ) : ?>
                                <div class="es-plan__listings-count es-plan__listings-count--<?php echo $type; ?>">
                                    <?php if ( $plan->{'is_' . $type . '_listings_limited'} ) : ?>
                                        <span class="es-plan__listings-count-inner">
                                            <?php echo $plan->{$type . '_listings_limit'} ? $plan->{$type . '_listings_limit'} : '-'; ?>
                                        </span>
                                        <span class="es-plan__listings-count-inner--mobile">
                                            <?php printf( _x( '%s %s listing', '%s %s listings', $plan->{$type . '_listings_limit'}, 'es' ), (int) $plan->{$type . '_listings_limit'}, $type ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="es-plan__listings-count-inner">
                                            <?php _ex( 'Unlimited', 'subscription plan listings count', 'es' ); ?>
                                        </span>
                                        <span class="es-plan__listings-count-inner--mobile">
                                            <?php printf( __( 'Unlimited %s listings', 'es' ), $type ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="es-plan__footer">
                            <?php if ( es_user_has_provided_subscription_plan( $plan->get_id(), 'monthly' ) ) : ?>
                                <span class="es-current-plan es-plan__buy--monthly">
                                    <span class="es-icon es-icon_check-small es-secondary-color"></span>
                                    <span><?php _e( 'Your current Plan', 'es' ); ?></span>
                                </span>
                            <?php else :
                                if ( get_current_user_id() && current_user_can( 'agent' ) ) {
                                    $url = add_query_arg( array( 'screen' => 'checkout', 'period' => 'monthly', 'plan' => $plan->get_id() ), get_the_permalink() );
                                } else {
	                                if ( $url = es_get_page_url( 'login' ) ) {
		                                $url = add_query_arg( array(
			                                'redirect_url' => rawurlencode( es_get_current_url() ),
			                                'auth_item' => 'agent-register-form',
		                                ), $url );
                                    } else {
                                        $url = '#es-authentication-popup';
                                    }
                                } ?>
                                <a href="<?php echo $url; ?>"
                                   class="es-btn es-btn--buy es-plan__buy--monthly es-btn--secondary es-btn--bordered">
	                                <?php if ( get_current_user_id() && current_user_can( 'agent' ) ) : ?>
		                                <?php echo $plan->is_free_trial_enabled ? __( $plan->start_trial_button ) : __( $plan->main_button ); ?>
	                                <?php else : ?>
		                                <?php _e( 'Sign up as agent', 'es' ); ?>
	                                <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ( es_user_has_provided_subscription_plan( $plan->get_id(), 'annual' ) ) : ?>
                                <span class="es-current-plan es-plan__buy--annual">
                                    <span class="es-icon es-icon_check-small es-secondary-color"></span>
                                    <span><?php _e( 'Your current Plan', 'es' ); ?></span>
                                </span>
                            <?php else :
                                if ( get_current_user_id() && current_user_can( 'agent' ) ) {
                                    $url = add_query_arg( array( 'screen' => 'checkout', 'period' => 'annual', 'plan' => $plan->get_id() ), get_the_permalink() );
                                } else {
                                    if ( $url = es_get_page_url( 'login' ) ) {
                                        $url = add_query_arg( array(
	                                        'redirect_url', rawurlencode( es_get_current_url() ),
                                            'auth_item' => 'agent-register-form',
                                        ), $url );
                                    } else {
                                        $url = '#es-authentication-popup';
                                    }
                                } ?>
                                <a href="<?php echo $url; ?>"
                                   class="es-btn es-btn--buy es-plan__buy--annual es-btn--secondary es-btn--bordered">
                                    <?php if ( get_current_user_id() && current_user_can( 'agent' ) ) : ?>
	                                    <?php echo $plan->is_free_trial_enabled ? __( $plan->start_trial_button ) : __( $plan->main_button ); ?>
                                    <?php else : ?>
                                        <?php _e( 'Sign up as agent', 'es' ); ?>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ( ! $plan->is_free_trial_enabled && $plan->main_button_caption ) : ?>
                                <p class="es-plan__caption">
                                    <?php echo stripslashes( $plan->main_button_caption ); ?>
                                </p>
                            <?php elseif ( $plan->is_free_trial_enabled && $plan->start_trial_button_caption ) : ?>
                                <p class="es-plan__caption">
                                    <?php echo stripslashes( $plan->start_trial_button_caption ); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
		</div>
	</div>
<?php endif;
