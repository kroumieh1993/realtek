<div class="es-wrap">
    <form class="es-filter">
		<?php do_action( 'es_requests_filter_before' );
		$post_status = es_get( 'post_status' );
		$post_status = $post_status ? $post_status : 'publish' ?>

        <input type="hidden" name="post_type" value="<?php echo Es_Request::get_post_type_name(); ?>"/>

        <div class="es-row" style="align-items: flex-end;">
            <div class="es-col-xl-4 es-col-md-6">
				<?php es_entities_filter_field_render( 's', array(
					'label' => __( 'Requests search', 'es' ),
					'type' => 'text',
					'attributes' => array(
						'placeholder' => __( 'Search by ID, name, message or contact', 'es' ),
					),
					'wrapper_class' => 'es-field--small'
				) ); ?>
            </div>
            <div class="es-col-xl-4 es-col-md-6" style="margin-bottom: 10px;">
                <button type="submit" class="es-btn es-btn--third es-btn--icon es-btn--small">
                    <span class="es-icon es-icon_search"></span>
		            <?php _e( 'Search' ); ?>
                </button>
            </div>
        </div>

		<?php do_action( 'es_requests_filter_after' ); ?>
    </form>

    <div class="es-nav-tab-wrap">
        <ul class="es-nav-tab">
            <li class="es-nav-tab__item<?php es_active_class( $post_status, 'publish', 'es-nav-tab__item--active' ); ?>"><a href="<?php echo add_query_arg( 'post_status', false ); ?>"><?php _e( 'Inbox', 'es' ); ?></a></li>
            <li class="es-nav-tab__item<?php es_active_class( $post_status, 'draft', 'es-nav-tab__item--active' ); ?>"><a href="<?php echo add_query_arg( 'post_status', 'draft' ); ?>"><?php _e( 'Archived', 'es' ); ?></a></li>
        </ul>
    </div>
</div>
