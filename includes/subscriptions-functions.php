<?php

/**
 * Return list of payment methods.
 *
 * @return mixed|void
 */
function es_get_payment_methods_list() {
	if ( ! class_exists( 'Es_Paypal_Payment_Method' ) ) {
		include 'classes/payments/methods/interface-payment-method.php';
		include 'classes/payments/methods/class-paypal-payment-method.php';
		include 'classes/payments/methods/class-paypal-subscriptions-payment-method.php';
	}

	return apply_filters( 'es_get_payment_methods_list', array(
		'paypal' => new Es_Paypal_Payment_Method(),
		'paypal-subscriptions' => new Es_Paypal_Subscription_Payment_Method(),
	) );
}

/**
 * @param $payment_method
 *
 * @return bool
 */
function es_is_payment_method_enabled( $payment_method ) {
	return (bool) ests( "is_{$payment_method}_payment_method_enabled" );
}

/**
 * Return payment method by name.
 *
 * @param $method
 *
 * @return mixed|void
 */
function es_get_payment_method( $method ) {
	$methods = es_get_payment_methods_list();
	return apply_filters( 'es_get_payment_method', ! empty( $methods[ $method ] ) ? $methods[ $method ] : null, $method );
}

/**
 * @return int Next plan id.
 */
function es_get_next_plan_id() {
	$plans = ests( 'plans' );

	if ( empty( $plans ) ) {
		$id = 1;
	} else {
		$id = max( wp_list_pluck( $plans, 'ID' ) ) + 1;
	}

	return $id;
}

/**
 * @param $data
 *
 * @return WP_Error|int
 */
function es_save_subscription_plan( $data ) {
	if ( empty( $data['ID'] ) ) {
		$data['ID'] = es_get_next_plan_id();
	}

	$subscription = es_get_subscription_plan( $data['ID'] );

	if ( empty( $data['name'] ) ) {
		return new WP_Error( -1, __( 'Plan name field is required to save', 'es' ) );
	}

	$subscription->save_fields( $data );

	do_action( 'es_after_save_subscription_plan', $subscription, $subscription->get_id() );

	return $data['ID'];
}

/**
 * Return paypal api instance.
 *
 * @return Es_PayPal_Api
 */
function es_paypal_get_api_instance() {
	if ( ! class_exists( 'Es_PayPal_Api' ) ) {
		include 'classes/payments/apis/paypal/class-paypal-api.php';
		include 'classes/payments/apis/paypal/class-paypal-product-api.php';
		include 'classes/payments/apis/paypal/class-paypal-plan-api.php';
		include 'classes/payments/apis/paypal/class-paypal-order-api.php';
		include 'classes/payments/apis/paypal/class-paypal-subscription-api.php';
	}

	$instance = false;

	if ( ( $client_id = ests( 'paypal_client_id' ) ) && ( $client_secret = ests( 'paypal_client_secret' ) ) ) {
		$instance = new Es_PayPal_Api( $client_id, $client_secret, ests( 'paypal_mode' ) );
	}

	return apply_filters( 'es_paypal_get_api_instance', $instance );
}

/**
 * Return paypal base product id.
 *
 * @return mixed|void
 */
function es_paypal_get_base_product() {
	return apply_filters( 'es_paypal_get_base_product',
		get_option( sprintf( 'es_paypal_%s_%s_base_product', ests( 'paypal_mode' ), ests( 'paypal_client_id' ) ) ) );
}

/**
 * Return paypal base product id.
 *
 * @param $product_data array
 *
 * @return mixed|void
 */
function es_paypal_set_base_product( $product_data ) {
	update_option( sprintf( 'es_paypal_%s_%s_base_product', ests( 'paypal_mode' ), ests( 'paypal_client_id' ) ), $product_data );
}

/**
 * @param $plan Es_Subscription_Plan
 * @param $period
 *
 * @return array|bool
 */
