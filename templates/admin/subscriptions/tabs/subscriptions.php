<?php if ( $plans = ests( 'plans' ) ) : ?>

	<table class="es-entity-table js-es-plans-table">
		<thead>
			<tr>
				<th><?php _e( 'ID', 'subscription plan' ); ?></th>
				<th><?php _e( 'Plan name', 'subscription plan' ); ?></th>
				<th><?php _e( 'Basic listings', 'subscription plan' ); ?></th>
				<th><?php _e( 'Featured listings', 'subscription plan' ); ?></th>
				<th><?php _e( 'Price / mo', 'subscription plan' ); ?></th>
				<th><?php _e( 'Price / year', 'subscription plan' ); ?></th>
				<th><?php _e( 'Actions', 'subscription plan' ); ?></th>
			</tr>
		</thead>
		<tbody>
        <?php foreach ( $plans as $key => $plan ) : $plan = es_get_subscription_plan( $plan['ID'] ); ?>
            <tr data-plan-id="<?php echo $plan->get_id(); ?>">
                <td>
                    <?php echo $plan->get_id(); ?>
                </td>
                <td>
                    <span class="es-plan-name"><?php echo $plan->name; ?></span>
                    <?php if ( $plan->is_default ) : ?>
                        <span class="es-default-plan"><?php _e( 'Default plan', 'es' ); ?></span>
                    <?php endif; ?>

                    <?php if ( $plan->is_label_active && $plan->label_text ) : ?>
	                    <span class="es-label" style="background-color: <?php echo $plan->label_color; ?>">
                            <?php echo $plan->label_text; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td><?php echo $plan->basic_listings_limit ? $plan->basic_listings_limit : '-'; ?></td>
                <td><?php echo $plan->featured_listings_limit ? $plan->featured_listings_limit : '-'; ?></td>
                <td>
                    <?php if ( $plan->is_free_plan_enabled ) : ?>
                        <?php _ex( 'FREE', 'subscription plan', 'es' ); ?>
                    <?php else : ?>
	                    <?php echo $plan->monthly_price ? es_format_value( $plan->monthly_price, 'price' ) : '-'; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $plan->annual_price ? es_format_value( $plan->annual_price, 'price' ) : '-'; ?></td>
                <td class="es-column es-column--actions">
                    <a href="<?php echo add_query_arg( array( 'plan_id' => $plan->get_id(), 'action' => 'delete', '_nonce' => wp_create_nonce( 'es_delete_plan' ) ) ); ?>" data-message="<?php printf( __( 'Delete %s plan?', 'es' ), $plan->name ); ?>" class="js-es-confirm-delete"><span class="es-icon es-icon_trash"></span></a>
                    <a href="<?php echo add_query_arg( array( 'tab' => 'plan-form', 'plan_id' => $plan->get_id() ) ); ?>"><span class="es-icon es-icon_settings"></span></a>
                    <a href="#"><span class="es-icon es-icon_arrows-hv"></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p><?php _e( 'You don’t have any plans yet.', 'es' ); ?></p>
<?php endif; ?>

<a style="margin-top: 10px;" href="<?php echo add_query_arg( array( 'tab' => 'plan-form', 'plan_id' => false ) ); ?>" class="es-btn es-btn--third es-btn--small">
    <span class="es-icon es-icon_plus"></span><?php _e( 'Create plan', 'es' ); ?>
</a>
