<?php

/**
 * Class Es_Paypal_Subscription_Payment_Method.
 */
class Es_Paypal_Subscription_Payment_Method implements Es_Payment_Method {

	/**
	 * @return bool
	 */
	public function is_active() {
		return (bool) ests( 'is_paypal_payment_method_enabled' );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function proceed( $data ) {
		$data = wp_parse_args( $data, array(
			'plan_id' => '',
			'is_automatic_payment' => 0,
			'period' => 'monthly',
		) );

		$plan = es_get_subscription_plan( $data['plan_id'] );

		if ( ! es_paypal_has_plan( $plan, $data['period'] ) ) {
			es_save_paypal_billing_plan( $plan );
		}

		$paypal_plan = es_paypal_get_plan( $plan, $data['period'] );

		if ( ! $plan->get_wp_entity() ) {
			wp_die( __( 'Plan doesn\'t exist', 'es' ) );
		}

		$sum = $plan->get_amount( $data['period'] );
		$api = es_paypal_get_api_instance();

		$order_id = es_save_order( array(
			'payment_method' => 'paypal-subscriptions',
			'payment_type' => 'plans',
			'amount' => $sum,
			'product_id' => $plan->get_id(),
			'is_subscription' => 1,
			'period' => $data['period'],
			'is_automatic_payment' => $data['is_automatic_payment'],
			'plan' => $plan->get_wp_entity(),
			'basic_listings_count' => $plan->is_basic_listings_limited ? $plan->basic_listings_limit : -1,
			'featured_listings_count' => $plan->is_featured_listings_limited ? $plan->featured_listings_limit : -1,
		) );

		if ( ! is_wp_error( $order_id ) ) {
			$order = es_get_order( $order_id );
			$homeurl = trailingslashit( site_url() );
//			$homeurl = 'https://estatik.com/';

			try {
				$subscription = $api->subscription()->create( array(
					'plan_id' => $paypal_plan['id'],
					'custom_id' => $order_id,
					'application_context' => array(
						'return_url' => add_query_arg( array( 'order_token' => $order->get_order_token(), 'payment_method' => 'paypal-subscriptions', 'payment-action' => 'return' ), $homeurl ),
						'cancel_url' => add_query_arg( array( 'order_token' => $order->get_order_token(), 'payment_method' => 'paypal-subscriptions', 'payment-action' => 'cancel' ), $homeurl ),
					),
				) );

				if ( ! empty( $subscription['id'] ) && ! empty( $subscription['links'] ) ) {
					$order->save_field_value( 'payment_data', $subscription );
					$order->save_field_value( 'create_time', strtotime( $subscription['create_time'] ) );
					$order->save_field_value( 'create_time', strtotime( $subscription['create_time'] ) );
					$order->save_field_value( 'start_time', strtotime( $subscription['start_time'] ) );

					if ( ! is_wp_error( $order ) ) {
						if ( ! empty( $subscription['links'] ) ) {
							wp_redirect( $subscription['links'][0]['href'] );
							die;
						}
					}
				}
			} catch ( Exception $e ) {
				wp_delete_post( $order_id , true );
				wp_die( $e->getMessage() );
			}
		}
	}
}
