<?php

/**
 * @var $query WP_Query
 * @var $attributes array
 */

$info_config = array(
	'dots' => true,
	'dotsClass' => 'slick-dots es-slick-dots',
	'slidesToShow' => 1,
	'slidesToScroll' => 1,
	'arrows' => false,
	'fade' => true,
	'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__images-slider"
);

$images_config = array(
    'arrows' => (bool) $attributes['is_arrows_enabled'],
    'dots' => false,
	'autoplay' => $attributes['autoplay'],
	'pauseOnHover' => true,
	'slidesToShow' => 1,
	'slidesToScroll' => 1,
	'prevArrow' => '<span class="es-slick-arrow slick-prev"><span class="es-icon es-icon_chevron-left"></span></span>',
	'nextArrow' => '<span class="es-slick-arrow slick-next"><span class="es-icon es-icon_chevron-right"></span></span>',
    'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__info-slider"
);

if ( $query->have_posts() ) : ?>
    <style>
        #<?php echo $attributes['id']; ?> .es-p-slideshow__info-slider {
            background-color: <?php echo $attributes['info_background']; ?>;
        }

        #<?php echo $attributes['id']; ?> .es-p-slideshow__images-slider .slick-slide img {
            max-height: <?php echo $attributes['max_height']; ?>;
        }
    </style>
    <div id="<?php echo $attributes['id']; ; ?>" class="content-font js-es-p-slideshow es-p-slideshow es-p-slideshow--<?php echo $attributes['layout']; ?>">
	    <?php if ( $attributes['enable_progress_bar'] ) : ?>
		    <?php include es_locate_template( 'front/shortcodes/slideshow/partials/progress-bar.php' ); ?>
	    <?php endif; ?>

        <div class="es-p-slideshow__info-slider">
            <div class="es-p-slideshow__info-slider-inner js-es-p-slideshow__info-slider" data-slick-config="<?php echo es_esc_json_attr( $info_config ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="es-slideshow-info">
                        <div class="es-slideshow-info__header">
                            <div class="es-slideshow-info__header__top">
	                            <?php if ( ! empty( $fields_to_show['es_label'] ) ) {
		                            do_action( 'es_property_badges' );
	                            }

                                es_the_property_share_popup( 'es-share-popup-' . get_the_ID() );

                                do_action( 'es_property_control', array(
                                    'show_sharing' => true,
                                    'show_wishlist' => ! empty( $attributes['enable_wishlist'] ),
                                    'is_full' => false,
                                    'share_popup_id' => 'es-share-popup-' . get_the_ID()
                                ) ); ?>
                            </div>
                            <?php
                            if ( ! empty( $fields_to_show['title'] ) )
                                es_the_title( '<h3 class="heading-font"><a href="' . es_get_the_permalink() . '">', '</a></h3>' );

                            if ( ! empty( $fields_to_show['price'] ) )
                                es_the_price();

                            if ( ! empty( $fields_to_show['price_note'] ) )
                                es_the_field( 'price_note', '<span class="es-badge es-badge--normal">', '</span>' ); ?>
                        </div>
                        <div class="es-slideshow-info__body">
	                        <?php do_action( 'es_property_meta', array( 'use_icons' => true ) );

	                        if ( ! empty( $fields_to_show['address'] ) )
	                            es_the_address( '<div class="es-address">', '</div>' );

	                        include es_locate_template( 'front/property/partials/property-terms.php' ); ?>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <div class="es-p-slideshow__images-slider js-es-p-slideshow__images-slider" data-slick-config="<?php echo es_esc_json_attr( $images_config ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <img class="es-slick-slide-image" src="<?php echo es_get_the_featured_image_url( 'large' ); ?>" alt="<?php the_title(); ?>"/>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif;
