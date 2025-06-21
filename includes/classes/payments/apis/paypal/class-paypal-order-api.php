<?php

/**
 * Class Es_PayPal_Order_Api
 */
class Es_PayPal_Order_Api {

	const ENDPOINT = '/v2/checkout/orders';

	/**
	 * @var Es_PayPal_Api
	 */
	protected $_api;

	/**
	 * Es_PayPal_Order_Api constructor.
	 *
	 * @param Es_PayPal_Api $api
	 */
	public function __construct( Es_PayPal_Api $api ) {
		$this->_api = $api;
	}

	public function capture( $token ) {
		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . '/' . $token . '/capture', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
				'Prefer' => 'return=representation',
			),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			}

			return $response;
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}

	public function get( $token ) {
		$request = wp_remote_get( $this->_api->get_api_url() . static::ENDPOINT . '/' . $token, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
				'Prefer' => 'return=representation',
			),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			}

			return $response;
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}

	/**
	 * @param $order_data array
	 *
	 * @throws Exception
	 */
	public function create( $order_data ) {
		$order_data = es_parse_args( $order_data, array(
			'intent' => 'CAPTURE',
			'purchase_units' => array(),
			'application_context' => array(
				'landing_page' => 'BILLING',
				'shipping_preference' => 'NO_SHIPPING',
				'user_action' => 'PAY_NOW',
				'return_url' => home_url(),
				'cancel_url' => home_url(),
			)
		) );

		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
				'Prefer' => 'return=representation',
			),
			'body' => json_encode( $order_data ),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			}

			return $response;
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}
}
