<?php

/**
 * @var $current_tab string
 */

$request_id = es_get( 'request_id', 'intval' );

es_current_user_can_or_die( 'read_post', $request_id );

$query = new WP_Query( array(
	'post_type' => 'request',
	'p' => $request_id,
    'post_status' => array( 'draft', 'publish' ),
) );

$request = es_get_request( $request_id );
$request_post = $request->get_wp_entity();

if ( ! $request->is_viewed ) $request->save_field_value( 'is_viewed', 1 );

if ( $query->have_posts() ) :
	add_action( 'es_after_property_content', 'es_property_content_extended' ); ?>

    <div id="single-request" class="es-profile__content es-profile__content--single-request">
        <div class="es-single-request">
            <?php if ( $request->post_id ) : ?>
                <div class="es-single-request__listing">
                    <?php $listings = es_get_shortcode_instance( 'es_my_listing', array(
                        'prop_id' => $request->post_id,
                        'ignore_search' => true,
                        'layout' => 'grid-1',
                        'disable_navbar' => true,
                    ) );
                    echo $listings->get_content(); ?>
                </div>
            <?php endif; ?>
            <?php while ( $query->have_posts() ) : $query->the_post(); $request = es_get_request( get_the_ID() ); ?>
                <div class="es-single-request__content">
                    <div class="es-single-request__content-inner">
                        <div class="es-flex es-flex--align-center es-flex--justify-between es-single-request__header">
                            <span class="es-request-meta"><?php _e( 'Request ID' ); ?>: <?php echo get_the_ID(); ?></span>
                            <?php the_date( '', '<span class="es-request-meta">', '</span>' ); ?>
                        </div>

                        <div class="es-flex es-flex--justify-between">
                            <h3 class="es-single-request__title"><?php the_title(); ?></h3>
                            <div class="es-single-request__contacts">
                                <?php if ( $request->email ) : ?>
                                    <a href="mailto:<?php echo $request->email; ?>" class="es-secondary-color es-leave-border"><?php echo $request->email; ?></a>
                                <?php endif; ?>
                                <?php if ( $tel = es_get_formatted_tel( $request->tel ) ) : ?>
                                    <a href="tel:<?php echo $tel; ?>" class="es-secondary-color es-leave-border"><?php echo $tel; ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="es-single-request__message">
                            <?php the_excerpt(); ?>
                        </div>

                        <form action="" method="post" class="js-es-ajax-form js-es-form-enable-on-change">
                            <?php es_framework_field_render( 'note', array(
                                    'label' => __( 'My note', 'es' ),
                                    'type' => 'textarea',
                                    'value' => esc_textarea( $request->note ),
                            ) ); ?>

                            <input type="hidden" name="request_id" value="<?php the_ID(); ?>"/>
                            <input type="hidden" name="action" value="es_request_save_note"/>
                            <?php wp_nonce_field( 'es_request_save_note' ); ?>

                            <div style="text-align: right">
                                <input class="es-btn es-btn--secondary es-btn--bordered" type="submit" value="<?php _e( 'Add note', 'es' ); ?>"/>
                            </div>
                        </form>
                    </div>
                    <div class="es-single-request__manage-links">
                        <?php $args = array(
	                        'post_ids' => get_the_ID(),
	                        'action' => 'delete',
	                        '_nonce' => wp_create_nonce( 'es_entities_actions' ),
	                        '_redirect' => add_query_arg( 'tab', 'requests', false )
                        );

                        if ( current_user_can( 'delete_post', get_the_ID() ) ) : ?>
                            <a href="<?php echo add_query_arg( $args, false ); ?>" data-confirm-button-icon="es-icon es-icon_trash" data-confirm-button="<?php _e( 'Delete', 'es' ); ?>" data-confirm-message data-confirm-title="<?php _e( 'Delete request?', 'es' ); ?>"
                               class="es-secondary-color"><?php _e( 'Delete request', 'es' ); ?></a>
                        <?php endif; ?>
                        <?php if ( get_post_status( get_the_ID() ) !== 'draft' && current_user_can( 'draft_es_request', get_the_ID() ) ) :
                            $args['action'] = 'draft'; ?>
                            <a href="<?php echo add_query_arg( $args, false ); ?>" data-confirm-button="<?php _e( 'Archive', 'es' ); ?>" data-confirm-message data-confirm-title="<?php _e( 'Archive request?', 'es' ); ?>"
                               class="es-secondary-color"><?php _e( 'Archive', 'es' ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>

	<?php remove_action( 'es_after_property_content', 'es_property_content_extended' );
endif;
