<h2><?php _e( 'Agent & Agency search', 'es' ); ?></h2>

<?php es_settings_recommended_page_render( 'agent_agency_search_results_page_id', array(
	'page_name'         => __( 'Search results', 'es' ),
	'page_display_name' => __( 'Default Search results', 'es' ),
	'page_content'      => '[es_agent_agency]',
) );