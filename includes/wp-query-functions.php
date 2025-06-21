<?php

/**
 * Return profile requsts tab wp query for requests.
 *
 * @return WP_Query
 */
function es_profile_requests_get_wp_query() {
	$meta_query = null;

	$q = es_get( 'q' );

	$post_status = es_get( 'status' );
	$post_status = $post_status == 'draft' ? $post_status : 'publish';

	$query_args = array(
		'post_type' => 'request',
		'posts_per_page' => ests( 'requests_per_page' ),
		'post_status' => $post_status,
	);

	if ( ! empty( $q ) ) {
		$meta_query['keywords'] = array(
			'key' => 'es_request_keywords',
			'value' => $q,
			'compare' => 'LIKE'
		);
	}

	if ( ! current_user_can( 'administrator' ) ) {
		$user = es_get_user_entity( get_current_user_id() );

		if ( $user->post_id ) {
			$meta_query['user_attached_to_request'] = array(
				'relation' => 'OR',
				array(
					'key' => 'es_request_recipient_entity_id',
					'value' => $user->post_id,
					'type' => 'NUMERIC',
				),
				array(
					'key' => 'es_request_post_id',
					'value' => $user->post_id,
					'type' => 'NUMERIC',
				),
			);
		}
	}

	$query_args['meta_query'] = $meta_query;

	$query_args = apply_filters( 'es_profile_requests_get_query_args', $query_args );

	return new WP_Query( $query_args );
}
