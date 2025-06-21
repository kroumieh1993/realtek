<?php

/**
 * Class Es_Payment_Init
 */
class Es_Payment_Init {

	/**
	 * Initialize payment actions.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'payment_submit' ) );

		if ( ! empty( $_GET['_subscription_nonce'] ) ) {
			add_action( 'init', array( __CLASS__, 'paypal_subscription_actions' ) );
		}

		if ( ! empty( $_GET['payment_method'] ) && ! empty( $_GET['payment-action'] ) ) {
			add_action( 'init', array( __CLASS__, 'payment_proceeded' ) );
			add_action( 'es_paypal_payment_proceeded', array( __CLASS__, 'paypal_payment_proceeded' ) );
			add_action( 'es_paypal-subscriptions_payment_proceeded', array( __CLASS__, 'paypal_subscriptions_payment_proceeded' ) );
		}
	}

	/**
	 * Cancel user subscription action.
	 *
	 * @return void
	 */
	public static function paypal_subscription_actions() {
		if ( ! ( $nonce = es_get_nonce( '_subscription_nonce' ) ) ) return;
		$subscription = es_get_user_subscription( get_current_user_id(), false );

		if ( $subscription && ( $order = $subscription->get_order() ) ) {
			if ( $order->is_subscription && stristr( $order->payment_method, 'paypal' ) ) {
				if ( ! empty( $order->payment_data['id'] ) ) {
					$paypal = es_paypal_get_api_instance();
					try {
						if ( wp_verify_nonce( $nonce, 'es_subscription_cancel' ) ) {
							if ( $paypal->subscription()->cancel( $order->payment_data['id'] ) ) {
								$subscription->deactivate();
								$subscription->set_cancelled();
							}
						} else if ( wp_verify_nonce( $nonce, 'es_subscription_renew' ) ) {
							if ( $paypal->subscription()->activate( $order->payment_data['id'] ) ) {
								$paypal_subscription = $paypal->subscription()->get( $order->payment_data['id'] );

								if ( 'ACTIVE' == $paypal_subscription['status'] ) {
									$order->save_field_value( 'payment_data', $paypal_subscription );
									$order->save_field_value( 'payment_status', $order::PAYMENT_STATUS_CHARGED );

									if ( ! empty( $paypal_subscription['subscriber']['email_address'] ) ) {
										$order->save_field_value( 'payer_email', $paypal_subscription['subscriber']['email_address'] );
									}

									if ( ! empty( $paypal_subscription['billing_info']['next_billing_time'] ) ) {
										$order->save_field_value( 'next_payment_time', strtotime( $paypal_subscription['billing_info']['next_billing_time'] ) );
									}

									$user = es_get_user_entity( 'id', get_current_user_id() );

									$result = es_set_user_subscription( array(
										'order_id'   => $order->get_id(),
										'start_time' => strtotime( $paypal_subscription['start_time'] ),
										'status'     => Es_Order::ORDER_STATUS_ACTIVE
									) );

									if ( $result ) {
										es_send_email( 'subscription_renew', $user->get_email(), array(
											'agent_name' => $user->get_full_name(),
										) );
									}
								}
							}
						}
					} catch ( Exception $e ) {
						wp_die( $e->getMessage() );
					}
				}
			}
		}

		$redirect_url = es_get_page_url( 'profile' );
		$redirect_url = $redirect_url ? add_query_arg( 'tab', 'billing', $redirect_url ) : home_url();
		wp_safe_redirect( $redirect_url );
		die;

	}

	/**
	 * Manage payment method response.
	 *
	 * @return void
	 */
	public static function payment_proceeded() {
		$payment_method = filter_input( INPUT_GET, 'payment_method' );

		if ( $payment_method_instance = es_get_payment_method( $payment_method ) ) {
			do_action( sprintf( 'es_%s_payment_proceeded', $payment_method ), $payment_method_instance );
		}
	}

