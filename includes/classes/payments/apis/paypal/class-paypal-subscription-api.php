<?php

/**
 * Class Es_PayPal_Subscription_Api.
 */
class Es_PayPal_Subscription_Api {

	const ENDPOINT = '/v1/billing/subscriptions';

	/**
	 * @var Es_PayPal_Api
	 */
	protected $_api;

	/**
	 * Es_PayPal_Subscription_Api constructor.
	 *
	 * @param Es_PayPal_Api $api
	 */
	public function __construct( Es_PayPal_Api $api ) {
		$this->_api = $api;
	}

	public function capture( $subscription_id ) {
		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . '/' . $subscription_id . '/capture', array(
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
	 * @param $subscription_id
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get( $subscription_id ) {
		$request = wp_remote_get( $this->_api->get_api_url() . static::ENDPOINT . '/' . $subscription_id, array(
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
	 * @param $subscription_id
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function activate( $subscription_id ) {
		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . '/' . $subscription_id . '/activate', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
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
	 * @param $subscription_id
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function cancel( $subscription_id ) {
		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . '/' . $subscription_id . '/cancel', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
			),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! $response ) return true;

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			}

			return $response;
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}

	/**
	 * @param $subscription_data
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function create( $subscription_data ) {
		$subscription_data = es_parse_args( $subscription_data, array(
			'plan_id' => '',
			'quantity' => 1,
//			'start_time' => date( 'Y-m-d\TH:i:s\Z', time() + 1 ),
			'custom_id' => '',
			'application_context' => array(
				'landing_page' => 'BILLING',
				'shipping_preference' => 'NO_SHIPPING',
				'return_url' => home_url(),
				'cancel_url' => home_url(),
			),
		) );

		$subscription_data = array_filter( $subscription_data );

		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
				'Prefer' => 'return=representation',
			),
			'body' => json_encode( $subscription_data ),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			} else {
				if ( ! empty( $response['id'] ) ) {
					return $response;
				} else {
					throw new Exception( __( 'PayPal subscription doesn\'t create. Unknown error.', 'es' ) );
				}
			}
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}
}
