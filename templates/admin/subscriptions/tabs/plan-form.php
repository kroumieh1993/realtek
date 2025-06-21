<?php $GLOBALS['plan_id'] = intval( filter_input( INPUT_GET, 'plan_id' ) ); global $plan_id; ?>
<form method="post" class="js-es-plan-form">
    <div class="es-fields-wrap">
        <?php es_plan_field_render( 'name', array(
            'label' => __( 'Plan name', 'es' ),
            'type' => 'text',
            'attributes' => array(
                'class' => 'js-es-plan-name',
                'maxlength' => 50,
                'required' => 'required',
            ),
            'enable_counter' => true,
        ) );

        es_plan_field_render( 'is_label_active', array(
            'label' => __( 'Insert label', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
                'data-toggle-container' => '#label-container'
            )
        ) );

        es_plan_field_render( 'label_text', array(
            'before' => '<div class="es-field-row" id="label-container">',
            'label' => __( 'Label text', 'es' ),
            'type' => 'text',
            'attributes' => array(
                'maxlength' => 16,
            )
        ) );

        es_plan_field_render( 'label_color', array(
            'label' => __( 'Label color', 'es' ),
            'type' => 'color',
            'after' => '</div>',
            'wrapper_class' => 'es-field--color--break-label',
        ) );

        es_plan_field_render( 'description', array(
            'label' => __( 'Description', 'es' ),
            'type' => 'textarea',
            'enable_counter' => true,
            'attributes' => array(
                'maxlength' => 100
            )
        ) );

        es_plan_field_render( 'is_default', array(
            'label' => __( 'Default plan', 'es' ),
            'type' => 'switcher',
        ) );

        es_plan_field_render( 'is_highlighted', array(
            'label' => __( 'Highlight plan block with color', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
                'data-toggle-container' => '.es-field__block_color'
            )
        ) );

        es_plan_field_render( 'block_color', array(
            'label' => __( 'Block color', 'es' ),
            'type' => 'color',
            'wrapper_class' => 'es-field--color--break-label',
        ) ); ?>
    </div>

    <div class="es-inline-fields">
        <?php es_plan_field_render( 'is_basic_listings_limited', array(
            'before' => '<div>',
            'label' => __( 'Basic listings', 'es' ),
            'type' => 'radio-bordered',
            'options' => array(
                1 => __( 'Limited', 'es' ),
                0 => __( 'Unlimited', 'es' ),
            ),
            'attributes' => array(
                'class' => 'js-es-is-limited',
                'data-field' => '.es-field__basic_listings_limit',
            ),
        ) );

        es_plan_field_render( 'basic_listings_limit', array(
            'label' => __( 'Number of listings', 'es' ),
            'type' => 'number',
            'attributes' => array(
                'min' => 0,
                'required' => 'required',
            ),
            'after' => '</div>'
        ) );

        es_plan_field_render( 'is_featured_listings_limited', array(
            'before' => '<div>',
            'label' => __( 'Featured listings', 'es' ),
            'type' => 'radio-bordered',
            'options' => array(
                1 => __( 'Limited', 'es' ),
                0 => __( 'Unlimited', 'es' ),
            ),
            'attributes' => array(
                'class' => 'js-es-is-limited',
                'data-field' => '.es-field__featured_listings_limit',
            ),
        ) );

        es_plan_field_render( 'featured_listings_limit', array(
            'label' => __( 'Number of listings', 'es' ),
            'type' => 'number',
            'attributes' => array(
                'min' => 0,
                'required' => 'required',
            ),
            'after' => '</div>'
        ) );

        ?>
    </div>

    <div class="es-fields-wrap">
        <?php es_plan_field_render( 'is_free_plan_enabled', array(
            'label' => __( 'Make plan FREE', 'es' ),
            'type' => 'switcher',
            'description' => __( 'FREE plan can be default', 'es' ),
            'attributes' => array(
                'data-inactive-container' => '.es-field__monthly_price, .es-field__annual_price',
                'data-toggle-disabled' => '#es-field-is_free_trial_enabled'
            )
        ) );

        es_plan_field_render( 'is_free_trial_enabled', array(
            'label' => __( 'Enable FREE trial', 'es' ),
            'type' => 'switcher',
            'attributes' => array(
                'data-revert-toggler' => 1,
                'data-toggle-disabled' => '#es-field-start_trial_button, #es-field-start_trial_button_caption',
                'data-toggle-container' => '.es-field__trial_period'
            ),
        ) );

        es_plan_field_render( 'trial_period', array(
	        'label' => __( 'Trial period', 'es' ),
	        'type' => 'radio-bordered',
	        'options' => array(
		        'week' => __( 'Weekly', 'es' ),
		        'month' => __( 'Monthly', 'es' ),
		        'year' => __( 'Yearly', 'es' )
	        ),
        ) );

        es_plan_field_render( 'monthly_price', array(
            'label' => __( 'Monthly price', 'es' ),
            'type' => 'number',
            'attributes' => array(
                'step' => 'any',
                'required' => 'required',
                'placeholder' => ests_label( 'currency_sign' )
            )
        ) );

        es_plan_field_render( 'annual_price', array(
            'label' => __( 'Annual price', 'es' ),
            'type' => 'number',
            'attributes' => array(
                'step' => 'any',
                'placeholder' => ests_label( 'currency_sign' )
            )
        ) ); ?>
    </div>

    <div class="es-inline-fields">
        <?php es_plan_field_render( 'main_button', array(
            'before' => '<div>',
            'label' => __( 'Main button', 'es' ),
            'type' => 'text',
            'attributes' => array(
	            'required' => 'required'
            ),
        ) );

        es_plan_field_render( 'main_button_caption', array(
            'label' => __( 'Caption text', 'es' ),
            'type' => 'textarea',
            'enable_counter' => true,
            'attributes' => array(
                'maxlength' => 60,
            ),
            'after' => '</div>',
        ) );

        es_plan_field_render( 'start_trial_button', array(
            'before' => '<div>',
            'label' => __( 'Button to start trial', 'es' ),
            'type' => 'text',
            'attributes' => array(
	            'required' => 'required'
            ),
        ) );

        es_plan_field_render( 'start_trial_button_caption', array(
            'label' => __( 'Caption text', 'es' ),
            'type' => 'textarea',
            'enable_counter' => true,
            'attributes' => array(
                'maxlength' => 60,
            ),
            'after' => '</div>',
        ) ); ?>

        <input type="hidden" class="js-es-plan-id" name="es_plan[ID]" value="<?php echo $plan_id; ?>"/>
    </div>

    <?php wp_nonce_field( 'es_save_subscription_plan' ); ?>

    <input type="hidden" name="action" value="es_save_subscription_plan"/>
    <button type="submit" style="margin-top: 24px;" class="es-btn es-btn--primary es-btn--large es-btn--save js-es-save-plan">
	    <?php _e( 'Save changes', 'es' ); ?>
    </button>
</form>
