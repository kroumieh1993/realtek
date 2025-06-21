<?php

/**
 * @var $query WP_Query
 * @var $attributes array
 */

$info_config = array(
    'dots' => false,
    'adaptiveHeight' => true,
	'slidesToShow' => 1,
    'slidesToScroll' => 1,
    'arrows' => false,
    'fade' => true,
    'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__images-slider, #{$attributes['id']} .js-es-p-slideshow__pager-slider"
);

$images_config = array(
	'arrows' => (bool) $attributes['is_arrows_enabled'],
    'adaptiveHeight' => true,
	'autoplay' => $attributes['autoplay'],
	'pauseOnHover' => true,
	'slidesToShow' => 1,
    'dots' => true,
    'slidesToScroll' => 1,
	'prevArrow' => '<span class="es-slick-arrow slick-prev"><span class="es-icon es-icon_chevron-left"></span></span>',
    'nextArrow' => '<span class="es-slick-arrow slick-next"><span class="es-icon es-icon_chevron-right"></span></span>',
	'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__pager-slider, #{$attributes['id']} .js-es-p-slideshow__info-slider"
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
				<img class="es-slick-slide-image" src="<?php echo es_get_the_featured_image_url( 'large' ); ?>" alt="<?php the_title(); ?>"/>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

        <div class="es-p-slideshow__info-slider">
            <div class="es-p-slideshow__info-slider-inner js-es-p-slideshow__info-slider" data-slick-config="<?php echo es_esc_json_attr( $info_config ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php include es_locate_template( 'front/shortcodes/slideshow/partials/property-info.php' ); ?>
				<?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>

        <?php if ( 'side-previews' == $attributes['layout'] ) :
	        $pager_config = array(
		        'slidesToShow' => 4,
                'slidesToScroll' => 1,
                'arrows' => false,
                'dots' => false,
                'vertical' => true,
                'verticalSwiping' => true,
                'asNavFor' => "#{$attributes['id']} .js-es-p-slideshow__info-slider, #{$attributes['id']} .js-es-p-slideshow__images-slider"
            ); ?>
            <div class="es-p-slideshow__pager-slider js-es-p-slideshow__pager-slider" data-slick-config="<?php echo es_esc_json_attr( $pager_config ); ?>">
	            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <img src="<?php echo es_get_the_featured_image_url( 'thumbnail' ); ?>" alt="<?php the_title(); ?>"/>
	            <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif;
