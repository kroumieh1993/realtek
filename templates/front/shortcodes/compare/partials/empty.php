<?php

/**
 * @var $query WP_Query
 */

?>
<div class="es-empty-container js-es-compare-empty <?php es_active_class( $query && $query->have_posts(), true, 'es-hidden' ); ?>">
    <h2 class="heading-font"><?php _e( 'No properties to compare', 'es' ); ?></h2>
    <p class="content-font"><?php _e( 'You don’t have any properties to compare yet.', 'es' ); ?></p>
    <?php if ( $url = es_get_page_url( 'search_results' ) ) : ?>
        <a href="<?php echo esc_url( $url ); ?>" class="es-btn es-btn--secondary"><?php _e( 'Go back', 'es' ); ?></a>
    <?php endif; ?>
</div>
