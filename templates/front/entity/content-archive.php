<?php

$classes = array( 'es-wishlist-link' );
$entity = es_get_entity_by_id( get_the_ID() );
$entity_name = $entity::get_entity_name();
$plural_entity_name = es_get_entity_plural_name( $entity_name );
$wishlist = es_get_wishlist_instance( $entity_name );
$entity_control_config = empty( $entity_control_config ) ? array() : $entity_control_config;
$entity_control_config = es_parse_args( $entity_control_config, array(
	'show_sharing' => false,
	'is_full' => false,
    'context' => 'entity-image'
) );

if ( $wishlist->has( get_the_ID() ) ) {
	$classes[] = 'es-wishlist-link--active';
}

if ( empty( $ignore_wrapper ) ) : ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php endif; ?>

	<div class="es-entity es-<?php echo $entity_name; ?> es-<?php echo $entity_name; ?>--<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>">
        <div class="es-entity__inner">

            <?php if ( function_exists( "es_get_the_{$entity_name}_avatar" ) ) : ?>
                <div class="es-entity__image es-<?php echo $entity_name; ?>__image">
                    <a href="<?php echo es_get_the_permalink(); ?>">
                        <img src="<?php echo call_user_func( "es_get_the_{$entity_name}_avatar_url" ); ?>" alt="<?php esc_attr_e( strip_tags( get_the_title() ) ); ?>"/>
                    </a>
	                <?php do_action( 'es_entity_control', array(
                        'show_sharing' => false,
                        'context' => 'entity-image',
                        'is_full' => false
                    ) ); ?>
                </div>
            <?php endif; ?>

            <?php include es_locate_template( "front/{$entity_name}/content-archive-info.php" ); ?>
        </div>
	</div>

<?php if ( empty( $ignore_wrapper ) ) : ?>
	</div>
<?php endif; ?>