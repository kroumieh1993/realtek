<?php

/**
 * @var $entity
 * @var $entity_control_config
 */

?>
<div class="es-entity__content">
	<div class="es-entity__basic">
		<h3 class="es-entity__title">
			<a href="<?php echo es_get_the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<?php if ( ( $rating = es_get_rating_markup( $entity->get_rating() ) ) || (  ests( 'is_agency_comments_enabled' ) && comments_open() ) ) : ?>
			<div class="es-entity__rating">
                <?php if ( ests( 'is_agency_rating_enabled' ) ) : ?>
				    <?php echo $rating; ?>
                <?php endif; ?>
				<?php do_action( 'es_reviews_link', 'agency' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $entity->address ) : ?>
			<div class="es-entity__address">
				<span class="es-icon es-icon_pin"></span>
                <?php echo $entity->address; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'es_stats_counter', get_the_ID() ); ?>
</div>

<?php do_action( 'es_entity_control', $entity_control_config ); ?>

<div class="es-entity__contact">
	<?php if ( $contact_config = $entity->get_preferred_contact_config() ) : ?>
        <div class="es-preferred-contact es-preferred-contact--<?php echo $contact_config['type']; ?>">
            <a target="_blank" href="<?php echo es_get_contact_link( $contact_config, $entity ); ?>"><?php echo $contact_config['label']; ?></a>
        </div>
	<?php endif; ?>
	<?php if ( es_is_entity_contact_btn_enabled( 'agency' ) && $entity instanceof Es_Emailed_Entity && $entity->get_email() ) : ?>
        <a href="#" class="es-btn es-btn--secondary es-btn--bordered es-btn--contact js-es-btn--contact" data-id="<?php echo $entity->get_id(); ?>">
            <?php echo _x( 'Contact', 'contact agent|agency', 'es' ); ?>
        </a>
    <?php endif; ?>
</div>