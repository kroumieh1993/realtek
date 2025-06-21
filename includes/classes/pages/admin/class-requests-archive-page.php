<?php

/**
 * Class Es_Requests_Archive_Page.
 */
class Es_Requests_Archive_Page extends Es_Entities_Archive_Page {

	/**
	 * Initialize requests archive page.
	 *
	 * @return void
	 */
	public static function init() {
		global $pagenow;

		parent::init();

		if ( is_admin() && 'edit.php' == $pagenow && 'request' == filter_input( INPUT_GET, 'post_type' ) ) {
			// Add bulk actions container.
			add_action( 'network_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'user_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
		}

		add_filter( 'views_edit-request', array( __CLASS__, 'render_header' ) );
        add_action( 'wp_ajax_es_request_note_popup', array( __CLASS__, 'ajax_note_popup' ) );
	}

	/**
	 * Add properties bulk actions container.
	 *
	 * @return void
	 */
	public static function add_bulk_actions() {
		es_load_template( 'admin/requests-archive/bulk-actions.php' );
	}

	/**
	 * @return void
	 */
    public static function ajax_note_popup() {
        if ( check_ajax_referer( 'es_request_note_popup', 'nonce' ) ) {
            $post_id = es_get( 'post_id', 'intval' );
            $redirect_url = es_get( 'redirect_url' );

            if ( es_is_request( $post_id ) && current_user_can(  'edit_post', $post_id ) ) : ?>
                <form action="" method="post">
                    <?php wp_nonce_field( 'es_save_request_nonce', 'es_save_request_nonce' ); ?>
                    <input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>"/>
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
                    <p class='es-center es-popup-text'><?php _e( 'Add note', 'es' ); ?></p>
                    <?php $field_config = array(
                        'attributes' => array( 'rows' => 6 ),
                        'type' => 'textarea',
                        'label' => __( 'Note text', 'es' )
                    );

                    es_framework_field_render( 'note', $field_config ); ?>
                    <div class='es-popup__buttons es-center'>
                        <a href='#' class='js-es-popup__close es-btn es-btn es-btn--link'><?php _e( 'Cancel', 'es' ); ?></a>
                        <input type='submit' class='es-btn es-btn--primary es-btn--large' value='<?php _e( 'Add note', 'es' ); ?>'/>
                     </div>
                </form>
            <?php else : ?>
                <p class='es-center es-popup-text'><?php _e( 'You have no permissions to do action', 'es' ); ?></p>';
                <div class='es-popup__buttons es-center'><button class='js-es-popup__close es-btn es-btn es-btn--link'><?php _e( 'Close', 'es' ); ?></button></div>";
            <?php endif;

            wp_die( false );
        }
    }

	/**
	 * @param $views
	 *
	 * @return mixed
	 */
	public static function render_header( $views ) {
		es_load_template( 'admin/requests-archive/header.php' );

		$f = es_framework_instance();
		$f->load_assets();

		$entity = es_get_entity( static::get_entity_name() );
		if ( $entity::count() ) {
			es_load_template( 'admin/requests-archive/filter.php' );
		} else {
			es_load_template( 'admin/partials/empty-archive.php', array(
				'entity_name' => static::get_entity_name(),
				'post_type' => static::get_post_type_name(),
                'can_add_new' => false
			) );
		}

		return $views;
	}

	/**
	 * @param array $columns
	 *
	 * @return array|mixed
	 */
	public static function add_table_columns( $columns ) {
		// Unset unused columns.
		unset( $columns['author'], $columns['date'], $columns['title'] );

		$columns['date'] = __( 'Date', 'es' );
		$columns['title'] = __( 'Name', 'es' );
		$columns['contacts'] = __( 'Contacts', 'es' );
		$columns['recipient'] = __( 'Recipient', 'es' );
		$columns['property'] = __( 'Property', 'es' );
		$columns['note'] = __( 'Notes', 'es' );
		$columns['message'] = __( 'Message', 'es' );
		$columns['actions'] = '<span class="es-icon es-icon_settings"></span>';

        unset( $columns['post_id'] );

		return $columns;
	}

	/**
	 * Render table column value.
	 *
	 * @param $column
	 * @param $post_id
	 */
	public static function add_table_columns_values( $column, $post_id ) {
		parent::add_table_columns_values( $column, $post_id );

		$request = es_get_request( $post_id );

		if ( 'message' == $column ) {
			the_excerpt();
		}

		if ( 'note' == $column ) {
			echo $request->note ? $request->note . '<br>' : '';
            echo "<a href='#' data-id='{$post_id}' class='js-es-add-note-popup'>" . __( 'Add note', 'es' ) . "</a>";
		}

		if ( 'contacts' == $column ) {
			if ( $request->email ) {
				echo "<a href='mailto:{$request->email}'>{$request->email}</a><br>";
				if ( $tel = es_get_formatted_tel( $request->tel ) ) {
					echo $tel;
				}
			}
		}

		if ( 'recipient' == $column ) {
            if ( es_is_agent( $request->post_id ) || es_is_agency( $request->post_id ) ) {
                $entity = es_get_entity_by_id( $request->post_id );
                if ( $entity instanceof Es_Entity ) {
	                $title = ucfirst( sprintf( __( $entity::get_entity_name() . ': %s', 'es' ),
		                get_the_title( $entity->get_id() ) ) );
	                printf( "<a href='%s' target='_blank'>%s</a>", get_permalink( $entity->get_id() ), $title );
                }
            }
            else if ( $request->recipient_custom_emails ) {
                printf( __( 'Custom emails: %s', 'es' ), $request->recipient_custom_emails );
            }

            if ( es_is_property( $request->post_id ) ) {
                if ( ! empty( $request->recipient_entity_id ) ) {
	                $entity = es_get_entity_by_id( $request->recipient_entity_id );
                    if ( $entity instanceof Es_Entity ) {
	                    $title = ucfirst( sprintf( __( $entity::get_entity_name() . ': %s', 'es' ),
		                    get_the_title( $entity->get_id() ) ) );
	                    printf( "<a href='%s' target='_blank'>%s</a>", get_permalink( $entity->get_id() ), $title );
                    }
                }
            }

            if ( ! empty( $request->recipient_user_id ) ) {
                global $wp_roles;
                $user = get_user_by( 'id', $request->recipient_user_id );

                if ( ! empty( $user->roles ) ) {
	                $role = array_shift( $user->roles );
                    $role = $wp_roles->roles[ $role ]['name'];
                } else {
                    $role = __( 'User', 'es' );
                }

                if ( $user ) {
	                printf( __( $role . ': %s (%s)', 'es' ), $user->user_nicename, $user->user_email );
                } else {
                    echo __( 'Empty', 'es' );
                }
            }
		}

		if ( 'property' == $column && es_is_property( $request->post_id ) ) {
            $title = get_the_title( $request->post_id );
            $title = $title ? $title : $request->post_id;
			echo "<a href='" . get_permalink( $request->post_id ) . "' target='_blank'>{$title}</a>";
		}

		if ( 'actions' == $column ) : ?>
			<div class="es-actions">
				<a href='#' class='es-more js-es-more'><span class='es-icon es-icon_more'></a>
				<div class="es-actions__dropdown">
					<ul>
						<?php if ( current_user_can( 'delete_post', $post_id ) ) : ?>
                            <li>
                                <a href="<?php echo get_delete_post_link( $post_id, '', true ); ?>">
									<?php echo _x( 'Delete', 'delete request', 'es' ); ?>
                                </a>
                            </li>
						<?php endif; ?>
                        <?php if ( get_post_status( get_the_ID() ) !== 'draft' && current_user_can( 'draft_es_request', get_the_ID() ) ) : $args['action'] = 'draft'; ?>
                            <li>
                                <a href="<?php echo es_get_action_post_link( $post_id, 'draft' ); ?>"><?php echo _x( 'Archive', 'delete request', 'es' ); ?></a>
                            </li>
                        <?php endif; ?>

						<?php if ( current_user_can( 'edit_post', get_the_ID() ) && ! $request->is_viewed ) : ?>
                            <li>
                                <a href="<?php echo es_get_action_post_link( $post_id, 'viewed' ); ?>"><?php echo _x( 'Mark as read', 'delete request', 'es' ); ?></a>
                            </li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		<?php endif;
	}

	public static function get_post_type_name() {
		return 'request';
	}

	public static function get_sort_options() {
		return null;
	}

	public static function get_default_sort() {
		return null;
	}

	/**
	 * @param $query
	 */
	public static function filter_query( $query ) {
		if ( ! $query->is_main_query() ) return;

//        $query->set( 'post_parent', 0 );

		if ( ! empty( $_GET['entities_filter'] ) ) {
			$filter = es_clean( $_GET['entities_filter'] );

			if ( ! empty( $filter['s'] ) ) {
				$meta_query['keywords'] = array(
					'key' => 'es_request_keywords',
					'value' => $filter['s'],
					'compare' => 'LIKE'
				);
			}
		}

        if ( empty( $_GET['post_status'] ) ) {
            $query->set( 'post_status', 'publish' );
        }

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', apply_filters( 'es_admin_properties_meta_query', $meta_query, $query ) );
		}
	}

	public static function get_entity_name() {
		return 'request';
	}
}
Es_Requests_Archive_Page::init();
