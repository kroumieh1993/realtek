<?php

$post_id = get_the_ID();
$property = es_get_property( $post_id );

if ( $property->agent_id ) {
    $agents = is_string( $property->agent_id ) ? array( $property->agent_id ) : $property->agent_id;
	$query = new WP_Query( array( 'post__in' => $agents, 'post_type' => 'agent', 'orderby' => 'post__in' ) );
	$context = ! empty( $context ) ? $context : 'basic';
	$context_class = "es-request-agents--{$context}";

	if ( $context == 'basic' )
		add_action( 'es_stats_counter', 'es_agent_preferred_contact_render' );

	if ( $query->have_posts() ) : $i = 0; ?>
        <div class="es-request-agents <?php echo $context_class; ?>">
			<?php while ( $query->have_posts() ) : $query->the_post(); $_class = ! $i ? ' es-agent-item--active' : ''; ?>
                <div class="es-agent-item js-es-agent-item<?php echo $_class; ?>">
					<?php es_load_template( 'front/entity/content-archive.php' ); ?>
                    <input type="checkbox" <?php checked( $i, 0 ); ?> class="js-es-agent-checkbox" name="agent[]" value="<?php the_ID(); ?>"/>
                </div>
				<?php $i++; endwhile; wp_reset_postdata(); ?>
        </div>
	<?php endif;

	if ( $context == 'basic' )
		remove_action( 'es_stats_counter', 'es_agent_preferred_contact_render' );
}