<?php

/**
 * Class Es_Paypal_Api
 *
 * @property $_mode string
 */
class Es_PayPal_Api {

	/**
	 * @const string
	 */
	const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';

	/**
	 * @const string
	 */
	const LIVE_URL = 'https://api-m.paypal.com';

	/**
	 * @var string
	 */
	protected $_client_id;

	/**
	 * @var string
	 */
	protected $_secret;

	/**
	 * @var string
	 */
	protected $_mode = 'sandbox';

	/**
	 * @var string
	 */
	protected $_access_token;

	/**
	 * @var array
	 */
	protected $_access_token_response;

	/**
	 * Es_PayPal_Api constructor.
	 *
	 * @param $client_id
	 * @param $client_secret
	 * @param string $mode
	 */
	public function __construct( $client_id, $client_secret, $mode = 'sandbox' ) {
		$this->_client_id = $client_id;
		$this->_secret = $client_secret;

		if ( in_array( $mode, array( 'sandbox', 'live' ) ) ) {
			$this->_mode = $mode;
		}
	}

	/**
	 * Return PayPal API url.
	 *
	 * @return string
	 */
	public function get_api_url() {
		return $this->_mode == 'sandbox' ? static::SANDBOX_URL : static::LIVE_URL;
	}

	/**
	 * Return PayPal API mode.
	 *
	 * @return string
	 */
	public function get_mode() {
		return $this->_mode;
	}

	/**
	 * Generate and return access token.
	 *
	 * @return string
	 */
	protected function generate_access_token() {
		$request = wp_remote_get( $this->get_api_url() . '/v1/oauth2/token', array(
			'method' => 'POST',
			'headers' => array(
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( $this->_client_id . ':' . $this->_secret ),
			),
			'body' => array(
				'grant_type' => 'client_credentials'
			)
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['error_description'] ) ) {
				throw new Exception( $response['error_description'] );
			} else if ( ! empty( $response['error'] ) ){
				throw new Exception( $response['error'] );
			} else if ( ! empty( $response['access_token'] ) ) {
				$this->_access_token_response = $response;
				return $response['access_token'];
			} else {
				throw new Exception( __( 'Unknown PayPal Auth error.', 'es' ) );
			}
		} else {
			return $request;
		}
	}

	/**
	 * Generate PayPal Access token
	 */
	public function get_access_token() {
		if ( empty( $this->_access_token ) ) {
			$this->_access_token = $this->generate_access_token();
		}

		return $this->_access_token;
	}

	/**
	 * @return Es_PayPal_Product_Api
	 */
	public function product() {
		return new Es_PayPal_Product_Api( $this );
	}

	/**
	 * @param $product_data array
	 *
	 * @return Es_PayPal_Plan_Api
	 */
	public function plan( $product_data = array() ) {
		return new Es_PayPal_Plan_Api( $product_data, $this );
	}

	/**
	 * @return Es_PayPal_Order_Api
	 */
	public function order() {
		return new Es_PayPal_Order_Api( $this );
	}

	/**
	 * @return Es_PayPal_Subscription_Api
	 */
	public function subscription() {
		return new Es_PayPal_Subscription_Api( $this );
	}
}
