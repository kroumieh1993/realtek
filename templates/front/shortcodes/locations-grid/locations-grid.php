<?php

/**
 * @var $items array
 * @var $args array
 * @var $instance Es_Locations_Grid_Shortcode
 */

if ( ! empty( $items ) ) : ?>
<div class="es-locations-grid js-es-locations-grid content-font<?php echo count( $items ) > 4 && ! empty( $enable_slider ) ? ' es-locations-grid--slider js-es-locations-slick' : ''; ?>">
	<?php foreach ( $items as $item ) :
        $term = ! empty( $item['location_id'] ) ? get_term( $item['location_id'], 'es_location' ) : null;
		$image = ! empty( $item['image'] ) ? wp_get_attachment_image( $item['image'], 'large' ) : null;
        if ( ! $term || is_wp_error( $term ) || ! $image ) continue;
		$args['es_location'] = $term->term_id; ?>
		<div class="es-location-item">
            <a href="<?php echo $instance->generate_location_link( $args ); ?>">
	            <?php echo $image; ?>
                <div class="es-location-item__inner">
                    <div class="es-location-item__content">
                        <h3 class="heading-font"><?php echo $term->name; ?></h3>
			            <?php if ( ! empty( $enable_counter ) ) : $count = $instance->count_properties( $args ); ?>
                            <p><?php printf( _n( '%s property', '%s properties', $count, 'es' ), $count ); ?></p>
			            <?php endif; ?>
                    </div>
                </div>
            </a>
		</div>
	<?php endforeach; ?>
</div>
<?php endif;
