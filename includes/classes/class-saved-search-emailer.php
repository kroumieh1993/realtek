<?php

/**
 * Class Es_Saved_Search_Emailer.
 */
class Es_Saved_Search_Emailer {

	/**
	 * Init saved search scheduler.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'activate_scheduler' ) );
		add_action( 'es_emailer_scheduler_handler', array( __CLASS__, 'emailer_handler' ) );
	}

	/**
	 * @param $period
	 *
	 * @return mixed|void
	 */
	public static function period_to_days( $period ) {
		if ( $period == 'daily' ) {
			$days = '-1 day';
		}

		if ( $period == 'weekly' ) {
			$days = '-7 day';
		}

		if ( $period == 'monthly' ) {
			$days = '-' . date('t') . ' days';
		}

		return apply_filters( 'es_saved_search_emailer_days_by_period', $days, $period );
	}

	/**
	 * @param $period
	 */
	public static function emailer_handler( $period ) {
		if ( ! ests( 'is_saved_search_enabled' ) ) return false;

		$date = static::period_to_days( $period );

		$saved_searches = get_posts( array(
			'post_type' => Es_Saved_Search::get_post_type_name(),
			'posts_per_page' => -1,
			'fields' => 'ids',
			'post_status' => 'private',
			'meta_query' => array(
				array(
					'key' => 'es_saved_search_update_type',
					'value' => $period,
				),
			),
			'date_query' => array(
				'column' => 'post_modified',
				'before' => date( 'Y-m-d', strtotime( $date ) )
			),
		) );

		if ( ! empty( $saved_searches ) ) {
			foreach ( $saved_searches as $saved_search_id ) {
				$saved_search = es_get_saved_search( $saved_search_id );
				$properties = $saved_search->get_properties_ids();

				if ( ! $properties ) continue;

				$saved_search->save_field_value( 'mailed_properties_ids', $properties );

				$email_instance = es_get_email_instance( 'saved_search', array(
					'saved_search' => $saved_search,
					'properties' => $properties,
				) );

				if ( $email_instance && $email_instance::is_active() && $email_instance->send( $saved_search->get_author()->user_email ) ) {
					wp_update_post( array(
						'ID' => $saved_search->get_id(),
					) );
				}
			}
		}
	}

	/**
	 * @return mixed|void
	 */
	public static function get_types() {
		return apply_filters( 'es_get_saved_search_update_types', array(
			'daily' => __( 'Daily', 'es' ),
			'weekly' => __( 'Weekly', 'es' ),
			'monthly' => __( 'Monthly', 'es' ),
			'none' => __( 'None', 'es' ),
		) );
	}

	/**
	 * Register and schedule saved search cron event.
	 *
	 * @return bool|void
	 */
	public static function activate_scheduler() {
		if ( ! ests( 'is_saved_search_enabled' ) ) return false;

		$period = apply_filters( 'es_saved_search_emailer_scheduler_period', '10m' );
		$types = static::get_types();
		unset( $types['none'] );

		if ( ! empty( $types ) ) {
			foreach ( array_keys( $types ) as $type ) {
				if ( ! wp_next_scheduled( 'es_emailer_scheduler_handler', array( $type ) ) ) {
					wp_schedule_event( time(), $period, 'es_emailer_scheduler_handler', array( $type ) );
				}
			}
		}

		do_action( 'es_emailer_scheduler_handler', 'daily' );
	}
}

Es_Saved_Search_Emailer::init();
