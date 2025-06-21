<?php

/**
 * Delete paypal subscription plan.
 *
 * @param Es_Subscription_Plan $plan.
 */
function es_delete_paypal_billing_plan( Es_Subscription_Plan $plan ) {
	if ( es_is_payment_method_enabled( 'paypal' ) ) {
		if ( ! empty( $plan->paypal_plan['id'] ) && ( $api = es_paypal_get_api_instance() ) ) {
			try {
				$api->plan()->deactivate( $plan->paypal_plan );
			} catch ( Exception $e ) {
				es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $e->getMessage(), 'error' );
			}
		}
	}
}
add_action( 'es_before_delete_subscription_plan', 'es_delete_paypal_billing_plan' );

/**
 * Create paypal base product and plans.
 *
 * @param Es_Subscription_Plan $plan .
 *
 * @throws Exception
 */
function es_save_paypal_billing_plan( Es_Subscription_Plan $plan ) {
	es_clear_flash( 'admin_subscriptions' );

	if ( ! es_is_payment_method_enabled( 'paypal' ) || ! ( $api = es_paypal_get_api_instance() ) || $plan->is_free_plan_enabled )
		return;

	if ( ! ( $product = es_paypal_get_base_product() ) ) {

		try {
			$product = $api->product()->create( apply_filters( 'es_paypal_base_product_data', array(
				'name' => __( 'Property management subscription', 'es' ),
			) ) );

			es_paypal_set_base_product( $product );
		} catch ( Exception $e ) {
			es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $e->getMessage(), 'error' );
		}
	}

	foreach ( Es_PayPal_Plan_Api::get_intervals() as $interval ) {
		if ( ! es_paypal_get_plan_price( $plan, $interval ) ) continue;

		$request_body = es_paypal_create_plan_request_body( $plan, $interval );

		if ( es_paypal_has_plan( $plan, $interval ) ) {
			if ( ! empty( $request_body['billing_cycles'] ) ) {
				$formatted_request_body = array();
				$paypal_plan = es_paypal_get_plan( $plan, $interval );

				foreach ( $request_body['billing_cycles'] as $billing_cycle ) {
					$formatted_request_body[] = array(
						'billing_cycle_sequence' => $billing_cycle['sequence'],
						'pricing_scheme' => $billing_cycle['pricing_scheme']
					);
				}

				try {
					$api->plan( $product )->update_pricing( $paypal_plan['id'], $formatted_request_body );
					$update_plan = $api->plan( $product )->get( $paypal_plan['id'] );
					es_paypal_save_plan( $plan, $update_plan, $interval );

				} catch ( Exception $e ) {
					es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $e->getMessage(), 'error' );
				}
			} else {
				$error = __( 'Empty product plan request body', 'es' );
				es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $error, 'error' );
			}
		} else {
			if ( ! empty( $product['id'] ) ) {
				try {
					if ( $request_body ) {
						$created_plan = $api->plan( $product )->create( $request_body );
						es_paypal_save_plan( $plan, $created_plan, $interval );
					} else {
						$error = __( 'Empty product plan request body', 'es' );
						es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $error, 'error' );
					}
				} catch ( Exception $e ) {
					es_set_flash( 'admin_subscriptions', '<b>PayPal</b>: ' . $e->getMessage(), 'error' );
				}
			}
		}
	}
}
add_action( 'es_after_save_subscription_plan', 'es_save_paypal_billing_plan' );

/**
 * @param $new_status
 * @param null $old_status
 * @param null $post
 */
function es_subscription_property_check_published( $new_status, $old_status = null, $post = null ) {
	if ( current_user_can( 'manage_options' ) && ( get_current_user_id() == $post->post_author || user_can( $post->post_author, 'manage_options' ) ) )
		return;
	if ( ! ests( 'is_subscriptions_enabled' ) || $new_status == $old_status || 'properties' != $post->post_type ) return;

	if ( user_can( $post->post_author, 'edit_post', $post ) ) {
		$flashes = es_get_flash_instance( 'subscriptions' );

		if ( es_user_has_active_subscription( $post->post_author ) ) {
			$subscription = es_get_user_subscription( $post->post_author );

			// If post is published.
			if ( $new_status != $old_status ) {
				if ( $new_status == 'publish' ) {
					if ( es_user_can_publish_listings( $post->post_author ) ) {
						$subscription->increase_published();
					} else {
						global $wpdb;
						$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $post->ID ) );
						$flashes->set_message( 'Your subscription plan doesn\'t allow to publish more listings.', 'es' );
					}
				} else if ( $old_status == 'publish' ) {
					$subscription->decrease_published();
				}
			}
		} else {
			$flashes->set_message( 'Your subscription plan doesn\'t allow to publish listings.', 'es' );
		}
	}
}
add_action( 'transition_post_status', 'es_subscription_property_check_published', 11, 3 );

