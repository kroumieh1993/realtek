<div class="es-slideshow-info">
    <div class="es-slideshow-info__inner">
        <div class="es-slideshow-info__header--flex">
            <div class="es-title-container">
                <?php if ( ! empty( $fields_to_show['es_label'] ) )
                    do_action( 'es_property_badges' );

                if ( ! empty( $fields_to_show['title'] ) )
                    es_the_title( '<h3 class="heading-font"><a href="' . es_get_the_permalink() . '">', '</a></h3>' ); ?>
            </div>

            <div class="es-price-container">
                <?php if ( ! empty( $fields_to_show['price_note'] ) )
                    es_the_field( 'price_note', '<span class="es-badge es-badge--normal">', '</span>' );

                if ( ! empty( $fields_to_show['price'] ) )
                    es_the_price(); ?>
            </div>
        </div>

	    <?php
        do_action( 'es_property_meta', array( 'use_icons' => true ) );

	    if ( ! empty( $fields_to_show['address'] ) )
            es_the_address( '<div class="es-address">', '</div>' );

        include es_locate_template( 'front/property/partials/property-terms.php' );

        do_action( 'es_property_control', array(
            'show_sharing' => true,
            'show_wishlist' => ! empty( $attributes['enable_wishlist'] ),
            'is_full' => false,
            'context' => 'property-content',
            'share_popup_id' => 'es-share-popup-' . get_the_ID()
        ) );

	    es_the_property_share_popup( 'es-share-popup-' . get_the_ID() ); ?>
    </div>
</div>
