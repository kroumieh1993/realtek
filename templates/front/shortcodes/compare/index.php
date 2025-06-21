<?php

/**
 * @var $query WP_Query
 * @var $compare Es_Compare
 * @var $entities_ids array
 * @var $attr array
 */

$grouped_terms = $compare->group_terms();

if ( $query && $query->have_posts() && ( $fields = $compare->get_fields() ) ) : ?>
	<div class="es-compare js-es-compare-wrapper content-font">
        <div class="es-compare__heading">
            <?php if ( ! empty( $attr['title'] ) ) : ?>
                <h2 class="heading-font"><?php echo $attr['title']; ?></h2>
            <?php endif; ?>
            <div class="es-fields-switcher-wrap">
                <button class="js-es-compare-fields-switcher es-btn es-btn--secondary es-btn--bordered" data-toggle-label="<?php _e( 'View all', 'es' ); ?>">
                    <?php _e( 'View only differences', 'es' ); ?>
                </button>
                <?php if ( $back_url = es_get_compare_back_url() ) : ?>
                    <a href="<?php echo esc_url( $back_url ); ?>" class="es-secondary-color"><?php echo __( 'Back to listings', 'es' ); ?></a>
                <?php endif; ?>
            </div>
            <a href="#" class="es-secondary-color es-compare-del-entity js-es-compare-del-all es-preload-link"><span class="es-icon es-icon_close"></span><?php _e( 'Remove all', 'es' ); ?></a>
        </div>
		<div class="es-compare__listings-wrap">
            <div class="es-compare__control-wrap"></div>
            <div class="es-compare__listings js-es-compare__listings-slider">
	            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="js-es-listing-<?php the_ID(); ?> js-es-listings js-es-entities es-listings es-listings--grid-1 es-listings--ignore-responsive js-es-compare-listing">
                        <a href="#" class="es-secondary-color es-compare-del-entity js-es-compare-del-entity es-preload-link" data-entity-id="<?php the_ID(); ?>"><span class="es-icon es-icon_close"></span><?php _e( 'Remove', 'es' ); ?></a>
			            <?php es_load_template( 'front/property/content-archive.php', array(
				            'ignore_wrapper' => true,
				            'show_compare' => false,
			            ) ); ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
		</div>
		<div class="es-compare__fields-wrap">
            <div class="es-compare__fields">
	            <?php foreach ( $fields as $field ) : ?>
		            <?php if ( $compare->is_grouped_field( $field ) && ! empty( $grouped_terms[ $field ] ) ) : ?>
                        <div class="es-compare__field js-es-compare__field" data-field="<?php echo $field; ?>">
                            <b><?php echo es_property_get_field_label( $field ); ?></b>
                        </div>
                        <?php foreach ( $grouped_terms[ $field ] as $term ) : ?>
                            <div class="es-compare__field js-es-compare__field" data-field="<?php echo $term->name; ?>">
                                <span><?php echo $term->name; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ( ! $compare->is_grouped_field( $field ) ) : ?>
                        <div class="es-compare__field js-es-compare__field" data-field="<?php echo $field; ?>">
                            <b><?php echo es_property_get_field_label( $field ); ?></b>
                        </div>
                    <?php endif; ?>
	            <?php endforeach; ?>
            </div>
            <div class="es-compare__values js-es-compare__listings-values-slider">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="es-compare__listing-values js-es-listing-<?php the_ID(); ?>">
		                <?php foreach ( $fields as $field ) : ?>
                            <?php if ( $compare->is_grouped_field( $field ) && ! empty( $grouped_terms[ $field ] ) ) : ?>
                                <div class="es-compare__listing-value es-compare__listing-value--clear">
                                    <div class="es-compare__mobile-label">
                                        <b><?php echo es_property_get_field_label( $field ); ?></b>
                                    </div>
                                </div>

				                <?php foreach ( $grouped_terms[ $field ] as $term ) : ?>
                                    <div class="es-compare__listing-value js-es-compare__listing-value" data-field="<?php echo $term->slug; ?>">
                                        <div class="es-compare__mobile-label">
                                            <b><?php echo $term->name; ?></b>
                                        </div>
	                                    <?php if ( has_term( $term, $term->taxonomy, get_the_ID() ) ) : ?>
                                            <span class="es-icon es-icon_check-mark es-secondary-color"></span>
	                                    <?php else : ?>
                                            <span class="es-icon es-icon_minus es-secondary-color"></span>
	                                    <?php endif; ?>
                                    </div>
				                <?php endforeach; ?>
                            <?php elseif ( ! $compare->is_grouped_field( $field ) ) : ?>
                                <div class="es-compare__listing-value js-es-compare__listing-value" data-field="<?php echo $field; ?>">
                                    <div class="es-compare__mobile-label">
                                        <b><?php echo es_property_get_field_label( $field ); ?></b>
                                    </div>
                                    <span><?php echo es_get_the_formatted_field( $field, get_the_ID() ); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
		</div>
	</div>
<?php endif;

include es_locate_template( 'front/shortcodes/compare/partials/empty.php' );