/**
 * @param $data array Property data.
 * @param $property_id int
 *
 * @return array
 */
function es_subscription_check_featured( $data, $property_id ) {
	if ( ! ests( 'is_subscriptions_enabled' ) ) return $data;

	$property = es_get_property( $property_id );
	$post = $property->get_wp_entity();

	if ( current_user_can( 'manage_options' ) && ( get_current_user_id() == $post->post_author || user_can( $post->post_author, 'manage_options' ) ) )
		return $data;

	$featured_id = es_get_featured_term_id();
	$flashes = es_get_flash_instance( 'subscriptions' );
	$labels = ! empty( $data['es_label'] ) ? $data['es_label'] : array();
	$key = array_search( $featured_id, $labels );

	if ( ! $featured_id || $post->post_status != 'publish' ) return $data;

	if ( es_user_has_active_subscription( $post->post_author ) ) {
		$subscription = es_get_user_subscription( $post->post_author );

		if ( $property->is_featured() && ( ! in_array( $featured_id, $labels ) ) ) {
			$subscription->decrease_featured();
		}

		if ( ! $property->is_featured() && in_array( $featured_id, $labels ) ) {
			if ( es_user_can_publish_featured_listings( $post->post_author ) ) {
				$subscription->increase_featured();
			} else {
				if ( $key !== false ) {
					unset( $labels[ $key ] );
					$data['es_label'] = $labels;

					$flashes->set_message( 'Your subscription plan doesn\'t allow to publish more featured listings.', 'es' );
				}
			}
		}
	} else {
		if ( $key !== false ) {
			unset( $labels[ $key ] );
			$data['es_label'] = $labels;

			$flashes->set_message( 'Your subscription plan doesn\'t allow to publish more featured listings.', 'es' );
		}
	}
	return $data;
}
add_filter( 'es_property_before_save_data', 'es_subscription_check_featured', 10, 2 );

/**
 * @param $response
 *
 * @return mixed
 */
function es_pm_subscriptions_messages( $response ) {
	$flashes = es_get_flash_instance( 'subscriptions' );

	if ( $messages = $flashes->get_messages() ) {
		$response['form_messages'] = $messages;
	}

	return $response;
}
add_filter( 'es_pm_ajax_save_property_handler_response', 'es_pm_subscriptions_messages' );

/**
 * Enable \ disable Featured label based on subscription params.
 *
 * @param $field_config
 * @param $field_key
 *
 * @return array
 */
function es_subscription_alter_featured_field( $field_config, $field_key ) {
	if ( 'es_label' == $field_key && ! es_user_can_publish_featured_listings() ) {
		if ( $featured_term_id = es_get_featured_term_id() ) {
			$property = es_get_the_property();

			if ( ! es_is_property( $property->get_id() ) ) {
				$property = es_get_property( -1 );
			}

			$labels = $property->es_label;
			$labels = $labels ? $labels : array();

			if ( ! in_array( $featured_term_id, $labels ) ) {
				$field_config['items_attributes'][ $featured_term_id ] = array(
					'attributes' => array(
						'disabled' => 'disabled',
					),
					'description' => "<div class='es-notify es-notify--warning'>" . __( 'Your subscription plan doesn\'t allow to publish more featured listings.', 'es' ) . "</div>"
				);
			}
		}
	}

	return $field_config;
}
add_filter( 'es_pm_before_render_property_field', 'es_subscription_alter_featured_field', 10, 2 );

/**
 * Add wp schedules for manage expired subscriptions
 *
 * return @void
 */
function es_subscriptions_add_schedules() {
	if ( ! wp_next_scheduled( 'es_subscriptions_check_expired' ) ) {
		wp_schedule_event( time(), '5min', 'es_subscriptions_check_expired' );
	}
}
add_action( 'wp', 'es_subscriptions_add_schedules' );

