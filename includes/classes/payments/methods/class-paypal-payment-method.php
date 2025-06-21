<?php

/**
 * Class Es_Paypal_Payment_Method.
 */
class Es_Paypal_Payment_Method implements Es_Payment_Method {

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
	 */
	public function proceed( $data ) {
		$data = wp_parse_args( $data, array(
			'basic_listings_count' => 1,
			'featured_listings_count' => 0,
		) );

		$basic_price = ests( 'otp_basic_price' );
		$featured_price = ests( 'otp_featured_price' );
		$sum = 0;

		if ( $basic_price && $data['basic_listings_count'] ) {
			$sum += $data['basic_listings_count'] * $basic_price;
		}

		if ( $featured_price && $data['featured_listings_count'] ) {
			$sum += $data['featured_listings_count'] * $featured_price;
		}

		$sum = apply_filters( 'es_process_otp_payment_amount', $sum );

		$api = es_paypal_get_api_instance();

		if ( ests( 'otp_is_basic_bonus_enabled' ) ) {
			$free_featured = ests( 'otp_free_featured_count' );
			$per_basic = ests( 'otp_free_basic_count' );

			if ( $per_basic && $free_featured && $data['basic_listings_count'] && $data['basic_listings_count'] >= $per_basic ) {
				$n = floor( $data['basic_listings_count'] / $per_basic );
				$data['featured_listings_count'] += $free_featured * $n;
			}
		}

		try {
			$order_id = es_save_order( array(
				'payment_method' => 'paypal',
				'payment_type' => 'otp',
				'amount' => $sum,
				'basic_listings_count' => $data['basic_listings_count'],
				'featured_listings_count' => $data['featured_listings_count'],
			) );

			if ( ! is_wp_error( $order_id ) ) {
				$order = es_get_order( $order_id );

				$paypal_order = $api->order()->create( array(
					'purchase_units' => array(
						(object) array( 'amount' => array( 'value' => $sum, 'currency_code' => ests( 'currency' ) ) )
					),
					'application_context' => array(
						'return_url' => add_query_arg( array( 'order_token' => $order->get_order_token(), 'payment_method' => 'paypal', 'payment-action' => 'return' ),trailingslashit( home_url() ) ),
						'cancel_url' => add_query_arg( array( 'order_token' => $order->get_order_token(), 'payment_method' => 'paypal', 'payment-action' => 'cancel' ), trailingslashit( home_url() ) ),
					),
				) );

				if ( ! empty( $paypal_order['id'] ) && ! empty( $paypal_order['links'] ) ) {
					$order->save_field_value( 'payment_data', $paypal_order );
					$order->save_field_value( 'create_time', strtotime( $paypal_order['create_time'] ) );

					if ( ! is_wp_error( $order ) ) {
						if ( ! empty( $paypal_order['links'] ) ) {
							wp_redirect( $paypal_order['links'][1]['href'] );
							die;
						}
					}
				}
			}
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}
}
