<?php

/**
 * @var $config array
 */

$sort = sanitize_text_field( filter_input( INPUT_GET, 'sort' ) );

$search_instance = es_get_shortcode_instance( 'es_search_form', $config );

echo $search_instance->get_content(); ?>

<div class="es-sort-wrap">
    <form action="<?php echo es_get_current_url(); ?>" method="get" class="es-property-sort-form js-es-submit-on-form-change">
        <?php do_action( 'es_sort_dropdown', $sort ? $sort : ests( 'properties_default_sorting_option' ) ); ?>
	    <?php if ( ! get_option( 'permalink_structure' ) ) : ?>
            <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>"/>
	    <?php endif; ?>
	    <?php if ( $tab = es_get( 'tab' ) ) : ?>
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
	    <?php endif; ?>
    </form>

    <?php if ( ests( 'is_frontend_management_enabled' ) && ! empty( $config['show_add_new'] ) && ( $url = es_get_add_new_property_url() ) ) : ?>
        <a href="<?php echo $url; ?>" class="es-btn es-btn--secondary">
            <span class="es-icon es-icon_plus"></span>
            <?php _e( 'Add new home', 'es' ); ?>
        </a>
    <?php endif; ?>
</div>