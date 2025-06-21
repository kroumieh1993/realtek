<?php

/**
 * Class Es_PayPal_Plan_Api
 */
class Es_PayPal_Plan_Api {

	const ENDPOINT = '/v1/billing/plans';
	const TYPE_INFINITE = 'INFINITE';

	const STATUS_ACTIVE = 'ACTIVE';
	const INTERVAL_MONTH = 'MONTH';
	const INTERVAL_WEEK = 'WEEK';
	const INTERVAL_YEAR = 'YEAR';

	const TENURE_TYPE_REGULAR = 'REGULAR';
	const TENURE_TYPE_TRIAL = 'TRIAL';

	/**
	 * @var Es_PayPal_Api
	 */
	protected $_api;

	/**
	 * @var array
	 */
	protected $_product_data;

	/**
	 * @var string
	 */
	protected $_product_id;

	/**
	 * @return string[]
	 */
	public static function get_intervals() {
		return array( static::INTERVAL_WEEK, static::INTERVAL_MONTH, static::INTERVAL_YEAR );
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function process_response( $request ) {
		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			} else {
				return $response;
			}
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}

	/**
	 * Es_PayPal_Plan_Api constructor.
	 *
	 * @param $product_data
	 * @param Es_PayPal_Api $api
	 */
	public function __construct( $product_data, Es_PayPal_Api $api ) {
		$this->_product_data = $product_data;
		$this->_product_id = $product_data['id'];
		$this->_api = $api;
	}

	/**
	 * @param $plan_data
	 *
	 * @return array
	 * @throws Exception
	 */
	public function create( $plan_data ) {
		$plan_data = es_parse_args( $plan_data, array(
			'product_id' => $this->_product_id,
			'name' => '',
			'type' => static::TYPE_INFINITE,
			'status' => static::STATUS_ACTIVE,
			'description' => '',
			'billing_cycles' => ''
		) );

		$plan_data = array_filter( $plan_data );

		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Prefer' => 'return=representation',
			),
			'body' => json_encode( $plan_data ),
		) );

		return static::process_response( $request );
	}

	/**
	 * @param $plan_id
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get( $plan_id ) {
		$request = wp_remote_get( $this->_api->get_api_url() . static::ENDPOINT . "/{$plan_id}", array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
			),
		) );

		return static::process_response( $request );
	}

	/**
	 * @param $plan_id
	 */
	public function deactivate( $plan_id ) {
		wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . "/{$plan_id}/deactivate", array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
			),
		) );
	}

	/**
	 * @param $plan_id
	 * @param $data
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function update_pricing( $plan_id, $data ) {
		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT . "/{$plan_id}/update-pricing-schemes", array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
			),
			'body' => json_encode( array(
				'pricing_schemes' => $data,
			) )
		) );

		return static::process_response( $request );
	}
}