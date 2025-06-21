<?php

/**
 * Class Es_Subscriptions_Page
 */
class Es_Subscriptions_Page {

	public static function init() {
		add_action( 'wp_ajax_es_save_subscription_plan', array( 'Es_Subscriptions_Page', 'ajax_save_plan' ) );
		add_action( 'wp_ajax_es_save_plans_order', array( 'Es_Subscriptions_Page', 'ajax_save_plans_order' ) );
		add_action( 'admin_init', array( 'Es_Subscriptions_Page', 'delete_plan' ) );
	}

	/**
	 * Delete plan action.
	 */
	public static function delete_plan() {
		if ( wp_verify_nonce( es_get_nonce( '_nonce' ), 'es_delete_plan' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				$action = es_clean( filter_input( INPUT_GET, 'action' ) );
				$plan_id = es_clean( filter_input( INPUT_GET, 'plan_id' ) );

				if ( 'delete' == $action ) {
					$plan = es_get_subscription_plan( $plan_id );

					$plan->delete();
				}
			}

			wp_safe_redirect( admin_url( 'admin.php?page=es_subscriptions&tab=subscriptions' ) );
		}
	}

	/**
	 * Save plans order via ajaxю
	 */
	public static function ajax_save_plans_order() {
		$response = es_ajax_invalid_nonce_response();

		if ( check_ajax_referer( 'es_save_plans_order'  ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( ! empty( $_POST['ordered_ids'] ) ) {
					$ordered_ids = es_clean( $_POST['ordered_ids'] );
					$plans = ests( 'plans' );
					$new_plans_order = array();
					//return false;
					if ( $plans ) {
						foreach ( $ordered_ids as $plan_id ) {
							foreach ( $plans as $plan ) {
								if ( ! empty( $plan['ID'] ) && $plan['ID'] == $plan_id ) {
									$new_plans_order[] = $plan;
									break;
								}
							}
						}

						ests_save_option( 'plans', $new_plans_order );
					}

					$response = es_notification_ajax_response( __( 'Plans order successfully saved', 'es' ), 'success' );
				} else {
					$response = es_notification_ajax_response( __( 'Plans order is empty.', 'es' ), 'error' );
				}
			} else {
				$response = es_notification_ajax_response( __( 'You have no permissions to do this action', 'es' ), 'error' );
			}
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Save plan via ajax.
	 *
	 * @return void
	 */
	public static function ajax_save_plan() {
		$response = es_ajax_invalid_nonce_response();

		if ( check_ajax_referer( 'es_save_subscription_plan'  ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( ! empty( $_POST['es_plan'] ) ) {
					$data = $_POST['es_plan'];
					$saved = es_save_subscription_plan( es_clean( $data ) );

					if ( ! is_wp_error( $saved ) ) {
						$response = es_notification_ajax_response( sprintf( __( 'Plan %s successfully saved', 'es' ), $data['name'] ), 'success' );
						$response['id'] = $saved;
					} else {
						$response = es_notification_ajax_response( $saved->get_error_message(), 'error' );
					}
				} else {
					$response = es_notification_ajax_response( __( 'Please fill plan form.', 'es' ), 'error' );
				}
			} else {
				$response = es_notification_ajax_response( __( 'You haven\'t permissions to do this action', 'es' ), 'error' );
			}
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Render subscriptions settings page
	 *
	 * @return void
	 */
	public static function render() {
		$f = es_framework_instance();
		$f->load_assets();

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'es-admin-subscriptions', plugin_dir_url( ES_FILE ) . 'admin/css/subscriptions.min.css', array( 'es-select2', 'es-admin', 'estatik-popup' ), Estatik::get_version() );
		wp_enqueue_script( 'es-admin-subscriptions', plugin_dir_url( ES_FILE ) . 'admin/js/subscriptions.min.js', array( 'jquery', 'es-admin', 'jquery-ui-sortable', 'estatik-popup' ), Estatik::get_version() );
		wp_localize_script( 'es-admin-subscriptions', 'EstatikSubscription', array(
			'nonces' => array(
				'save_plans_order' => wp_create_nonce( 'es_save_plans_order' )
			),
			'tr' => es_js_get_translations(),
		) );

		$tab = filter_input( INPUT_GET, 'tab' );

		$tabs = apply_filters( 'es_admin_get_subscriptions_tabs', array(
			'subscriptions' => array(
				'label' => __( 'Subscriptions settings', 'es' ),
				'template' => es_locate_template( 'admin/subscriptions/tabs/subscriptions.php' ),
			),
			'plan-form' => array(
				'label' => '<a href="' . add_query_arg( 'tab', 'subscriptions' ) . '"><span class="es-icon es-icon_arrow-left"></span>' .  __( 'Set up plan', 'es' ) . '</a>',
				'template' => es_locate_template( 'admin/subscriptions/tabs/plan-form.php' ),
				'navbar_hide' => true,
			),
			'otp' => array(
				'label' => __( 'One-time payment settings', 'es' ),
				'template' => es_locate_template( 'admin/subscriptions/tabs/one-time-payment-tab.php' )
			),
			'payment' => array(
				'label' => __( 'Payment settings', 'es' ),
				'template' => es_locate_template( 'admin/subscriptions/tabs/payment-tab.php' )
			),
			'texts' => array(
				'label' => __( 'Texts', 'es' ),
				'template' => es_locate_template( 'admin/subscriptions/tabs/texts-tab.php' )
			),
		) );

		es_load_template( 'admin/subscriptions/index.php', array(
			'tabs' => $tabs,
			'current_tab' => $tab && ! empty( $tabs[ $tab ] ) ? $tab : 'subscriptions'
		) );
	}
}

Es_Subscriptions_Page::init();