function es_paypal_get_plan( $plan, $period ) {
	$plans = $plan->paypal_plan;
	$paypal_client = ests( 'paypal_client_id' );
	$mode = ests( 'paypal_mode' );
	$period = $plan::prepare_period( $period );

	return ! empty( $plans[ $mode ][ $paypal_client ][ $period ] ) ?
		$plans[ $mode ][ $paypal_client ][ $period ] : false;
}

/**
 * Check is subscription plan has paypal plan.
 *
 * @param $plan Es_Subscription_Plan
 * @param $period
 *
 * @return bool
 */
function es_paypal_has_plan( $plan, $period ) {
	$plans = $plan->paypal_plan;
	$paypal_client = ests( 'paypal_client_id' );
	$mode = ests( 'paypal_mode' );
	$period = $plan::prepare_period( $period );

	return ( ! empty( $plans[ $mode ][ $paypal_client ][ $period ] ) );
}

/**
 * Save paypal plan in plugin subscription plan.
 *
 * @param $plan Es_Subscription_Plan
 * @param $paypal_plan array
 * @param $period string
 */
function es_paypal_save_plan( $plan, $paypal_plan, $period ) {
	$plans = $plan->paypal_plan;
	$paypal_client = ests( 'paypal_client_id' );
	$mode = ests( 'paypal_mode' );
	$period = $plan::prepare_period( $period );

	$plans[ $mode ][ $paypal_client ][ $period ] = $paypal_plan;
	$plan->save_field_value( 'paypal_plan', $plans );
}

/**
 * Return plan price by plan period.
 *
 * @param Es_Subscription_Plan $plan
 * @param string $period
 *
 * @return int|null
 */
function es_paypal_get_plan_price( Es_Subscription_Plan $plan, $period = Es_PayPal_Plan_Api::INTERVAL_MONTH ) {
	$price = null;
	$period = $plan::prepare_period( $period );

	if ( $period == Es_PayPal_Plan_Api::INTERVAL_MONTH ) {
		$price = $plan->monthly_price;
	} else if ( $period == Es_PayPal_Plan_Api::INTERVAL_YEAR ) {
		$price = $plan->annual_price;
	}

	return apply_filters( 'es_paypal_get_plan_price', $price );
}

/**
 * @param $plan Es_Subscription_Plan
 * @param $interval
 *
 * @return array
 */
function es_paypal_create_plan_request_body( Es_Subscription_Plan $plan, $interval ) {
	$body = array();
	$price = es_paypal_get_plan_price( $plan, $interval );

	if ( ! empty( $price ) && ( $paypal_product = es_paypal_get_base_product() ) ) {
		$billing_cycles = array();
		$sequence = 1;

		$body['name'] = sprintf( $plan->name . ' (%s)', $interval );
		$body['payment_preferences'] = array(
			'auto_bill_outstanding' => true,
			'setup_fee_failure_action' => 'CANCEL',
		);

		if ( $plan->is_free_trial_enabled ) {
			$billing_cycles[] = array(
				'frequency' => array(
					'interval_unit' => $plan->trial_period,
					'interval_count' => 1,
				),
				'tenure_type' => 'TRIAL',
				'sequence' => $sequence++,
				'total_cycles' => 1,
			);
		}

		$billing_cycles[] = array(
			'frequency' => array(
				'interval_unit' => $interval,
				'interval_count' => 1,
			),
			'tenure_type' => 'REGULAR',
			'sequence' => $sequence,
			'total_cycles' => 0,
			'pricing_scheme' => array(
				'fixed_price' => array(
					'value' => $price,
					'currency_code' => ests( 'currency' )
				)
			),
		);

		$body['billing_cycles'] = $billing_cycles;
	}

	return apply_filters( 'es_paypal_create_plan_request_body', $body, $plan, $interval );
}

/**
 * @param Es_Subscription_Plan $plan
 *
 * @return void
 */