/**
 * Check for expired subscriptions.
 *
 * @return void
 */
function es_subscriptions_check_paypal_expired() {
	if ( ! ests( 'is_subscriptions_enabled' ) || ! ( $paypal_api = es_paypal_get_api_instance() ) ) return;

	$orders_ids = get_posts( array(
		'post_status' => 'private',
		'post_type' => 'es_order',
		'posts_per_page' => apply_filters( 'es_subscriptions_paypal_schedule_check_num', 5 ),
		'fields' => 'ids',
		'order' => 'ASC',
		'orderby' => 'modified',
		'meta_query' => array(
			array(
				'key' => 'es_order_payment_method',
				'value' => 'paypal-subscriptions',
			),
			array(
				'key' => 'es_order_status',
				'value' => Es_Order::ORDER_STATUS_CLOSED,
				'compare' => '!=',
			)
		),
	) );

	if ( ! empty( $orders_ids ) ) {
		foreach ( $orders_ids as $order_id ) {
			$order = es_get_order( $order_id );

			if ( ! empty( $order->payment_data['id'] ) ) {
				$payment_data = $order->payment_data;

				try {
					$subscription = $paypal_api->subscription()->get( $order->payment_data['id'] );

					if ( ! empty( $subscription['billing_info']['next_billing_time'] ) ) {
						$order->save_field_value( 'next_payment_time', strtotime( $subscription['billing_info']['next_billing_time'] ) );
					}

					if ( $payment_data['status_update_time'] != $subscription['status_update_time'] ) {
						if ( ! empty( $subscription['status'] ) ) {
							switch ( $subscription['status'] ) {
								case 'APPROVAL_PENDING':
									break;

								case 'ACTIVE':
									$order->active();
									break;

								case 'SUSPENDED':
									if ( $order->next_payment_time <= time() ) {
										$order->suspend();
									}
									break;

								case 'EXPIRED':
								case 'CANCELLED':
									if ( $order->next_payment_time <= time() ) {
										es_cancel_user_subscription( $order->get_id() );
										$order->close();
									}

									if ( 'EXPIRED' == $subscription['status'] ) {
										$user_instance = es_get_user_entity( $order->user_id );
										$email_instance = es_get_email_instance( 'subscription_expired', array(
											'agent_name' => $user_instance->get_full_name(),
											'user_entity' => $user_instance
										) );

										if ( $email_instance && $email_instance::is_active() ) {
											$email_instance->send( $user_instance->get_email() );
										}
									}

									break;
							}
						}
					}

					if ( $subscription['update_time'] != $payment_data['update_time'] ) {
						$order->save_field_value( 'payment_data', $subscription );
					}
				} catch ( Exception $e ) {

				}

				$post_modified     = current_time( 'mysql' );
				$post_modified_gmt = current_time( 'mysql', 1 );

				global $wpdb;
				$wpdb->update( $wpdb->posts, array( 'post_modified' => $post_modified, 'post_modified_gmt' => $post_modified_gmt ), array( 'ID' => $order_id ) );
			}
		}
	}
}
add_action( 'es_subscriptions_check_expired', 'es_subscriptions_check_paypal_expired' );

/**
 * @param $post_id
 */
function es_subscriptions_hit_limits_email( $post_id ) {
	$post = get_post( $post_id );

	if ( ! user_can( $post->post_author, 'administrator' ) && user_can( $post->post_author, 'publish_es_properties' ) && ! es_user_can_publish_listings( $post->post_author ) ) {
		$hit_limit_sent = get_user_meta( $post->post_author, 'es_hit_limit_email_reset', true );

		if ( es_user_has_active_subscription( $post->post_author ) && $hit_limit_sent ) {
			$user_entity = es_get_user_entity( $post->post_author );
			es_send_email( 'subscription_upgrade', $user_entity->get_email(), array(
				'agent_name' => $user_entity->get_full_name(),
				'user_entity' => $user_entity,
			) );
			delete_user_meta( $post->post_author, 'es_hit_limit_email_reset' );
		}
	}
}
add_action( 'es_after_save_property', 'es_subscriptions_hit_limits_email', 20 );
