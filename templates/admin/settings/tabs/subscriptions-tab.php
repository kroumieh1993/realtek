<h2><?php echo _x( 'Subscriptions', 'plugin settings', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--subscriptions es-settings-fields--max-width">
	<?php es_settings_field_render( 'is_subscriptions_enabled', array(
			'label' => __( 'Enable subscriptions', 'es' ),
			'type' => 'switcher',
			'description' => __( 'Enable Subscriptions to activate subscriptions plans, orders and other settings.', 'es' ),
			'attributes' => array(
				'data-toggle-container' => '#es-subscriptions-container',
			),
		) ); ?>

    <div id="es-subscriptions-container">
	    <?php es_settings_recommended_page_render( 'pricing_page_id', array(
		    'page_name' => __( 'Pricing page', 'es' ),
		    'page_display_name' => __( 'Pricing page', 'es' ),
		    'page_content' => '[es_subscriptions]',
	    ) ); ?>
    </div>
</div>
