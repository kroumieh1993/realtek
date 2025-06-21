<?php

/**
 * Class Es_Order.
 *
 * @property $featured_listings_count int
 * @property $user_id int
 * @property $amount int
 * @property $basic_listings_count int
 * @property $create_time int
 * @property $period string
 * @property $payment_method string
 * @property $payer_email string
 * @property $next_payment_time string
 * @property $payment_type string
 * @property $plan array
 * @property $payment_data array
 * @property $is_subscription bool
 * @property $payment_status bool
 * @property $status bool
 */
class Es_Order extends Es_Post {

	const PAYMENT_STATUS_PENDING = 'pending';
	const PAYMENT_STATUS_CHARGED = 'charged';
	const PAYMENT_STATUS_CREATED = 'created';

	const ORDER_STATUS_ACTIVE = 'active';
	const ORDER_STATUS_PENDING = 'pending';
	const ORDER_STATUS_CANCELED = 'canceled';
	const ORDER_STATUS_CLOSED = 'closed';
	const ORDER_STATUS_SUSPENDED = 'suspended';

	/**
	 * @return string
	 */
	public function get_entity_prefix() {
		return 'es_order_';
	}

	public static function get_entity_name() {
		return 'order';
	}

	public static function get_subscription_statuses() {
		return apply_filters( 'es_order_statuses_list', array(
			static::ORDER_STATUS_CLOSED => __( 'Closed', 'es' ),
			static::ORDER_STATUS_ACTIVE => __( 'Active', 'es' ),
			static::ORDER_STATUS_CANCELED => __( 'Canceled', 'es' ),
			static::ORDER_STATUS_PENDING => __( 'Pending', 'es' ),
			static::ORDER_STATUS_SUSPENDED => __( 'Suspended', 'es' ),
		) );
	}

	public function get_status() {
		$statuses = static::get_subscription_statuses();

		return ! empty( $statuses[ $this->status ] ) ? $statuses[ $this->status ] : null;
	}

	/**
	 * @return mixed|null
	 */
	public function get_payment_status() {
		$statuses = apply_filters( 'es_order_payment_statuses_list', array(
			static::PAYMENT_STATUS_PENDING => __( 'Pending', 'es' ),
			static::PAYMENT_STATUS_CHARGED => __( 'Charged', 'es' ),
			static::PAYMENT_STATUS_CREATED => __( 'Created', 'es' ),
		) );

		return ! empty( $statuses[ $this->payment_status ] ) ? $statuses[ $this->payment_status ] : null;
	}

	/**
	 * @return mixed|string
	 */
	public static function get_post_type_name() {
		return 'es_order';
	}

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'user_id' => array(),
			'end_date' => array(),
			'payment_data' => array(),
			'period' => array(),
			'payer_email' => array(),
			'basic_listings_count' => array(
				'default_value' => 0,
			),
			'featured_listings_count' => array(
				'default_value' => 0,
			),
			'payment_method' => array(),
			'payment_type' => array(),
			'payment_status' => array(
				'default_value' => static::PAYMENT_STATUS_CREATED
			),
			'status' => array(
				'default_value' => static::ORDER_STATUS_PENDING
			),
			'create_time' => array(
				'default_value' => time(),
			),
			'start_time' => array(
				'default_value' => time(),
			),
			'next_payment_time' => array(),
			'amount' => array(),
			'currency' => array(
				'default_value' => ests( 'currency' ),
			),
			'product_id' => array(),
			'plan' => array(),
			'is_subscription' => array(
				'default_value' => 0
			),
			'is_automatic_payment' => array(
				'default_value' => 0,
			),
		);

		return apply_filters( 'es_order_default_fields', $fields );
	}

	/**
	 * @return string
	 */
	public function get_order_token() {
		return base64_encode( json_encode( array(
			'id' => $this->get_id(),
			'time' => time(),
		) ) );
	}

	/**
	 * @param $token
	 *
	 * @return static|null
	 */
	public static function get_by_token( $token ) {
		$token = json_decode( base64_decode( $token ), true );

		return ! empty( $token['id'] ) ? new static( $token['id'] ) : null;
	}

	/**
	 * @return mixed|void
	 */
	public function get_period_label() {
		$label = null;

		if ( $this->period == 'annual' ) {
			$label = __( 'Annual', 'es' );
		} else if ( $this->period == 'monthly' ) {
			$label = __( 'Monthly', 'es' );
		}

		return apply_filters( 'es_order_get_period_label', $label, $this->get_id() );
	}

	/**
	 * @return mixed
	 */
	public function get_payment_type_label() {
		$label = null;

		if ( $this->is_subscription ) {
			$label = __( 'Subscription', 'es' );
		} else if ( $this->payment_type == 'otp' ) {
			$label = __( 'One time payment' );
		}

		return apply_filters( 'es_order_get_payment_type_label', $label, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_create_date() {
		return apply_filters( 'es_order_get_create_date', date( 'F j, Y, g:i a', $this->create_time ), $this->get_id() );
	}

	/**
	 * @inheritdoc
	 */
	public function deactivate() {
		$this->save_field_value( 'status', static::ORDER_STATUS_CANCELED );
	}

	/**
	 * @return void
	 */
	public function suspend() {
		$this->save_field_value( 'status', static::ORDER_STATUS_SUSPENDED );
	}

	/**
	 * @return void
	 */
	public function active() {
		$this->save_field_value( 'status', static::ORDER_STATUS_ACTIVE );
	}

	/**
	 * @return void
	 */
	public function close() {
		$this->save_field_value( 'status', static::ORDER_STATUS_CLOSED );
	}

	/**
	 * @return bool
	 */
	public function is_cancelled() {
		return $this->status == static::ORDER_STATUS_CANCELED;
	}

	/**
	 * @return bool
	 */
	public function is_suspended() {
		return $this->status == static::ORDER_STATUS_SUSPENDED;
	}
}
