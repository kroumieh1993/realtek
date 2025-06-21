<h2><?php echo _x( 'Agents', 'plugin settings', 'es' ); ?></h2>

<?php es_settings_field_render( 'is_agents_enabled', array(
    'before' => '<div class="es-settings-fields es-settings-fields--agents es-settings-fields--max-width">',
    'label' => __( 'Enable agents', 'es' ),
    'type' => 'switcher',
    'attributes' => array(
        'data-toggle-container' => '#es-agent-fields-container'
    ),
    'after' => '</div>',
) ); ?>

<div id="es-agent-fields-container">
    <div class="es-settings-fields es-settings-fields--agents es-settings-fields--max-width">

        <?php es_settings_field_render( 'is_frontend_management_enabled', array(
            'label' => __( 'Enable adding new property via frontend', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'manual_listing_approve', array(
            'label' => __( 'Enable approving submit listing by admin', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'default_agent_avatar_attachment_id', array(
            'label' => __( 'Default agent profile photo', 'es' ),
            'type' => 'images',
            'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
            'button_label' => __( 'Upload image', 'es' ),
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'agents_layout', array(
        'label' => __( 'Default layout for listings pages', 'es' ),
        'type' => 'radio-image',
        'images' => array(
            'grid' => ES_PLUGIN_URL . 'admin/images/agent-grid-layout.svg',
            'list' => ES_PLUGIN_URL . 'admin/images/agent-list-layout.svg',
        ),
    ) ); ?>

    <div class="es-settings-fields es-settings-fields--agents es-settings-fields--max-width">
        <?php es_settings_field_render( 'is_agents_layout_switcher_enabled', array(
            'label' => __( 'Enable list view', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
	            'data-grid-label' => __( 'Enable grid view', 'es' ),
	            'data-list-label' => __( 'Enable list view', 'es' ),
            ),
        ) );

        es_settings_field_render( 'agents_per_page', array(
            'label' => __( 'Agents number per page', 'es' ),
            'type' => 'number',
        ) );

        es_settings_field_render( 'is_agent_rating_enabled', array(
            'label' => __( 'Show agent rating', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'is_agent_comments_enabled', array(
            'label' => __( 'Show comments', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'agent_comments_per_page', array(
            'label' => __( 'Comments number per page', 'es' ),
            'type' => 'number',
        ) );

        es_settings_field_render( 'is_agent_commenting_enabled', array(
            'label' => __( 'Enable commenting', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'agent_listings_per_page', array(
            'label' => __( 'Agent listings number per page', 'es' ),
            'type' => 'number',
        ) );

        es_settings_field_render( 'is_agents_sorting_enabled', array(
	        'label' => __( 'Enable sorting', 'es' ),
	        'type' => 'switcher',
	        'attributes' => array(
		        'data-toggle-container' => '#es-agents-sorting-container',
	        ),
        ) ); ?>

        <div id="es-agents-sorting-container" class="es-hidden">
		    <?php es_settings_field_render( 'agents_sorting_options', array(
			    'label' => __( 'Sort options', 'es' ),
			    'type' => 'checkboxes',
		    ) );

		    es_settings_field_render( 'agents_default_sorting_option', array(
			    'label' => __( 'Default sort options', 'es' ),
			    'type' => 'select',
		    ) ); ?>
        </div>

        <?php es_settings_field_render( 'is_agents_wishlist_enabled', array(
            'label' => __( 'Enable wishlist', 'es' ),
            'type' => 'switcher',
        ) );

        es_settings_field_render( 'is_agents_sharing_enabled', array(
	        'label' => __( 'Enable sharing', 'es' ),
	        'type' => 'switcher',
        ) );

        es_settings_field_render( 'is_agent_position_enabled', array(
	        'label' => __( 'Enable agent position', 'es' ),
	        'type' => 'switcher',
        ) );

        es_settings_field_render( 'is_agent_contact_form_enabled', array(
	        'label' => __( 'Enable Contact form on Agent page', 'es' ),
	        'type' => 'switcher',
	        'attributes' => array(
		        'data-toggle-container' => '#es-agent-contact-btn'
	        ),
        ) );

        es_settings_field_render( 'is_agent_contact_button_enabled', array(
	        'before' => "<div id='es-agent-contact-btn'>",
	        'label' => __( 'Enable Contact button', 'es' ),
	        'type' => 'switcher',
	        'after' => '</div>'
        ) ); ?>
    </div>
</div>