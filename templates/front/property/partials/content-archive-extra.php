<?php $extra_fields = apply_filters( 'es_property_content_extra_fields', array(
	'es_status', 'es_category', 'es_type', 'ID', 'mls_num', 'post_excerpt',
) );

if ( ! empty( $extra_fields ) ) : ?>
	<ul class="es-listing__extra-fields">
		<?php foreach ( $extra_fields as $field ) : $field_info = es_property_get_field_info( $field );
		if ( ! $field_info ) continue; ?>

		<li class="es-extra-field es-extra-field--<?php echo $field; ?>">
			<span><?php echo $field_info['label']; ?>: </span>
			<span><?php es_the_formatted_field( $field ); ?></span>
		</li>

		<?php endforeach; ?>
	</ul>

    <?php if ( current_user_can( 'edit_post', get_the_ID() ) ) : ?>
        <div class="es-listing__manage">
            <a href="<?php echo add_query_arg( array(
	            'screen' => 'edit-property',
                'tab' => 'my-listings',
	            'property_id' => get_the_ID(),
            ), true ); ?>" class="es-secondary-color"><?php _e( 'Edit property', 'es' ); ?></a>
            <a href="<?php the_permalink(); ?>" class="es-btn es-btn--secondary es-btn--bordered">
                <?php _e( 'View', 'es' ); ?>
            </a>
        </div>
    <?php endif; ?>
<?php endif;
