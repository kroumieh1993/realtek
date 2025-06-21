<?php

/**
 * Class Es_Agencies_Archive_Page.
 */
class Es_Agencies_Archive_Page extends Es_Entities_Archive_Page {

	/**
	 * @return void
	 */
	public static function init() {
	    global $pagenow;
		parent::init();

		if ( is_admin() && 'edit.php' == $pagenow && 'agency' == filter_input( INPUT_GET, 'post_type' ) ) {
			// Add bulk actions container.
			add_action( 'network_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'user_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
		}

		add_filter( 'views_edit-agency', array( __CLASS__, 'render_agencies_filter' ) );
	}

	/**
	 * @param array $columns
	 *
	 * @return array|mixed
	 */
	public static function add_table_columns( $columns ) {
		// Unset unused columns.
		unset( $columns['author'], $columns['date'], $columns['title'], $columns['comments'] );

		// Add post ID column.
		$columns = es_push_array_pos( array( 'post_id' => __( 'ID', 'es' ) ),  $columns, 1 );
		$columns = es_push_array_pos( array( 'thumbnail-avatar' => __( 'Photo', 'es' ) ),  $columns, 2 );
		$columns = es_push_array_pos( array( 'name' => __( 'Name', 'es' ) ),  $columns, 3 );

		$columns = es_push_array_pos( array( 'active' => __( 'Active', 'es' ) ),  $columns, 9 );
		$columns = es_push_array_pos( array( 'properties_qty' => __( 'Properties Qty', 'es' ) ),  $columns, 10 );
		$columns = es_push_array_pos( array( 'rating' => __( 'Rating', 'es' ) ),  $columns, 11 );
		$columns = es_push_array_pos( array( 'reviews' => __( 'Reviews', 'es' ) ),  $columns, 12 );
		$columns = es_push_array_pos( array( 'status' => __( 'Status', 'es' ) ),  $columns, 13 );

		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'actions' => '<span class="es-icon es-icon_settings"></span>' ), $columns, 100 );

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
		$agency = es_get_agency( $post_id );

		switch ( $column ) {
			case 'thumbnail-avatar':
				echo es_get_the_agency_avatar();
				break;

			case 'email':
			case 'rating':
				echo $agency->{$column};
				break;

			case 'properties_qty':
					echo "<a href='" . es_get_the_permalink( $post_id ) . "#listings' target='_blank'>" . $agency->get_property_qty() . "</a>";
				break;

			case 'reviews':
				$count_comments = wp_count_comments( $agency->get_id() );
				echo "<a href='" . get_permalink( $agency->get_id() ) . "#reviews' target='_blank'>{$count_comments->all}</a>";
				break;

			case 'active':
				$term = get_term_by( 'name', 'Active', 'es_status' );

				if ( $term && ! is_wp_error( $term ) ) {
					echo "<a href='" . add_query_arg( 'es_status', array( $term->term_id ), es_get_the_permalink( $post_id ) ) . "#listings' target='_blank'>" . $agency->get_property_qty() . "</a>";
				}
				break;

			case 'status':
				echo $agency->get_status();
				break;

			case 'name':
				echo "<a href='" . get_edit_post_link( $post_id ) . "'>" . get_the_title() . "</a>";
				break;
		}

		if ( 'actions' == $column ) :
			$post = get_post( $post_id );
			$title            = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post    = current_user_can( 'edit_post', $post->ID ); ?>

            <div class="es-actions">
                <a href='#' class='es-more js-es-more'><span class='es-icon es-icon_more'></a>
                <div class="es-actions__dropdown">
                    <ul>
						<?php if ( is_post_type_viewable( $post_type_object ) ) {
							if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
								if ( $can_edit_post ) {
									$preview_link    = get_preview_post_link( $post );
									printf(
										'<li><a href="%s" target="_blank" rel="bookmark" aria-label="%s">%s</a></li>',
										esc_url( $preview_link ),
										/* translators: %s: Post title. */
										esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ),
										__( 'Preview' )
									);
								}
							} elseif ( 'trash' != $post->post_status ) {
								printf(
									'<li><a href="%s" target="_blank" rel="bookmark" aria-label="%s">%s</a></li>',
									get_permalink( $post_id ),
									/* translators: %s: Post title. */
									esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
									__( 'View' )
								);
							}
						} ?>
						<?php if ( $can_edit_post ) : ?>
                            <li>
								<?php printf( '<a href="%s" aria-label="%s">%s</a>',
									get_edit_post_link( $post_id ),
									esc_attr( sprintf( __( 'Edit %s agency', 'es' ), $title ) ),
									__( 'Edit agency', 'es' )
								); ?>
                            </li>
						<?php endif; ?>
	                    <?php if ( $post->post_status == 'publish' ) : ?>
                            <li><a href="<?php echo es_get_action_post_link( $post_id, 'draft' ); ?>"><?php _e( 'Deactivate', 'es' ); ?></a></li>
	                    <?php else : ?>
                            <li><a href="<?php echo es_get_action_post_link( $post_id, 'publish' ); ?>"><?php _e( 'Activate', 'es' ); ?></a></li>
	                    <?php endif; ?>
	                    <?php if ( current_user_can( 'delete_post', $post_id ) ) : ?>
                            <li>
                                <a href="<?php echo get_delete_post_link( $post_id, '', true ); ?>">
				                    <?php _e( 'Delete' ); ?>
                                </a>
                            </li>
	                    <?php endif; ?>
                    </ul>
                </div>
            </div>

		<?php endif;
	}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {
		return 'agency';
	}

	/**
	 * @return mixed
	 */
	public static function get_sort_options() {
		return ests_values( 'agencies_sorting_options' );
	}

	/**
	 * @return string
	 */
	public static function get_default_sort() {
		return 'newest';
	}

	/**
	 * @param $query
	 */
	public static function filter_query( $query ) {
		if ( ! $query->is_main_query() ) return;

		$sort = filter_input( INPUT_GET, 'sort' );
		$sort = $sort ? $sort : 'newest';

		if ( $sort ) {
			switch ( $sort ) {
				case 'newest':
					$query->set( 'orderby', 'date' );
					$query->set( 'order', 'DESC' );
					break;

				case 'highest_rating':
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'es_agent_rating' );
					$query->set( 'order', 'DESC' );
					break;

				case 'reviews':
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'es_agent_reviews_count' );
					$query->set( 'order', 'DESC' );
					break;

				default:
			}
		}

		if ( ! empty( $_GET['entities_filter'] ) ) {
			$filter = $_GET['entities_filter'];

			if ( ! empty( $filter['s'] ) ) {
				$meta_query['keywords'] = array(
					'key' => 'es_agency_keywords',
					'value' => $filter['s'],
					'compare' => 'LIKE'
				);
			}
		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', apply_filters( 'es_admin_agencies_meta_query', $meta_query, $query ) );
		}
	}

	/**
	 * Add properties bulk actions container.
	 *
	 * @return void
	 */
	public static function add_bulk_actions() {
		es_load_template( 'admin/agencies-archive/bulk-actions.php' );
	}

	/**
	 * Render header of properties page.
	 *
	 * @param $views
	 *
	 * @return string
	 */
	public static function render_agencies_filter( $views ) {
		$f = es_framework_instance();
		$f->load_assets();
		es_load_template( 'admin/agencies-archive/header.php' );
		es_load_template( 'admin/agencies-archive/filter.php' );

		return $views;
	}

	public static function get_entity_name() {
		return 'agency';
	}
}

Es_Agencies_Archive_Page::init();
