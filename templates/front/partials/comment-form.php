<div class="es-review-form es-review-form--<?php the_ID(); ?> es-review-form--<?php echo $entity::get_entity_name(); ?>">
    <div class="es-review-form__entity-info">
        <?php if ( method_exists( $entity, 'get_avatar' ) ) : ?>
            <div class="es-review-form__avatar"><?php echo $entity->get_avatar(); ?></div>
        <?php endif; ?>

        <h3 class="es-review-form__title"><?php printf( __( 'Write a review about %s', 'es' ), get_the_title( $entity->get_id() ) ); ?></h3>
        <p class="es-review-form__subtitle"><?php _e( 'Your first-hand experience really help other clients. Thanks!', 'es' ); ?></p>
    </div>

	<?php
    $req      = get_option( 'require_name_email' );
	$commenter     = wp_get_current_commenter();

    comment_form( array(
		'comment_field' => es_framework_get_field_html( 'comment', array(
			'label' => __( 'Your review *', 'es' ),
			'type' => 'textarea',
			'enable_counter' => true,
			'attributes' => array(
                'placeholder' => __( 'How was their knowledge of the market, timeliness in responding… anything else?', 'es' ),
				'maxlength' => 500,
				'required' => 'required',
				'rows' => 4,
				'id' => 'comment',
			),
		) ),
        'fields' => array(
	        'author' => es_framework_get_field_html( 'author', array(
		        'label' => __( 'Name' ),
		        'value' => $commenter['comment_author'],
		        'attributes' => array(
			        'required' => $req ? 'required' : false,
			        'size' => 30,
			        'maxlength' => 245,
		        )
	        ) ),
            'email' => es_framework_get_field_html( 'email', array(
	            'label' => __( 'Email' ),
                'type' => 'email',
	            'value' => $commenter['comment_author_email'],
	            'attributes' => array(
		            'required' => $req ? 'required' : false,
                    'size' => 30,
                    'maxlength' => 100,
	            )
            ) ),
            'url' => es_framework_get_field_html( 'url', array(
                'label' => __( 'Website' ),
                'type' => 'url',
                'value' => $commenter['comment_author_url'],
                'attributes' => array(
	                'size' => 30,
	                'maxlength' => 200,
                )
            ) ),
        ),
		'label_submit' => __( 'Submit your review', 'es' ),
		'class_submit' => 'es-btn es-btn--primary',
		'logged_in_as' => null,
		'must_log_in' => null,
		'title_reply' => null,
		'title_reply_to' => null,
		'title_reply_before' => null,
		'title_reply_after' => null,
	), $entity->get_id() );

	do_action( 'es_privacy_policy', 'comment_form' ); ?>
</div>
