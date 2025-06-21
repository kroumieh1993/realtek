<h2><?php echo _x( 'User Profile', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
	<?php es_settings_recommended_page_render( 'profile_page_id', array(
		'page_name'         => __( 'Profile', 'es' ),
		'page_display_name' => __( 'Profile', 'es' ),
		'page_content'      => '[es_profile]',
	) );

	es_settings_field_render( 'is_profile_requests_tab_enabled', array(
		'label' => __( 'Enable requests tab', 'es' ),
		'type' => 'switcher',
	) ); ?>
</div>
