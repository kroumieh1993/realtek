<?php

/**
 * Class Es_Admin_Menu
 */
class Es_Admin_Menu {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( 'Es_Admin_Menu', 'register_admin_pages' ) );
		add_action( 'admin_menu', array( 'Es_Admin_Menu', 'menu_order_count' ), 9999 );
	}

	/**
	 * @return void
	 */
	public static function menu_order_count() {
		global $submenu;

		if ( isset( $submenu['estatik'] ) ) {
			// Remove 'WooCommerce' sub menu item
			unset( $submenu['estatik'][0] );
		}
	}

	/**
	 * Register admin pages.
	 *
	 * @return void
	 */
	public static function register_admin_pages() {
		$is_wl_enabled = ests( 'is_white_label_enabled' );
		$parent = $is_wl_enabled ? 'estatik' : 'es_dashboard';
		$label = $is_wl_enabled ? __( 'Listings' ) : __( 'Estatik', 'es' );
		$function = $is_wl_enabled ? null : array( 'Es_Dashboard_Page', 'render' );

		$request_count = es_get_new_requests_count();

		$menu_pages = array(
			'estatik' => array(
				'args' => array(
					__( 'Estatik', 'es' ),
					$label,
					'manage_options',
					$parent,
					$function,
					! $is_wl_enabled ? ES_PLUGIN_URL . 'admin/images/logo.svg' : 'dashicons-admin-home',
					'20.7'
				),
				'callback' => 'add_menu_page',
			),
			'dashboard' => array(
				'args' => array(
					$parent,
					__( 'Dashboard', 'es' ),
					__( 'Dashboard', 'es' ),
					'manage_options',
					'es_dashboard',
					array( 'Es_Dashboard_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
				'disabled' => ests( 'is_white_label_enabled' )
			),
			'my-listings' => array(
				'args' => array(
					$parent,
					__( 'My listings', 'es' ),
					__( 'My listings', 'es' ),
					'manage_options',
					'edit.php?post_type=properties',
				),
				'callback' => 'add_submenu_page',
			),
            'add-listing' => array(
				'args' => array(
					$parent,
					__( 'Add new property', 'es' ),
					__( 'Add new property', 'es' ),
					'manage_options',
					'post-new.php?post_type=properties',
				),
				'callback' => 'add_submenu_page',
			),
			'data-manager' => array(
				'args' => array(
					$parent,
					__( 'Data manager', 'es' ),
					__( 'Data manager', 'es' ),
					'manage_options',
					'es_data_manager',
					array( 'Es_Data_Manager_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
			),
			'subscriptions' => array(
				'args' => array(
					$parent,
					__( 'Subscriptions', 'es' ),
					__( 'Subscriptions', 'es' ),
					'manage_options',
					'es_subscriptions',
					array( 'Es_Subscriptions_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_subscriptions_enabled' )
			),
			'orders' => array(
				'args' => array(
					$parent,
					__( 'Orders', 'es' ),
					__( 'Orders', 'es' ),
					'manage_options',
					'edit.php?post_type=es_order',
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_subscriptions_enabled' )
			),
			'agents' => array(
				'args' => array(
					$parent,
					__( 'Agents', 'es' ),
					__( 'Agents', 'es' ),
					'manage_options',
					'edit.php?post_type=agent',
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_agents_enabled' ),
			),
			'add-agent' => array(
				'args' => array(
					$parent,
					__( 'Add Agent', 'es' ),
					__( 'Add Agent', 'es' ),
					'manage_options',
					'post-new.php?post_type=agent',
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_agents_enabled' ),
			),
			'agencies' => array(
				'args' => array(
					$parent,
					__( 'Agencies', 'es' ),
					__( 'Agencies', 'es' ),
					'manage_options',
					'edit.php?post_type=agency',
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_agencies_enabled' ),
			),
			'add-agency' => array(
				'args' => array(
					$parent,
					__( 'Add Agency', 'es' ),
					__( 'Add Agency', 'es' ),
					'manage_options',
					'post-new.php?post_type=agency',
				),
				'callback' => 'add_submenu_page',
				'disabled' => ! ests( 'is_agencies_enabled' ),
			),
			'requests' => array(
				'args' => array(
					$parent,
					__( 'Requests', 'es' ),
					$request_count ? __( 'Requests', 'es' ) . sprintf( ' <span class="awaiting-mod count-%s">%s</span>', $request_count, $request_count ) : __( 'Requests', 'es' ),
					'manage_options',
					'edit.php?post_type=request',
				),
				'callback' => 'add_submenu_page',
			),
			'fields-builder' => array(
				'args' => array(
					$parent,
					__( 'Fields Builder', 'es' ),
					__( 'Fields Builder', 'es' ),
					'manage_options',
					'es_fields_builder',
					array( 'Es_Fields_Builder_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
			),
			'settings' => array(
				'args' => array(
					$parent,
					__( 'Settings', 'es' ),
					__( 'Settings', 'es' ),
					'manage_options',
					'es_settings',
					array( 'Es_Settings_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
			),
		);

		if ( ! es_is_demo_executed() ) {
			$menu_pages['demo'] = array(
				'args' => array(
					$parent,
					__( 'Demo content', 'es' ),
					__( 'Demo content', 'es' ),
					'manage_options',
					'es_demo',
					array( 'Es_Demo_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
			);
		}

		if ( es_need_migration() ) {
			$menu_pages['migration'] = array(
				'args' => array(
					$parent,
					__( 'Migration', 'es' ),
					__( 'Migration', 'es' ),
					'manage_options',
					'es_migration',
					array( 'Es_Migration_Page', 'render' )
				),
				'callback' => 'add_submenu_page',
			);
		}

		$menu_pages = apply_filters( 'es_register_admin_pages_args', $menu_pages );

		if ( ! empty( $menu_pages ) ) {
			foreach ( $menu_pages as $menu_page ) {
				if ( empty( $menu_page['disabled'] ) ) {
					call_user_func_array( $menu_page['callback'], $menu_page['args'] );
				}
			}
		}

	}
}

Es_Admin_Menu::init();
