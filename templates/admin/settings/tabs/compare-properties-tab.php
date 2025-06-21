<h2><?php echo _x( 'Comparing properties', 'plugin settings', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">

    <?php es_settings_field_render( 'is_compare_enabled', array(
        'label' => __( 'Enable comparing properties', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-compare-container',
        ),
    ) ); ?>

    <div id="es-compare-container">
	    <?php es_settings_recommended_page_render( 'compare_page_id', array(
		    'page_name' => __( 'Compare properties', 'es' ),
		    'page_display_name' => __( 'Compare properties page', 'es' ),
		    'page_content' => '[es_compare]',
	    ) ); ?>

	    <?php es_settings_field_render( 'is_compare_auth_required', array(
		    'label' => __( 'Enable comparing properties through log in', 'es' ),
		    'type' => 'switcher',
	    ) ); ?>

        <?php es_settings_field_render( 'compare_max_entities_num', array(
		    'label' => __( 'Maximum number of properties to compare', 'es' ),
		    'type' => 'incrementer',
	    ) ); ?>

	    <?php es_settings_field_render( 'compare_max_entities_num_error', array(
		    'label' => __( 'Error if user tries to compare more properties', 'es' ),
		    'type' => 'text',
	    ) ); ?>

	    <?php es_settings_field_render( 'compare_fields', array(
		    'label' => __( 'Parameters to compare', 'es' ),
		    'type' => 'fields-list-selector',
		    'options' => es_get_available_compare_fields(),
            'field_label' => __( 'Add new parameter', 'es' ),
		    'attributes' => array(
			    'placeholder' => __( 'Select among existing', 'es' ),
		    ),
		    'add_button' => "<button disabled class='es-btn es-btn--third js-es-add-fields-item'>" . __( 'Add', 'es' ) . "</button>",
		    'item_markup' => "<li class='es-item' data-item-id='{item-id}'><b>{field_name}</b>{hidden}<a href='#' class='js-es-delete-fields-item'><span class='es-icon es-icon_close'></a></li>",
	    ) ); ?>
    </div>
</div>
