<?php

/**
 * Class Es_Agents_Archive_Page.
 */
class Es_Agents_Archive_Page extends Es_Entities_Archive_Page {

	/**
	 * @return void
	 */
    public static function init() {
	    global $pagenow;

	    parent::init();

	    if ( is_admin() && 'edit.php' == $pagenow && 'agent' == filter_input( INPUT_GET, 'post_type' ) ) {
		    // Add bulk actions container.
		    add_action( 'network_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
		    add_action( 'user_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
		    add_action( 'admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
	    }

	    add_filter( 'views_edit-agent', array( __CLASS__, 'render_agents_filter' ) );
	    add_action( 'init', array( __CLASS__, 'entities_actions' ) );
    }

	public static function entities_actions() {
        parent::entities_actions();


	}

	/**
	 * @param array $columns
	 *
	 * @return array|mixed
	 */
	public static function add_table_columns( $columns ) {
		// Unset unused columns.
		unset( $columns['author'], $columns['date'], $columns['comments'] );

		$columns['title'] = __( 'Name', 'es' );

		// Add post ID column.
		$columns = es_push_array_pos( array( 'post_id' => __( 'ID', 'es' ) ),  $columns, 1 );
		$columns = es_push_array_pos( array( 'thumbnail-avatar' => __( 'Photo', 'es' ) ),  $columns, 2 );
		$columns = es_push_array_pos( array( 'agency_id' => __( 'Agency', 'es' ) ),  $columns, 4 );
		$columns = es_push_array_pos( array( 'user_email' => __( 'Email', 'es' ) ),  $columns, 5 );
		$columns = es_push_array_pos( array( 'phone' => __( 'Phone', 'es' ) ),  $columns, 6 );

		if ( ests( 'is_subscriptions_enabled' ) ) {
			$columns = es_push_array_pos( array( 'plan' => __( 'Plan', 'es' ) ),  $columns, 7 );
			$columns = es_push_array_pos( array( 'ends' => __( 'Ends', 'es' ) ),  $columns, 8 );
		}

		if ( term_exists( 'Active', 'es_status' ) ) {
			$columns = es_push_array_pos( array( 'active' => __( 'Active', 'es' ) ),  $columns, 9 );
        }

        $columns = es_push_array_pos( array( 'properties_qty' => __( 'Properties Qty', 'es' ) ), $columns, 10 );
		$columns = es_push_array_pos( array( 'rating' => __( 'Rating', 'es' ) ),  $columns, 11 );
		$columns = es_push_array_pos( array( 'reviews' => __( 'Reviews', 'es' ) ),  $columns, 12 );
		$columns = es_push_array_pos( array( 'status' => __( 'User status', 'es' ) ),  $columns, 13 );

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
		$agent = es_get_agent( $post_id );

		if ( $agent->user_id ) {
			$subscription = es_get_user_subscription( $agent->user_id );
		}

		switch ( $column ) {
			case 'thumbnail-avatar':
				echo es_get_the_agent_avatar();
				break;

			case 'user_email':
                echo $agent->get_email();
                break;

            case 'plan':
	            if ( ! empty( $subscription ) ) {
		            echo $subscription->get_title();
	            }
                break;

			case 'ends':
				if ( ! empty( $subscription ) ) {
                    if ( $order = $subscription->get_order() ) {
                        if ( $order->end_date ) {
	                        echo date( 'Y-m-d H:i:s', $order->end_date );
                        }
                    }
				}
				break;

			case 'properties_qty':
				echo "<a href='" . es_get_the_permalink( $post_id ) . "#listings' target='_blank'>" . $agent->get_property_qty() . "</a>";
				break;

			case 'rating':
				echo $agent->{$column};
				break;

            case 'reviews':
                $count_comments = wp_count_comments( $agent->get_id() );
				echo "<a href='" . get_permalink( $agent->get_id() ) . "#reviews' target='_blank'>{$count_comments->all}</a>";
				break;

			case 'agency_id':
				if ( $agent->has_agency() ) {
					echo "<a href='" . get_edit_post_link( $agent->agency_id ) . "'>" . get_the_title( $agent->agency_id ) . "</a>";
				}
				break;

            case 'status':
				echo $agent->get_user_status();
				break;

            case 'phone':
                $contacts = $agent->contacts;
                if ( ! empty( $contacts ) ) {
                    foreach ( $contacts as $contact ) {
                        if ( ! empty( $contact['phone']['tel'] ) ) {
	                        echo $contact['phone']['tel'];
	                        break;
                        }
                    }
                }
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
									esc_attr( sprintf( __( 'Edit %s profile', 'es' ), $title ) ),
									__( 'Edit profile', 'es' )
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
		return 'agent';
	}

	/**
	 * @return mixed
	 */
	public static function get_sort_options() {
		return ests_values( 'agents_sorting_options' );
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

			if ( ! empty( $filter['agency_id'] ) ) {
			    $meta_query[] = array(
				    'value' => intval( $filter['agency_id'] ),
				    'key' => 'es_agent_agency_id',
				    'type' => 'NUMERIC',
			    );
			}

			if ( ! empty( $filter['s'] ) ) {
				$meta_query['keywords'] = array(
					'key' => 'es_agent_keywords',
					'value' => $filter['s'],
					'compare' => 'LIKE'
				);
			}
		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', apply_filters( 'es_admin_agents_meta_query', $meta_query, $query ) );
		}
	}

	/**
	 * Add properties bulk actions container.
	 *
	 * @return void
	 */
	public static function add_bulk_actions() {
		es_load_template( 'admin/agents-archive/bulk-actions.php' );
	}

	/**
	 * Render header of properties page.
	 *
	 * @param $views
	 *
	 * @return string
	 */
	public static function render_agents_filter( $views ) {
		$f = es_framework_instance();
		$f->load_assets();
		es_load_template( 'admin/agents-archive/header.php' );
		$entity = es_get_entity( static::get_post_type_name() );
		if ( $entity::count() ) {
			es_load_template( 'admin/agents-archive/filter.php' );
		} else {
			es_load_template( 'admin/partials/empty-archive.php', array(
                'entity_name' => 'agent',
                'post_type' => static::get_post_type_name(),
            ) );
		}

		return $views;
	}

	public static function get_entity_name() {
		return 'agent';
	}
}

Es_Agents_Archive_Page::init();
