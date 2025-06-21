<?php

/**
 * Class Es_Subscription_Plan.
 *
 * @property $ID number
 * @property $name string
 * @property $is_label_active number
 * @property $label_text string
 * @property $label_color string
 * @property $description string
 * @property $is_default number
 * @property $is_highlighted number
 * @property $block_color string
 * @property $is_basic_listings_limited number
 * @property $basic_listings_limit number
 * @property $is_featured_listings_limited number
 * @property $featured_listings_limit number
 * @property $is_free_plan_enabled number
 * @property $is_free_trial_enabled number
 * @property $trial_period string
 * @property $monthly_price number
 * @property $annual_price number
 * @property $main_button string
 * @property $main_button_caption string
 * @property $start_trial_button string
 * @property $start_trial_button_caption string
 * @property $paypal_plan string
 */
class Es_Subscription_Plan extends Es_Entity {

	/**
	 * Es_Subscription_Plan constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id = null ) {
		parent::__construct( $id );

		if ( $id ) {
			$plans = ests( 'plans' );
			if ( ! empty( $plans ) ) {
				foreach ( $plans as $plan ) {
					if ( ! empty( $plan['ID']  ) && $plan['ID'] == $id ) {
						$this->_wp_entity = $plan;
						break;
					}
				}
			}
		}
	}

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'ID' => array(),
			'name' => array(),
			'is_label_active' => array(),
			'label_text' => array(),
			'label_color' => array(
				'default_value' => '#FFB300'
			),
			'description' => array(),
			'is_default' => array(),
			'is_highlighted' => array(),
			'block_color' => array(
				'default_value' => '#FFF8E1'
			),
			'is_basic_listings_limited' => array(
				'default_value' => 1
			),
			'basic_listings_limit' => array(),
			'is_featured_listings_limited' => array(
				'default_value' => 1
			),
			'featured_listings_limit' => array(),
			'is_free_plan_enabled' => array(),
			'is_free_trial_enabled' => array(),
			'trial_period' => array(
				'default_value' => 'week',
			),
			'monthly_price' => array(),
			'annual_price' => array(),
			'main_button' => array(),
			'main_button_caption' => array(),
			'start_trial_button' => array(),
			'start_trial_button_caption' => array(),
			'paypal_plan' => array(),
		);
		return apply_filters( 'es_subscription_plan_default_fields', $fields );
	}

	/**
	 * @param $period
	 *
	 * @return string
	 */
	public static function prepare_period( $period ) {
		if ( 'monthly' == $period ) {
			return 'MONTH';
		} else if ( 'annual' == $period ) {
			return 'YEAR';
		}

		return $period;
	}

	/**
	 * @return string
	 */
	public function get_entity_prefix() {
		return false;
	}

	/**
	 * @return array|WP_Post|WP_User
	 */
	public function get_wp_entity() {
		if ( ! empty( $this->_wp_entity ) ) {
			return $this->_wp_entity;
		}

		$plans = ests( 'plans' );
		$key = $this->get_key();

		if ( ! is_null( $key ) && ! empty( $plans[ $key ] ) ) {
			$this->_wp_entity = $plans[ $key ];
		} else {
			$id = $this->get_id();

			if ( ! empty( $id ) && ! empty( $plans ) ) {
				foreach ( $plans as $plan ) {
					if ( ! empty( $plan['ID'] ) && $plan['ID'] == $id ) {
						$this->_wp_entity = $plan;
						break;
					}
				}
			}
		}

		return $this->_wp_entity;
	}

	/**
	 * @param $period
	 *
	 * @return mixed
	 */
	public function get_amount( $period ) {
		return $this->{$period . '_price'};
	}

	/**
	 * @param bool $force
	 *
	 * @return mixed|void
	 */
	public function delete( $force = false ) {
		$plans = ests( 'plans' );
		$key = $this->get_key();
		unset( $plans[ $key ] );
		do_action( 'es_before_delete_subscription_plan', $this, $this->get_id() );
		ests_save_option( 'plans', $plans );
	}

	/**
	 * @return bool|void
	 */
	public function deactivate() {
		$this->delete();
	}

	/**
	 * Return plan field value.
	 *
	 * @param $field
	 *
	 * @return mixed|void
	 */
	public function get_field_value( $field ) {
		$entity = $this->get_wp_entity();
		$entity_name = static::get_entity_name();
		$value = null;

		if ( $f_info = static::get_field_info( $field) ) {
			$default_value = isset( $f_info['default_value'] ) ? $f_info['default_value'] : $value;
			$value = isset( $entity[ $field ] ) ? $entity[ $field ] : $default_value;
		}

		return apply_filters( "es_{$entity_name}_get_field_value", $value, $field, $this );
	}

	/**
	 * @return int|string|null
	 */
	public function get_key() {
		$plans = ests( 'plans' );

		foreach ( $plans as $key => $plan ) {
			if ( $plan['ID'] == $this->get_id() ) {
				return $key;
			}
		}

		return null;
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 */
	public function save_field_value( $field, $value ) {
		$entity_name = static::get_entity_name();
		$f_info = static::get_field_info( $field );

		if ( $f_info ) {
			$entity = $this->get_wp_entity();
			$plans = ests( 'plans' );
			$value = es_clean( $value );
			$value = apply_filters( "es_{$entity_name}_save_field_value", $value, $field, $this );

			do_action( "es_{$entity_name}_before_save_field_value", $value, $field, $this );
			$entity[ $field ] = $value;
			$this->_wp_entity = $entity;
			$key = $this->get_key();

			if ( ! is_null( $key ) ) {
				$plans[ $key ] = $entity;
			} else {
				$plans[ $this->get_id() ] = $entity;
			}

			ests_save_option( 'plans', $plans );
			do_action( "es_{$entity_name}_after_save_field_value", $value, $field, $this );
		}
	}

	/**
	 * @param $field
	 * @param string $value
	 */
	public function delete_field_value( $field, $value = '' ) {
		$entity = static::get_entity_name();
		do_action( "es_{$entity}_before_delete_field_value", $field, $value, $this );
		$plans = ests( 'plans' );
		$entity = $this->get_wp_entity();
		unset( $entity[ $field ] );

		$key = $this->get_key();

		if ( ! is_null( $key ) ) {
			$plans[ $key ] = $entity;
		} else {
			$plans[ $this->get_id() ] = $entity;
		}

		ests_save_option( 'plans', $plans );
		do_action( "es_{$entity}_after_delete_field_value", $field, $value, $this );
	}

	/**
	 * Return entity name.
	 *
	 * @return string
	 */
	public static function get_entity_name() {
		return 'subscription_plan';
	}
}