	/**
	 * Finish payment and start subscription.
	 *
	 * @return void
	 */
	public static function paypal_subscriptions_payment_proceeded() {
		$subscription_id = es_get( 'subscription_id' );
		$order = Es_Order::get_by_token( es_get( 'order_token' ) );
		$api = es_paypal_get_api_instance();

		try {
			$paypal_subscription = $api->subscription()->get( $subscription_id );

			if ( 'ACTIVE' == $paypal_subscription['status'] ) {
				$order->save_field_value( 'payment_data', $paypal_subscription );
				$order->save_field_value( 'payment_status', $order::PAYMENT_STATUS_CHARGED );

				if ( ! empty( $paypal_subscription['subscriber']['email_address'] ) ) {
					$order->save_field_value( 'payer_email', $paypal_subscription['subscriber']['email_address'] );
				}

				if ( ! empty( $paypal_subscription['billing_info']['next_billing_time'] ) ) {
					$order->save_field_value( 'next_payment_time', strtotime( $paypal_subscription['billing_info']['next_billing_time'] ) );
				}

				$user = es_get_user_entity();

				$prev_order_id = get_user_meta( $user->get_id(), 'es_subscription_order_id', true );

				$result = es_set_user_subscription( array(
					'order_id' => $order->get_id(),
					'start_time' => strtotime( $paypal_subscription['start_time'] ),
					'status' => Es_Order::ORDER_STATUS_ACTIVE
				) );

				if ( $result ) {
					if ( ! $prev_order_id ) {
						es_send_email( 'subscription_user_subscribed', $user->get_email(), array(
							'agent_name' => $user->get_full_name(),
						) );
					} else {
						es_send_email( 'subscription_upgraded', $user->get_email(), array(
							'agent_name' => $user->get_full_name(),
						) );
					}

					$redirect_url = es_get_page_url( 'profile' );
					$redirect_url = $redirect_url ? add_query_arg( 'tab', 'billing', $redirect_url ) : home_url();
					wp_safe_redirect( apply_filters( 'es_paypal_subscriptions_redirect_url', $redirect_url ) );
					die;
				}
			}

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	/**
	 * Manage paypal response and start user subscription.
	 *
	 * @return void
	 * @throws Exception
	 */
	public static function paypal_payment_proceeded() {
		$token = filter_input( INPUT_GET, 'token' );
		$order = Es_Order::get_by_token( filter_input( INPUT_GET, 'order_token' ) );
		$api = es_paypal_get_api_instance();
		$paypal_order = $api->order()->get( $token );

		if ( ! empty( $paypal_order['status'] ) ) {
			if ( $paypal_order['status'] == 'APPROVED' ) {
				$order->save_field_value( 'payment_status', $order::PAYMENT_STATUS_PENDING );
				try {
					$paypal_order = $api->order()->capture( $token );
				} catch ( Exception $e ) {
					wp_die( $e->getMessage() );
				}
			}

			if ( $paypal_order['status'] == 'COMPLETED' ) {
				$order->save_field_value( 'payment_status', $order::PAYMENT_STATUS_CHARGED );
				$order->save_field_value( 'status', $order::ORDER_STATUS_ACTIVE );

				$result = es_set_user_subscription( array(
					'order_id' => $order->get_id(),
					'start_time' => time(),
				) );

				$user = es_get_user_entity();

				if ( $result ) {
					es_send_email( 'subscription_otp_payed', $user->get_email(), array(
						'agent_name' => $user->get_full_name()
					) );

					$redirect_url = es_get_page_url( 'profile' );
					$redirect_url = $redirect_url ? add_query_arg( 'tab', 'billing', $redirect_url ) : home_url();
					wp_safe_redirect( apply_filters( 'es_paypal_payment_redirect_url', $redirect_url ) );
					die;
				}
			}
		} else {
			wp_die( __( 'Incorrect paypal return action response. Please contact the site support.', 'es' ) );
		}
	}

	/**
	 * Checkout submit.
	 *
	 * @return void
	 */
	public static function payment_submit() {
		if ( wp_verify_nonce( es_get_nonce( 'es_submit_payment' ), 'es_submit_payment' ) ) {
			$payment_type = sanitize_key( filter_input( INPUT_POST, 'payment_type' ) );
			$allowed_payment_types = es_subscriptions_get_payment_types();
			$payment_methods = es_get_payment_methods_list();
			$payment_method = sanitize_key( filter_input( INPUT_POST, 'payment_method' ) );
			$plan_id = intval( filter_input( INPUT_POST, 'plan_id' ) );
			$plan = es_get_subscription_plan( $plan_id );

			if ( $plan_id && $plan->is_free_plan_enabled ) {
				$order_id = es_save_order( array(
					'product_id' => $plan->get_id(),
					'is_subscription' => 1,
					'payment_status' => Es_Order::PAYMENT_STATUS_CHARGED,
					'status' => Es_Order::ORDER_STATUS_ACTIVE,
					'plan' => $plan->get_wp_entity(),
					'period' => sanitize_text_field( filter_input( INPUT_POST, 'period' ) ),
					'basic_listings_count' => $plan->is_basic_listings_limited ? $plan->basic_listings_limit : -1,
					'featured_listings_count' => $plan->is_featured_listings_limited ? $plan->featured_listings_limit : -1,
				) );

				if ( $order_id && ! is_wp_error( $order_id ) ) {
					$result = es_set_user_subscription( array(
						'order_id' => $order_id,
					) );

					if ( $result ) {
						$redirect_url = es_get_page_url( 'profile' );
						$redirect_url = $redirect_url ? add_query_arg( 'tab', 'billing', $redirect_url ) : home_url();
						wp_safe_redirect( apply_filters( 'es_payment_submit_redirect_url', $redirect_url ) );
						die;
					}
				} else {
					wp_die( $order_id->get_error_message() );
				}
			} else {
				if ( ! array_key_exists( $payment_type, $allowed_payment_types ) || ! array_key_exists( $payment_method, $payment_methods ) ) {
					wp_die( __( 'Provided payment is not allowed.', 'es' ) );
				}

				do_action( sprintf( 'es_before_%s_%s_payment_submit', $payment_type, $payment_method ) );

				$payment_method_instance = es_get_payment_method( $payment_method );

				if ( $payment_method_instance instanceof Es_Payment_Method ) {
					$payment_method_instance->proceed( es_clean( $_POST ) );
				} else {
					wp_die( __( 'Provided payment method is not allowed.', 'es' ) );
				}

				do_action( sprintf( 'es_after_%s_%s_payment_submit', $payment_type, $payment_method ) );
			}
		}
	}
}
Es_Payment_Init::init();
