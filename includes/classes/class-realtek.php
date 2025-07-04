<?php

/**
 * Class Realtek
 *
 * Plugin initializer class.
 */
class Realtek {

	/**
	 * Plugin instance.
	 *
	 * @var Realtek
	 */
	protected static $_instance;

	const VERSION = '4.1.1';
	const TYPE = 'pro';

	/**
	 * Realtek constructor.
	 */
	protected function __construct() {
		$this->load_files();

		register_deactivation_hook( ES_FILE, array( get_called_class(), 'deactivation' ) );
		register_activation_hook( ES_FILE, array( get_called_class(), 'activation' ) );
	}

	/**
	 * @return string
	 */
	public static function get_version() {
		return static::VERSION;
	}

	/**
	 * @return string
	 */
	public static function get_plugin_type() {
		return static::TYPE;
	}

	/**
	 * Load plugin dependencies & files.
	 *
	 * @return void
	 */
	protected function load_files() {

		$files = apply_filters( 'es_plugin_files', array(
			'settings-container' => ES_PLUGIN_CLASSES . 'class-settings-container.php',
			'taxonomies_init' => ES_PLUGIN_CLASSES . 'class-taxonomies.php',
			'post_types_init' => ES_PLUGIN_CLASSES . 'class-post-types.php',

			'functions' => ES_PLUGIN_INCLUDES . 'functions.php',
			'ajax' => ES_PLUGIN_INCLUDES . 'ajax.php',
			'helper-functions' => ES_PLUGIN_INCLUDES . 'helper-functions.php',
			'loop-functions' => ES_PLUGIN_INCLUDES . 'loop-functions.php',
			'entity-functions' => ES_PLUGIN_INCLUDES . 'entity-functions.php',
			'property-functions' => ES_PLUGIN_INCLUDES . 'property-functions.php',
			'agents-functions' => ES_PLUGIN_INCLUDES . 'agents-functions.php',
			'subscriptions-functions' => ES_PLUGIN_INCLUDES . 'subscriptions-functions.php',
			'wp-query-functions' => ES_PLUGIN_INCLUDES . 'wp-query-functions.php',
			'subscriptions-hooks' => ES_PLUGIN_INCLUDES . 'subscriptions-hooks.php',
			'hooks' => ES_PLUGIN_INCLUDES . 'hooks.php',
			'flash' => ES_PLUGIN_CLASSES . 'helpers' . DS . '/class-flash-message.php',

			'auth' => ES_PLUGIN_CLASSES . 'auth' . DS . 'class-authentication.php',
			'fb-auth' => ES_PLUGIN_CLASSES . 'auth' . DS . 'class-facebook-authentication.php',
			'google-auth' => ES_PLUGIN_CLASSES . 'auth' . DS . 'class-google-authentication.php',

			'framework' => ES_PLUGIN_CLASSES . 'framework' . DS . 'framework.php',
			'admin_logo_init' => ES_PLUGIN_CLASSES . 'class-realtek-logo.php',

			'assets-init' => ES_PLUGIN_CLASSES . 'class-assets-init.php',
			'auth-init' => ES_PLUGIN_CLASSES . 'class-auth-init.php',
			'admin-init' => ES_PLUGIN_CLASSES . 'class-admin-init.php',

			'entity-meta-box' => ES_PLUGIN_CLASSES . 'meta-boxes' . DS . 'class-entity-fields-meta-box.php',
			'property-meta-box' => ES_PLUGIN_CLASSES . 'meta-boxes' . DS . 'class-property-fields-meta-box.php',
			'agent-meta-box' => ES_PLUGIN_CLASSES . 'meta-boxes' . DS . 'class-agent-fields-meta-box.php',
			'agency-meta-box' => ES_PLUGIN_CLASSES . 'meta-boxes' . DS . 'class-agency-fields-meta-box.php',

			'dashboard-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-dashboard-page.php',
			'settings-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-settings-page.php',
			'fields-builder-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-fields-builder-page.php',
			'terms-creator' => ES_PLUGIN_CLASSES . DS . 'data-manager' . DS . 'class-terms-creator.php',
			'labels-creator' => ES_PLUGIN_CLASSES . DS . 'data-manager' . DS . 'class-labels-creator.php',
			'features-creator' => ES_PLUGIN_CLASSES . DS . 'data-manager' . DS . 'class-features-creator.php',
			'features-icons-creator' => ES_PLUGIN_CLASSES . DS . 'data-manager' . DS . 'class-features-icons-creator.php',
			'locations-creator' => ES_PLUGIN_CLASSES . DS . 'data-manager' . DS . 'class-locations-creator.php',
			'data-manager-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-data-manager-page.php',
			'entities-archive-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-entities-archive-page.php',
			'properties-archive-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-properties-archive-page.php',
			'requests-archive-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-requests-archive-page.php',
			'agents-archive-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-agents-archive-page.php',
			'agencies-archive-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-agencies-archive-page.php',
			'subscriptions-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-subscriptions-page.php',
			'subscriptions-orders-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-subscriptions-orders-archive-page.php',
			'demo-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-demo-page.php',
			'migration-page' => ES_PLUGIN_CLASSES . DS . 'pages' . DS . 'admin' . DS . 'class-migration-page.php',

            ES_PLUGIN_CLASSES . DS . 'helpers' . DS . 'interface-container.php',
            ES_PLUGIN_CLASSES . DS . 'helpers' . DS . 'class-user-container.php',
            ES_PLUGIN_CLASSES . DS . 'helpers' . DS . 'class-cookies-container.php',
            'wishlist-user' => ES_PLUGIN_CLASSES . DS . 'wishlist' . DS . 'class-wishlist-user.php',
            'wishlist-cookie' => ES_PLUGIN_CLASSES . DS . 'wishlist' . DS . 'class-wishlist-cookie.php',

			'compare-user' => ES_PLUGIN_CLASSES . DS . 'compare' . DS . 'class-compare-cookie.php',
			'compare-cookie' => ES_PLUGIN_CLASSES . DS . 'compare' . DS . 'class-compare-user.php',
			'compare' => ES_PLUGIN_CLASSES . DS . 'compare' . DS . 'class-compare-instance.php',

			'admin-menu' => ES_PLUGIN_CLASSES . 'class-admin-menu.php',

			'address-components' => ES_PLUGIN_CLASSES . 'class-address-components.php',
			'emailed-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'interface-emailed-entity.php',
			'entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-entity.php',
			'post-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-post.php',
			'user-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-user.php',
			'saved-search' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-saved-search.php',
			'property-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-property.php',
			'request-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-request.php',
			'subscription-plan-entity' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-subscription-plan.php',
			'agent-post' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-agent-post.php',
			'agent-user' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-agent-user.php',
			'agency' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-agency.php',
			'order' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-order.php',
			'user-subscription' => ES_PLUGIN_CLASSES . DS . 'entities' . DS . 'class-user-subscription.php',

			'base-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-widget.php',
			'base-properties-filter-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-properties-filter-widget.php',
			'search-form-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-search-form-widget.php',
			'request-form-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-request-form-widget.php',
			'locations-grid-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-locations-grid-widget.php',
			'properties-slideshow-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-properties-slideshow-widget.php',
			'properties-slider-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-properties-slider-widget.php',
			'listings-widget' => ES_PLUGIN_CLASSES . 'widgets' . DS . 'class-listings-widget.php',

			'migrations' => ES_PLUGIN_CLASSES . 'class-migrations.php',

			'fields-builder-item' => ES_PLUGIN_CLASSES . 'fields-builder' . DS . 'class-fields-builder-item.php',
			'fields-builder' => ES_PLUGIN_CLASSES . 'fields-builder' . DS . 'class-fields-builder.php',
			'sections-builder' => ES_PLUGIN_CLASSES . 'fields-builder' . DS . 'class-sections-builder.php',

			'shortcodes' => ES_PLUGIN_CLASSES . 'shortcodes' . DS . 'class-shortcodes.php',

			'elementor-init' => ES_PLUGIN_CLASSES . 'class-elementor.php',
			'divi-init' => ES_PLUGIN_CLASSES . 'class-divi.php',

			'profile-page' => ES_PLUGIN_CLASSES . 'pages' . DS . 'front' . DS . 'class-profile-page.php',
			'template-loader' => ES_PLUGIN_CLASSES . 'class-template-loader.php',
			'comment-walker' => ES_PLUGIN_CLASSES . 'helpers' . DS . 'class-walker-comment.php',

			'entities-table' => ES_PLUGIN_CLASSES . 'entities' . DS . 'tables' . DS . 'class-entities-table.php',
			'properties-table' => ES_PLUGIN_CLASSES . 'entities' . DS . 'tables' . DS . 'class-properties-table.php',
			'payment-ini' => ES_PLUGIN_CLASSES . 'payments' . DS . 'class-payment-init.php',

			'email' => ES_PLUGIN_CLASSES . 'emails/class-email.php',
			'request-property-info-email' => ES_PLUGIN_CLASSES . 'emails/class-request-property-info-email.php',
			'request-agent-info-email' => ES_PLUGIN_CLASSES . 'emails/class-request-agent-info-email.php',
			'request-agency-info-email' => ES_PLUGIN_CLASSES . 'emails/class-request-agency-info-email.php',
			'new-user-info-email' => ES_PLUGIN_CLASSES . 'emails/class-new-user-info-email.php',
			'new-user-registered-admin-email' => ES_PLUGIN_CLASSES . 'emails/class-new-user-registered-admin-email.php',
			'reset-password-email' => ES_PLUGIN_CLASSES . 'emails/class-reset-password-email.php',
			'admin-listing-agent-added-email' => ES_PLUGIN_CLASSES . 'emails/class-admin-listing-agent-added-email.php',
			'saved-search-email' => ES_PLUGIN_CLASSES . 'emails/class-saved-search-email.php',
			'agent-listing-submitted-email' => ES_PLUGIN_CLASSES . 'emails/class-agent-listing-submitted-email.php',
			'admin-listing-published-email' => ES_PLUGIN_CLASSES . 'emails/class-admin-listing-published-email.php',
			'agent-listing-published-email' => ES_PLUGIN_CLASSES . 'emails/class-agent-listing-published-email.php',
			'subscription-expired-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-expired-email.php',
			'subscription-upgrade-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-upgrade-email.php',
			'subscription-upgraded-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-upgraded-email.php',
			'subscription-otp-payed-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-otp-payed-email.php',
			'subscription-user-subscribed-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-user-subscribed-email.php',
			'subscription-renew-email' => ES_PLUGIN_CLASSES . 'emails/class-subscription-renew-email.php',
			'new-agent-confirm-email' => ES_PLUGIN_CLASSES . 'emails/class-new-agent-confirm-email.php',

			'saved_search_emailer' => ES_PLUGIN_CLASSES . DS . '/class-saved-search-emailer.php',
			'polylang' => ES_PLUGIN_CLASSES . 'class-polylang-init.php',
		) );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				require_once $file;
			}
		}
	}

	/**
	 * Plugin Activation handler.
	 *
	 * @return void
	 */
	public static function activation() {
		do_action( 'es_activation' );

		add_role( 'agent', _x( 'Agent', 'realtek agent role name', 'es' ) );
	}

	/**
	 * Plugin Deactivation handler.
	 *
	 * @return void
	 */
	public static function deactivation() {

	}

	/**
	 * Return plugin instance.
	 *
	 * @return static
	 */
	public static function get_instance() {

		if ( ! static::$_instance ) {
			static::$_instance = new static();
		}

		return static::$_instance;
	}
}
