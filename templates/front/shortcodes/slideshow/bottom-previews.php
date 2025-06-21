<?php

/**
 * @var $query WP_Query
 * @var $attributes array
 */

$pager_config = array(
    'slidesToShow' => 6,
    'arrows' => true,
    'dots' => false,
	'prevArrow' => '<span class="es-slick-arrow slick-prev">
        <span class="es-icon es-icon_chevron-left"></span></span>',
    'nextArrow' => '<span class="es-slick-arrow slick-next">
        <span class="es-icon es-icon_chevron-right"></span></span>',
    'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__images-slider"
);

$images_config = array(
    'adaptiveHeight' => true,
    'arrows' => false,
    'autoplay' => $attributes['autoplay'],
    'dots' => false,
	'pauseOnHover' => true,
	'slidesToShow' => 1,
	'slidesToScroll' => 1,
	'prevArrow' => '<span class="es-slick-arrow slick-prev"><span class="es-icon es-icon_chevron-left"></span></span>',
	'nextArrow' => '<span class="es-slick-arrow slick-next"><span class="es-icon es-icon_chevron-right"></span></span>',
    'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__pager-slider"
);

if ( $query->have_posts() ) : ?>
    <style>
        #<?php echo $attributes['id']; ?> .es-p-slideshow__images-slider .slick-slide img {
            max-height: <?php echo $attributes['max_height']; ?>;
        }
    </style>

	<div id="<?php echo $attributes['id']; ; ?>" class="content-font js-es-p-slideshow es-p-slideshow es-p-slideshow--<?php echo $attributes['layout']; ?> es-p-slideshow--side-<?php echo $attributes['side']; ?>">
		<?php if ( $attributes['enable_progress_bar'] ) : ?>
			<?php include es_locate_template( 'front/shortcodes/slideshow/partials/progress-bar.php' ); ?>
		<?php endif; ?>

        <div class="es-p-slideshow__images-slider js-es-p-slideshow__images-slider" data-slick-config="<?php echo es_esc_json_attr( $images_config ); ?>">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <div class="es-p-slideshow__slide">
                    <img class="es-slick-slide-image" src="<?php echo es_get_the_featured_image_url( 'large' ); ?>" alt="<?php the_title(); ?>"/>
                    <?php es_the_property_share_popup( 'es-share-popup-' . get_the_ID() );

                    do_action( 'es_property_control', array(
                        'show_sharing' => true,
                        'show_wishlist' => ! empty( $attributes['enable_wishlist'] ),
                        'is_full' => false,
                        'context' => 'property-image',
                        'share_popup_id' => 'es-share-popup-' . get_the_ID()
                    ) ); ?>
                    <div class="es-slideshow-info">
                        <div class="es-slideshow-info__inner">
                            <div class="es-slideshow-info__header es-slideshow-info__header--flex">
                                <div class="es-title-container">
		                            <?php if ( ! empty( $fields_to_show['es_label'] ) )
                                        do_action( 'es_property_badges' );

                                    if ( ! empty( $fields_to_show['title'] ) )
		                                es_the_title( '<h3 class="heading-font"><a href="' . es_get_the_permalink() . '">', '</a></h3>' ); ?>

                                    <div class="es-address-container">
		                                <?php if ( ! empty( $fields_to_show['address'] ) )
                                            es_the_address( '<div class="es-address">', '</div>' );

		                                include es_locate_template( 'front/property/partials/property-terms.php' ); ?>
                                    </div>
                                </div>
                                <div class="es-price-container">
		                            <?php do_action( 'es_property_control', array(
			                            'show_sharing' => true,
			                            'show_wishlist' => ! empty( $attributes['enable_wishlist'] ),
			                            'is_full' => false,
			                            'context' => 'property-image'
		                            ) );

		                            if ( ! empty( $fields_to_show['price'] ) )
                                        es_the_price();

		                            if ( ! empty( $fields_to_show['price_note'] ) )
		                                es_the_field( 'price_note', '<span class="es-badge es-badge--normal">', '</span>' );

		                            do_action( 'es_property_meta', array( 'use_icons' => true ) );?>
                                </div>
                            </div>
                            <div class="es-address-container">
		                        <?php if ( ! empty( $fields_to_show['address'] ) )
                                    es_the_address( '<div class="es-address">', '</div>' );

		                        include es_locate_template( 'front/property/partials/property-terms.php' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endwhile; wp_reset_postdata(); ?>
        </div>

        <div class="es-p-slideshow__pager-slider">
            <div class="es-p-slideshow__pager-slider-inner js-es-p-slideshow__pager-slider" data-slick-config="<?php echo es_esc_json_attr( $pager_config ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <img src="<?php echo es_get_the_featured_image_url( 'thumbnail' ); ?>" alt="<?php the_title(); ?>"/>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
	</div>
<?php endif;
