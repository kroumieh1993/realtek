<?php

/**
 * @var $css_layout string
 * @var $query WP_Query
 * @var $hash string
 * @var $plural_entity_name string
 * @var $entity_name string
 */

?>

<div class="es-entities__wrap-inner js-es-entities__wrap_inner et_smooth_scroll_disabled">
	<?php do_action( 'es_before_entities', $entity_name );

	if ( $query->have_posts() && empty( $args['disable_navbar'] ) && ( ! empty( $args['show_sort'] ) || ! empty( $args['show_total'] ) || ! empty( $args['show_layouts'] ) ) ) : ?>
		<?php do_action( "es_entities_navbar", array_merge( $args, array( 'query' => $query ) ) ); ?>
	<?php endif; ?>

	<div data-entity="<?php echo $entity_name; ?>" class="js-es-<?php echo $plural_entity_name; ?> es-<?php echo $plural_entity_name; ?> js-es-entities es-entities <?php echo $css_layout; ?>" data-layout="<?php echo $css_layout; ?>" data-hash="<?php esc_attr_e( $hash ); ?>">
		<?php if ( $query->have_posts() ) : ?>
			<?php while( $query->have_posts() ) : $query->the_post(); ?>
				<?php include es_locate_template( "front/entity/content-archive.php" ); ?>
			<?php endwhile; wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
	<?php if ( ! $query->have_posts() ) : ?>
		<?php do_action( 'es_entities_no_found_posts', $args ); ?>
	<?php endif; ?>
	<?php if ( $query->have_posts() && ! empty( $args['view_all_page_id'] ) && ! empty( $args['view_all_link_name'] ) ) : ?>
		<div class="es-view-all">
			<a href="<?php echo get_permalink( $args['view_all_page_id'] ); ?>" class="es-btn es-btn--secondary es-btn--bordered">
				<?php _e( $args['view_all_link_name'], 'es' ); ?>
			</a>
		</div>
	<?php endif;

	if ( empty( $args['disable_pagination'] ) ) {
		es_the_pagination( $query );
	}

	do_action( 'es_after_entities', $entity_name ); ?>
</div>
