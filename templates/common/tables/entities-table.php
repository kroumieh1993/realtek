<?php

/**
 * @var Es_Entity $empty_entity
 * @var Es_Entities_Table $instance
 * @var WP_Query $entities_query
 */

if ( $entities_query->have_posts() ) :
	if ( $fields = $instance::get_table_columns_fields() ) : ?>
    <div class="es-table-wrap">
		<table class="js-es-table es-table es-table--<?php echo $instance::get_entity_type(); ?> content-font">
			<thead>
				<tr>
					<?php foreach ( $fields as $field ) : ?>
						<th class="es-column es-column--<?php echo $field; ?>">
                            <?php if ( '_manage-checkbox' == $field ) : ?>
                                <?php es_framework_field_render( 'post_id', array(
                                    'type' => 'checkbox',
                                    'attributes' => array(
                                        'class' => 'js-es-table-check-all'
                                    ),
                                ) ); ?>
							<?php elseif ( '_manage-buttons' == $field ) : ?>
								<span class="es-icon es-icon_settings"></span>
							<?php else : ?>

                            <?php echo apply_filters(
									sprintf( 'es_%s_table_column_label', $instance::get_entity_type() ), $empty_entity::get_field_label( $field ), $field, $instance ); ?>
							<?php endif; ?>
                        </th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<?php if ( $entities_query->have_posts() ) : ?>
                <tbody>
                    <?php while ( $entities_query->have_posts() ) : $entities_query->the_post(); ?>
                        <tr class="es-<?php echo $instance::get_entity_type(); ?>-row status-<?php echo get_post_status( get_the_ID() ); ?>">
				            <?php foreach ( $fields as $field ) : ?>
                                <td class="es-column es-column--<?php echo $field; ?>">
                                    <?php if ( '_manage-checkbox' == $field ) : ?>
                                        <?php es_framework_field_render( 'post_id', array(
                                            'type' => 'checkbox',
                                            'attributes' => array(
                                                'value' => get_the_ID(),
                                            ),
                                        ) ); ?>
                                    <?php elseif ( '_manage-buttons' == $field ) : ?>
                                        <div class="es-actions">
                                            <a href="#" class="es-more js-es-more"><span class="es-icon es-icon_more"></span></a>
                                            <div class="es-actions__dropdown">
                                                <ul>
                                                    <?php if ( get_post_status( get_the_ID() ) == 'publish' ) : ?>
                                                        <li><a target="_blank" href="<?php the_permalink(); ?>"><?php _e( 'View', 'es' ); ?></a></li>
                                                    <?php else : ?>
                                                        <li><a target="_blank" href="<?php echo get_preview_post_link( get_the_ID() ); ?>"><?php _e( 'Preview', 'es' ); ?></a></li>
                                                    <?php endif; ?>

                                                    <?php if ( current_user_can( 'edit_post', get_the_ID() ) ) : ?>
                                                        <li><a href="<?php echo add_query_arg( array(
                                                                'screen' => 'edit-property',
                                                                'property_id' => get_the_ID(),
                                                            ) ); ?>"><?php _e( 'Edit', 'es' ); ?></a></li>
                                                    <?php endif; ?>

                                                    <?php if ( current_user_can( 'edit_post', get_the_ID() ) && current_user_can( 'publish_es_properties' ) ) : ?>
                                                        <?php if ( get_post_status( get_the_ID() ) == 'publish' ) : ?>
                                                            <li><a href="<?php echo es_get_action_post_link( get_the_ID(), 'draft' ); ?>"><?php _e( 'Unpublish', 'es' ); ?></a></li>
                                                        <?php else : ?>
                                                            <?php if ( es_user_can_publish_listings() && ! ests( 'manual_listing_approve' ) ) : ?>
                                                                <li><a href="<?php echo es_get_action_post_link( get_the_ID(), 'publish' ); ?>"><?php _e( 'Publish', 'es' ); ?></a></li>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <li><a href="<?php echo es_get_action_post_link( get_the_ID(), 'copy' ); ?>"><?php _e( 'Copy', 'es' ); ?></a></li>

                                                    <?php if ( current_user_can( 'delete_post', get_the_ID() ) ) : ?>
                                                        <li><a href="" data-entity-id="<?php the_ID(); ?>" class="js-es-delete-<?php echo $empty_entity::get_entity_name(); ?>"><?php _e( 'Delete', 'es' ); ?></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <?php echo apply_filters( sprintf( 'es_%s_table_column_value', $instance::get_entity_type() ), es_get_the_formatted_field( $field ), $field, $instance ); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
			<?php endif; ?>
		</table>
    </div>
	<?php es_the_pagination( $entities_query ); wp_reset_postdata();
endif;
else : ?>
	<div class="js-es-no-posts">
		<p class="es-subtitle"><?php _e( 'You don’t have any listing yet', 'es' ); ?></p>
        <?php if ( ests( 'is_frontend_management_enabled' ) && ! empty( $config['show_add_new'] ) && ( $url = es_get_add_new_property_url() ) ) : ?>
            <p><?php _e( 'Fill the form to add new home now.', 'es' ); ?></p>
            <a class="es-btn es-btn--secondary" href="<?php echo $url; ?>">
                <span class="es-icon es-icon_plus"></span>
                <?php _e( 'Add new home', 'es' ); ?></a>
        <?php endif; ?>
	</div>
<?php endif;