function es_plan_classes( Es_Subscription_Plan $plan ) {
	$classes[] = 'es-plan';
	$classes[] = 'js-es-plan';
	$classes[] = 'es-plan--monthly-active';

	if ( $plan->is_label_active && $plan->label_text ) $classes[] = 'es-plan--has-label';
	if ( $plan->is_highlighted ) $classes[] = 'es-plan--highlighted';
	if ( $plan->annual_price || $plan->is_free_plan_enabled ) $classes[] = 'es-plan--has-annual-price';
	if ( $plan->monthly_price || $plan->is_free_plan_enabled ) $classes[] = 'es-plan--has-monthly-price';

	echo apply_filters( 'es_plan_classes', implode( ' ', $classes ), $plan );
}

/**
 * @return string
 */
function es_get_add_new_property_url() {
	$link = false;

	if ( es_user_can_publish_listings() ) {
		$link = add_query_arg( 'screen', 'add-new-property' );
	} else {
		if ( ests( 'plans' ) ) {
			$link = es_get_page_url( 'pricing' );
		} else if ( es_is_otp_enabled() ) {
			$link = add_query_arg( 'screen', 'otp', es_get_page_url( 'pricing' ) );
		}
	}

	return apply_filters( 'es_get_add_new_property_url', $link );
}

/**
 * @return mixed|void
 */
function es_subscriptions_get_payment_types() {
	$types = array();

	if ( ests( 'plans' ) ) {
		$types['plans'] = __( ests( 'subscription_tr_subscriptions_btn' ), 'es' );
	}

	if ( es_is_otp_enabled() ) {
		$types['otp'] = __( ests( 'subscription_tr_otp_btn' ), 'es' );
	}

	return apply_filters( 'es_subscriptions_get_payment_types', $types );
}

/**
 * @return bool
 */
function es_is_otp_enabled() {
	return ests( 'is_subscriptions_enabled' ) && ests( 'otp_basic_price' ) && ests( 'is_otp_enabled' );
}

/**
 * @param $data
 *
 * @return int|WP_Error|null
 */
function es_set_user_subscription( $data ) {
	$data = wp_parse_args( $data, array(
		'user_id' => get_current_user_id(),
		'order_id' => '',
	) );

	if ( $subscription = es_get_user_subscription( $data['user_id'] ) ) {
		$subscription->deactivate();
	}

	$order = es_get_order( $data['order_id'] );
	$order->save_field_value( 'status', $order::ORDER_STATUS_ACTIVE );

	update_user_meta( $data['user_id'], 'es_subscription_order_id', $data['order_id'] );

	if ( ! es_has_user_meta( $data['user_id'], 'es_published_listings_count' ) ) {
		update_user_meta( $data['user_id'], 'es_published_listings_count', 0 );
		update_user_meta( $data['user_id'], 'es_published_featured_listings_count', 0 );
	}

	return true;
}

/**
 * @param $user_id
 */
function es_cancel_user_subscription( $user_id = null ) {
	global $wpdb;
	$user_id = $user_id ? $user_id : get_current_user_id();

	delete_user_meta( $user_id, 'es_subscription_order_id'  );
	delete_user_meta( $user_id, 'es_published_listings_count'  );
	delete_user_meta( $user_id, 'es_published_featured_listings_count' );

	if ( ! user_can( $user_id, 'manage_options' ) ) {

		// Draft all agent listings.
		$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array(
			'post_author' => $user_id,
			'post_type' => 'properties',
			'post_status' => 'publish',
		) );
	}
}

/**
 * @return Es_Subscription_Plan
 */
function es_get_default_plan() {
	$plans = ests( 'plans' );
	$result = null;

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			if ( ! empty( $plan['is_default'] ) ) {
				$result = es_get_subscription_plan( $plan['ID'] );
			}
		}
	}

	return apply_filters( 'es_get_default_plan', $result );
}

/**
 * @param null $user_id
 *
 * @param bool $use_default_subscription
 *
 * @return Es_User_Subscription|bool
 */
