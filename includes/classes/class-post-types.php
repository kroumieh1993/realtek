<?php

/**
 * Class Es_Post_Types.
 *
 * Register custom plugin post types.
 */
class Es_Post_Types {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( 'Es_Post_Types', 'register_post_types' ) );
	}

	/**
	 * Register plugin custom post types.
	 *
	 * @return void
	 */
	public static function register_post_types() {
		if ( ests( 'is_subscriptions_enabled' ) ) {
			$args = array(
				'label' => __( 'Order', 'es' ),
				'labels' => array(
					'name' => __( 'Order', 'es' ),
				),
				'public' => false,
				'supports' => array( 'title', 'author' ),
				'show_ui' => true,
				'show_in_menu' => false,
			);

			register_post_type( 'es_order', $args );
		}

		$args = array(
			'label' => __( ests( 'post_type_name' ), 'es' ),
			'labels' => array(
				'name' => __( ests( 'post_type_name' ), 'es' ),
				'singular_name'      => _x( 'Property', 'post type singular name', 'es' ),
				'menu_name'          => _x( 'Properties', 'admin menu', 'es' ),
				'name_admin_bar'     => _x( 'Property', 'add new on admin bar', 'es' ),
				'add_new'            => _x( 'Add New', 'Customize Property', 'es' ),
				'add_new_item'       => __( 'Add a New Property', 'es' ),
				'new_item'           => __( 'New Property', 'es' ),
				'edit_item'          => __( 'Edit Property', 'es' ),
				'view_item'          => __( 'View Property', 'es' ),
				'all_items'          => __( 'All Properties', 'es' ),
				'search_items'       => __( 'Search Properties', 'es' ),
				'not_found'          => __( 'No properties found.', 'es' ),
				'not_found_in_trash' => __( 'No properties found in Trash.', 'es' ),
			),
			'public' => true,
			'show_in_menu' => false,
			'has_archive' => ests( 'is_properties_archive_enabled' ),
			'supports' => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'elementor' ),
			'rewrite' => array(
				'slug' => ests( 'property_slug' ),
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			),
            'map_meta_cap' => true,
            'capability_type'     => array( 'es_property', 'es_properties' ),
            'capabilities' => array(
                'create_posts' => 'create_es_properties',
            ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'properties',
		);

		register_post_type( 'properties', $args );

        $args = array(
            'public' => false,
            'show_in_menu' => false,
            'has_archive' => false,
            'supports' => array( 'title', 'author' ),
            'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
            'rest_base' => 'saved_search',
        );

        register_post_type( 'saved_search', $args );

		if ( ests( 'is_agents_enabled' ) ) {
			register_post_type( 'agent', array(
				'label' => __( 'Agents', 'es' ),
				'labels' => array(
					'name'               => _x( 'Agents', 'post type general name', 'es' ),
					'singular_name'      => _x( 'Agent', 'post type singular name', 'es' ),
					'menu_name'          => _x( 'Agents', 'admin menu', 'es' ),
					'name_admin_bar'     => _x( 'Agent', 'add new on admin bar', 'es' ),
					'add_new'            => _x( 'Add New', 'Customize Agent', 'es' ),
					'add_new_item'       => __( 'Add a New Agent', 'es' ),
					'new_item'           => __( 'New Agent', 'es' ),
					'edit_item'          => __( 'Edit Agent', 'es' ),
					'view_item'          => __( 'View Agent', 'es' ),
					'all_items'          => __( 'All Agents', 'es' ),
					'search_items'       => __( 'Search Agents', 'es' ),
					'not_found'          => __( 'No agents found.', 'es' ),
					'not_found_in_trash' => __( 'No agents found in Trash.', 'es' ),
				),
				'public' => true,
				'show_in_menu' => false,
				'has_archive' => true,
				'map_meta_cap' => true,
				'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'elementor' ),
				'capability_type'     => array( 'agent', 'agents' ),
				'capabilities' => array(
					'create_posts' => 'create_agents',
				),
				'rewrite' => array(
					'slug' => ests( 'agent_slug' ),
					'with_front' => false,
					'pages'      => true,
					'feeds'      => true,
					'ep_mask'    => EP_PERMALINK,
				),
			) );
		}

		register_post_type( 'request', array(
			'label' => __( 'Requests', 'es' ),
			'labels' => array(
				'name' => __( 'Requests', 'es' ),
				'singular_name'      => _x( 'Request', 'post type singular name', 'es' ),
			),
			'show_in_menu' => false,
			'public' => false,
			'show_ui' => true,
			'supports' => array( 'title', 'excerpt' ),
			'map_meta_cap' => true,
			'capability_type'     => array( 'es_request', 'es_requests' ),
			'capabilities' => array(
				'create_posts' => 'create_es_requests',
			),
		) );

		if ( ests( 'is_agencies_enabled' ) ) {
			register_post_type( 'agency', array(
				'label' => __( 'Agencies', 'es' ),
				'labels' => array(
					'name'               => _x( 'Agencies', 'post type general name', 'es' ),
					'singular_name'      => _x( 'Agency', 'post type singular name', 'es' ),
					'menu_name'          => _x( 'Agencies', 'admin menu', 'es' ),
					'name_admin_bar'     => _x( 'Agency', 'add new on admin bar', 'es' ),
					'add_new'            => _x( 'Add New', 'Customize Agency', 'es' ),
					'add_new_item'       => __( 'Add a New Agency', 'es' ),
					'new_item'           => __( 'New Agency', 'es' ),
					'edit_item'          => __( 'Edit Agency', 'es' ),
					'view_item'          => __( 'View Agency', 'es' ),
					'all_items'          => __( 'All Agencies', 'es' ),
					'search_items'       => __( 'Search Agencies', 'es' ),
					'not_found'          => __( 'No agencies found.', 'es' ),
					'not_found_in_trash' => __( 'No agencies found in Trash.', 'es' ),
				),
				'public' => true,
				'show_in_menu' => false,
				'has_archive' => true,
				'map_meta_cap' => true,
				'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'elementor' ),
				'capability_type'     => array( 'agency', 'agencies' ),
				'capabilities' => array(
					'create_posts' => 'create_agencies',
				),
				'rewrite' => array(
					'slug' => ests( 'agency_slug' ),
					'with_front' => false,
					'pages'      => true,
					'feeds'      => true,
					'ep_mask'    => EP_PERMALINK,
				),
			) );
		}
	}
}

Es_Post_Types::init();
