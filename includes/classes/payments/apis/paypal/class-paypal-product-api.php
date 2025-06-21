<?php

/**
 * Class Es_PayPal_Product_Api.
 */
class Es_PayPal_Product_Api {

	/**
	 * @var Es_PayPal_Api
	 */
	protected $_api;

	/**
	 * @const string
	 */
	const ENDPOINT = '/v1/catalogs/products';

	const PRODUCT_TYPE_DIGITAL = 'DIGITAL';
	const PRODUCT_CATEGORY_RPM = 'RENTAL_PROPERTY_MANAGEMENT';

	/**
	 * Es_PayPal_Product_Api constructor.
	 *
	 * @param Es_PayPal_Api $api
	 */
	public function __construct( Es_PayPal_Api $api ) {
		$this->_api = $api;
	}

	/**
	 * Create PayPal product.
	 *
	 * @param $product_data
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function create( $product_data ) {
		$product_data = wp_parse_args( $product_data, array(
			'name' => '',
			'type' => static::PRODUCT_TYPE_DIGITAL,
			'category' => static::PRODUCT_CATEGORY_RPM,
		) );

		$request = wp_remote_post( $this->_api->get_api_url() . static::ENDPOINT, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->_api->get_access_token(),
				'Content-Type' => 'application/json',
			),
			'body' => json_encode( $product_data ),
		) );

		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $response['name'] ) && $response['name'] == 'INVALID_REQUEST' ) {
				throw new Exception( $response['message'] );
			} else {
				if ( ! empty( $response['id'] ) ) {
					return $response;
				} else {
					throw new Exception( __( 'PayPal product doesn\'t create. Unknown error.', 'es' ) );
				}
			}
		} else {
			throw new Exception( $request->get_error_message() );
		}
	}
}