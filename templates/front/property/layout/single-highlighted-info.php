<?php
/**
 * Modern highlighted layout for single property page.
 *
 * @var int $post_id
 */
$property = es_get_the_property( get_the_ID() );
?>
<div class="es-single__highlighted">
    <div class="es-single__hero">
        <?php do_action( 'es_property_badges' ); ?>
        <?php es_the_property_slider(); ?>
        <?php es_the_mobile_slider(); ?>
    </div>

    <div class="es-single__summary-wrap">
        <div class="es-single__summary-main">
            <?php es_the_title( '<h1 class="property-title heading-font">', '</h1>' ); ?>
            <?php do_action( 'es_single_property_after_title', get_the_ID() ); ?>

            <div class="es-single__address-container">
                <?php es_the_address( '<span class="es-address">', '</span>' ); ?>
                <?php es_load_template( 'front/property/partials/property-terms.php' ); ?>
            </div>

            <div class="es-single__actions">
                <?php if ( es_get_the_field( 'price' ) ) : ?>
                    <div class="es-price-container">
                        <?php es_the_price(); ?>
                        <?php es_the_field( 'price_note', '<span class="es-badge es-price-badge es-badge--normal">', '</span>' ); ?>
                    </div>
                <?php endif; ?>

                <div class="es-control-wrap">
                    <?php do_action( 'es_property_control', array(
                        'show_sharing' => true,
                        'is_full' => false,
                        'icon_size' => 'big'
                    ) ); ?>
                </div>
            </div>

            <?php es_load_template( 'front/property/partials/property-meta.php', array(
                'use_icons' => true,
            ) ); ?>
        </div>

        <aside class="es-single__summary-side">
            <div class="es-single__summary-card">
                <?php if ( es_get_the_field( 'price' ) ) : ?>
                    <div class="es-price-container es-price-container--stacked">
                        <?php es_the_price(); ?>
                        <?php es_the_field( 'price_note', '<span class="es-badge es-price-badge es-badge--normal">', '</span>' ); ?>
                    </div>
                <?php endif; ?>

                <ul class="es-single__highlights">
                    <?php if ( es_get_the_field( 'bedrooms', $property ) ) : ?>
                        <li>
                            <span class="es-single__highlights-label"><?php _e( 'Bedrooms', 'es' ); ?></span>
                            <span class="es-single__highlights-value"><?php es_the_field( 'bedrooms', '', '' ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( es_get_the_field( 'bathrooms', $property ) ) : ?>
                        <li>
                            <span class="es-single__highlights-label"><?php _e( 'Bathrooms', 'es' ); ?></span>
                            <span class="es-single__highlights-value"><?php es_the_field( 'bathrooms', '', '' ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( es_get_the_field( 'area', $property ) ) : ?>
                        <li>
                            <span class="es-single__highlights-label"><?php _e( 'Area', 'es' ); ?></span>
                            <span class="es-single__highlights-value"><?php es_the_field( 'area', '', '' ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( es_get_the_field( 'es_category', $property ) ) : ?>
                        <li>
                            <span class="es-single__highlights-label"><?php _e( 'Status', 'es' ); ?></span>
                            <span class="es-single__highlights-value"><?php es_the_field( 'es_category', '', '' ); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>

                <?php if ( es_is_request_form_active() && ! ests( 'is_request_form_button_disabled' ) ) : ?>
                    <a href="#request_form" class="es-btn es-btn--primary es-btn--full es-btn--request-info js-es-scroll-to">
                        <?php _e( 'Request info', 'es' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