function es_get_user_subscription( $user_id = null, $use_default_subscription = true ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	if ( ! $user_id ) {
		return false;
	}

	$subscription_order_id = get_user_meta( $user_id, 'es_subscription_order_id', true );

	$default_plan = ! $subscription_order_id && $use_default_subscription ?
		es_get_default_plan() : null;

	$subscription = $subscription_order_id ? Es_User_Subscription::build_from_order( es_get_order( $subscription_order_id ) ) : false;

	if ( ! $subscription && $default_plan ) {
		$subscription = Es_User_Subscription::build_from_plan( $default_plan );
	}

	return $subscription;
}

/**
 * @param $user_id
 *
 * @return Es_Order[]
 */
function es_get_user_orders( $user_id = null ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	if ( $user_id ) {
		$posts = get_posts( array(
			'post_type' => 'es_order',
			'post_status' => 'private',
			'meta_key' => 'es_order_user_id',
			'meta_value' => $user_id,
			'posts_per_page' => -1,
			'fields' => 'ids',
		) );
	}

	return ! empty( $posts ) ? array_map( 'es_get_order', $posts ) : array();
}

/**
 * @param null $user_id
 *
 * @return bool
 */
function es_user_has_active_subscription( $user_id = null ) {
	$user_id = $user_id ? $user_id : get_current_user_id();
	$subscription = es_get_user_subscription( $user_id );

	if ( ! $subscription || ! $user_id ) return false;

	$order = $subscription->get_order();

	return $subscription && ( ! $order || ( $order && $order->status == $order::ORDER_STATUS_ACTIVE ) );
}

/**
 * @param null $user_id
 *
 * @return bool
 */
function es_user_can_publish_listings( $user_id = null ) {
	$user_id = $user_id ? $user_id : get_current_user_id();
	if ( ! ests( 'is_subscriptions_enabled' ) || user_can( $user_id, 'manage_options' ) ) return true;

	if ( ! es_user_has_active_subscription( $user_id ) ) return false;
	$subscription = es_get_user_subscription( $user_id );
	$can_publish_num = 0;

	if ( $subscription->basic_listings_count == -1 ) {
		$can_publish = true;
	} else {
		if ( $subscription->basic_listings_count >= 0 ) {
			$can_publish_num += $subscription->basic_listings_count;
		}

		if ( $subscription->featured_listings_count >= 0 ) {
			$can_publish_num += $subscription->featured_listings_count;
		}

		if ( $subscription->basic_listings_count < 0 ) {
			$can_publish = true;
		} else {
			$can_publish = $can_publish_num > $subscription->published_listings_count;
		}

		if ( $can_publish ) {
			update_user_meta( $user_id, 'es_hit_limit_email_reset', 1 );
		}
	}

	return apply_filters( 'es_user_can_publish_listings', $can_publish, $subscription, $user_id );
}

/**
 * @param null $user_id
 *
 * @return bool
 */
function es_user_can_publish_featured_listings( $user_id = null ) {
	$user_id = $user_id ? $user_id : get_current_user_id();
	if ( ! ests( 'is_subscriptions_enabled' ) || user_can( $user_id, 'manage_options' ) ) return true;

	if ( ! es_user_has_active_subscription( $user_id ) ) return false;
	$subscription = es_get_user_subscription( $user_id );

	if ( $subscription->featured_listings_count == -1 ) {
		$can_publish = true;
	} else {
		if ( $subscription->published_featured_listings_count < 0 ) {
			$can_publish = true;
		} else {
			$can_publish = $subscription->published_featured_listings_count < $subscription->featured_listings_count;
		}
	}

	return apply_filters( 'es_user_can_publish_featured_listings', $can_publish, $subscription, $user_id );
}

/**
 * @param $plan_id
 * @param $period
 *
 * @return bool
 */
function es_user_has_provided_subscription_plan( $plan_id, $period = null ) {
	$subscription = es_get_user_subscription();

	if ( ! $subscription ) return false;

	$order = $subscription ? $subscription->get_order() : null;
	$order_period = $order ? $order->period : '';

	if ( ! empty( $subscription->plan['is_default'] ) ) {
		$order_period = $period = '';
	}

	return $subscription && ! empty( $subscription->plan['ID'] ) && $subscription->plan['ID'] == $plan_id
		&& $order_period == $period;
}
