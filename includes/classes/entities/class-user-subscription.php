<?php

/**
 * Class Es_Free_Subscription.
 *
 * @property $order_id int
 * @property $user_id int
 * @property $basic_listings_count int
 * @property $featured_listings_count int
 * @property $published_featured_listings_count int
 * @property $published_listings_count int
 * @property $plan array
 */
class Es_User_Subscription {

	/**
	 * @var array
	 */
	protected $_data;

	/**
	 * @var string
	 */
	protected $_id;

	/**
	 * Es_User_Subscription constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$this->_data = es_parse_args( $data, array(
			'user_id' => get_current_user_id(),
		) );

		if ( ! empty( $data['id'] ) ) {
			$this->_id = $data['id'];
		}

		if ( ! empty( $this->user_id ) ) {
			$this->published_listings_count =
				(int)get_user_meta( $this->user_id, 'es_published_listings_count', true );

			$this->published_featured_listings_count =
				(int)get_user_meta( $this->user_id, 'es_published_featured_listings_count', true );
		}
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * @param Es_Order $order
	 *
	 * @return Es_User_Subscription
	 */
	public static function build_from_order( Es_Order $order ) {
		return new static( array(
			'id' => $order->get_id(),
			'user_id' => $order->user_id,
			'order_id' => $order->get_id(),
			'basic_listings_count' => $order->basic_listings_count,
			'featured_listings_count' => $order->featured_listings_count,
			'plan' => $order->plan,
		) );
	}

	/**
	 * Return subscription title.
	 *
	 * @return string|void
	 */
	public function get_title() {
		return ! empty( $this->plan['name'] ) ? $this->plan['name'] : __( 'One time payment', 'es' );
	}

	public function increase_published() {
		$inc = $this->published_listings_count + 1;
		update_user_meta( $this->user_id, 'es_published_listings_count', $inc );
		$this->published_listings_count = $inc;
	}

	public function decrease_published() {
		$inc = $this->published_listings_count - 1;
		update_user_meta( $this->user_id, 'es_published_listings_count', $inc );
		$this->published_listings_count = $inc;
	}

	public function increase_featured() {
		$inc = $this->published_featured_listings_count + 1;
		update_user_meta( $this->user_id, 'es_published_featured_listings_count', $inc );
		$this->published_featured_listings_count = $inc;
	}

	public function decrease_featured() {
		$inc = $this->published_featured_listings_count - 1;
		$inc = $inc < 0 ? 0 : $inc;
		update_user_meta( $this->user_id, 'es_published_featured_listings_count', $inc );
		$this->published_featured_listings_count = $inc;
	}

	/**
	 * @param Es_Subscription_Plan $plan
	 *
	 * @param $user_id
	 *
	 * @return Es_User_Subscription
	 */
	public static function build_from_plan( Es_Subscription_Plan $plan, $user_id = null ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		return new static( array(
			'user_id' => $user_id,
			'plan' => $plan->get_wp_entity(),
			'basic_listings_count' => (int)$plan->is_basic_listings_limited ?$plan->basic_listings_limit : -1,
			'featured_listings_count' => (int)$plan->is_featured_listings_limited ? $plan->featured_listings_limit : -1,
		) );
	}

	/**
	 * Return subscription related order.
	 *
	 * @return Es_Order|null
	 */
	public function get_order() {
		return ! empty( $this->order_id ) ? es_get_order( $this->order_id ) : null;
	}

	/**
	 * @return void
	 */
	public function set_cancelled() {
		$order = $this->get_order();

		if ( $order ) {
			$order->deactivate();
		}
	}

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'user_id' => array(
				'default_value' => get_current_user_id(),
			),

			'order_id' => array(),

			'basic_listings_count' => array(
				'default_value' => 0,
			),

			'published_listings_count' => array(
				'default_value' => 0,
			),

			'published_featured_listings_count' => array(
				'default_value' => 0,
			),

			'featured_listings_count' => array(
				'default_value' => 0,
			),

			'plan' => array(),
		);

		return apply_filters( 'es_' . static::get_entity_name() . '_default_fields', $fields );
	}

	/**
	 * Return entity field value.
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		$fields = static::get_default_fields();
		$value = null;

		if ( isset( $fields[ $name ] ) ) {
			$value = $this->get_field_value( $name );
		}

		return apply_filters( "es_get_" . static::get_entity_name() . "_field_value", $value, $name, $this );
	}

	/**
	 * @param $field
	 * @param $value
	 */
	public function __set( $field, $value ) {
		$this->_data[ $field ] = $value;
	}

	/**
	 * Magic method for empty and isset methods.
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		$value = $this->__get( $name );
		return ! empty( $value );
	}

	/**
	 * Return plan field value.
	 *
	 * @param $field
	 *
	 * @return mixed|void
	 */
	public function get_field_value( $field ) {
		$fields = static::get_default_fields();
		$value = null;

		if ( isset( $fields[ $field ] ) ) {
			$f_info = $fields[ $field ];
			$default_value = isset( $f_info['default_value'] ) ? $f_info['default_value'] : $value;
			$value = isset( $this->_data[ $field ] ) ? $this->_data[ $field ] : $default_value;
		}

		return apply_filters( "es_user_subscription_get_field_value", $value, $field, $this );
	}

	/**
	 * @return void
	 */
	public function deactivate() {
		$order = $this->get_order();
		if ( $order ) {
			$order->close();
			es_cancel_user_subscription( $this->user_id );
		}
	}

	/**
	 * @return mixed|void
	 */
	public function delete() {
		$this->deactivate();
	}

	/**
	 * Return entity name.
	 *
	 * @return string
	 */
	public static function get_entity_name() {
		return 'user_subscription';
	}
}
