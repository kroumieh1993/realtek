<div class="es-wrap">
    <form class="es-filter">
		<?php do_action( 'es_orders_filter_before' );
		$plans = ests( 'plans' );
        $plans = $plans ? wp_list_pluck( $plans, 'name', 'ID' ) : array();
        $plans = array( 'one_time_payment' => __( 'One time payment', 'es' ) ) + $plans; ?>

        <input type="hidden" name="post_type" value="<?php echo Es_Order::get_post_type_name(); ?>"/>

        <div class="es-row" style="align-items: flex-end;">
            <div class="es-col-xl-2 es-col-md-6">
				<?php es_entities_filter_field_render( 's', array(
					'label' => __( 'Agent search', 'es' ),
					'type' => 'text',
					'attributes' => array(
						'placeholder' => __( 'Agent email or username', 'es' ),
					),
					'wrapper_class' => 'es-field--small'
				) ); ?>
            </div>
            <div class="es-col-xl-2 es-col-md-6">
                <?php es_entities_filter_field_render( 'plan_id', array(
                    'label'         => __( 'Subscription plan', 'es' ),
                    'type'          => 'select',
                    'attributes'    => array(
                        'placeholder' => __( 'Choose plan', 'es' ),
                    ),
                    'options' => $plans,
                    'wrapper_class' => 'es-field--small'
                ) ); ?>
            </div>
            <div class="es-col-xl-2 es-col-md-6">
		        <?php es_entities_filter_field_render( 'status', array(
			        'label'         => __( 'Subscription status', 'es' ),
			        'type'          => 'select',
			        'attributes'    => array(
				        'placeholder' => __( 'Choose status', 'es' ),
			        ),
			        'options' => Es_Order::get_subscription_statuses(),
			        'wrapper_class' => 'es-field--small'
		        ) ); ?>
            </div>
            <div class="es-col-xl-4 es-col-md-6">
		        <div class="es-row">
                    <div class="es-col-6">
                        <?php es_entities_filter_field_render( 'start_date_from', array(
                            'label'         => __( 'Start date from', 'es' ),
                            'type'          => 'date',
                            'wrapper_class' => 'es-field--small',
                            'attributes'    => array(
                                'placeholder'      => _x( 'From', 'subscription start date', 'es' ),
                                'data-date-format' => ests( 'date_format' ),
                            ),
                        ) ); ?>
                    </div>
                    <div class="es-col-6">
	                    <?php es_entities_filter_field_render( 'start_date_to', array(
		                    'label'         => __( 'Start date to', 'es' ),
		                    'type'          => 'date',
		                    'wrapper_class' => 'es-field--small',
		                    'attributes'    => array(
			                    'placeholder'      => _x( 'To', 'subscription start date', 'es' ),
			                    'data-date-format' => ests( 'date_format' ),
		                    ),
	                    ) ); ?>
                    </div>
                </div>
            </div>
            <div class="es-col-xl-2 es-col-md-6" style="margin-bottom: 10px;">
                <button type="submit" class="es-btn es-btn--third es-btn--icon es-btn--small">
                    <span class="es-icon es-icon_search"></span>
		            <?php _e( 'Search' ); ?>
                </button>
            </div>
        </div>

		<?php do_action( 'es_requests_filter_after' ); ?>
    </form>
</div>
