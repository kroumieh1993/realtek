<?php

/**
 * @var $current_tab string
 * @var $user_entity Es_User
 */

$profile_form_tab = es_get( 'screen' );
$fbuilder_sections = es_get_sections_builder_instance();
$fbuilder_fields = es_get_fields_builder_instance();
$networks = es_get_social_networks_list();
$profile_form_tab = $profile_form_tab ? $profile_form_tab : 'details';
$skeleton = "{before}
                   <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                       <label for='{id}'>{label}{caption}<div class='es-input__wrap'>{input}</div>{description}</label>
                   </div>
               {after}";
$agent_user = es_get_agent_user( get_current_user_id() );
$temp_post = get_post();
global $post;

if ( $agent_user->post_id ) {
	$post = get_post( $agent_user->post_id  );
} else {
	$post = null;
} ?>

<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
	<h2 class="heading-font"><?php _e( 'Edit profile', 'es' ); ?></h2>

	<ul class="es-nav-tab" style="margin-bottom: 30px;">
		<li class="es-nav-tab__item<?php es_active_class( $profile_form_tab, 'details', 'es-nav-tab__item--active' ); ?>">
			<a href="<?php echo add_query_arg( 'screen', 'details' ); ?>">
				<?php _e( 'Profile details', 'es' ); ?>
			</a>
		</li>
        <?php if ( ! es_is_user_registered_via_social_network( $user_entity->get_id() ) ) : ?>
            <li class="es-nav-tab__item<?php es_active_class( $profile_form_tab, 'password', 'es-nav-tab__item--active' ); ?>">
                <a href="<?php echo add_query_arg( 'screen', 'password' ); ?>">
                    <?php _e( 'Change password', 'es' ); ?>
                </a>
            </li>
        <?php endif; ?>
	</ul>

    <?php if ( 'details' == $profile_form_tab ) : ?>
        <form action="#" method="POST" enctype="multipart/form-data" class="js-es-ajax-form js-es-form-enable-on-change js-es-confirm-by-pwd">
            <?php es_framework_field_render( 'avatar_id', array(
                'type' => 'avatar',
                'image' => get_avatar( $user_entity->get_id(), 96 ),
                'upload_button_classes' => 'es-btn es-btn--secondary es-btn--bordered es-btn--upload-photo',
                'upload_button_label' => __( 'Upload profile photo', 'es' ),
                'exists_upload_button_label' => __( 'Upload new photo', 'es' ),
                'default_image' => "<img src='" . es_user_get_default_image_url( $user_entity->get_id() ) . "' class='avatar'>",
                'value' => $user_entity->avatar_id,
            ) );

            if ( ! es_is_user_registered_via_social_network( $user_entity->get_id() ) ) :
                es_framework_field_render( 'es_confirm_password', array(
                    'label'       => __( 'Confirm with password', 'es' ),
                    'type'        => 'password',
                    'attributes' => array(
                        'required' => 'required',
                    ),
                    'wrapper_class' => 'js-es-confirm-field es-hidden',
                    'skeleton' => $skeleton,
                ) );
            endif; ?>

            <?php foreach ( $fbuilder_sections::get_items( 'agent' ) as $section_id => $config ) :
                $fields = $fbuilder_fields::get_frontend_tab_fields( $section_id, 'agent' );
                if ( ! $fields ) continue; ?>

                <h3 class="heading-font es-profile-heading"><?php echo $config['label']; ?></h3>

                <?php foreach ( $fields as $field_key => $field_config ) : ?>
                    <?php es_agent_field_render( $field_key, $field_config ); ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <input type="hidden" name="action" value="es_profile_save_info"/>
            <?php wp_nonce_field( 'es_profile_save_info', 'es_profile_nonce' ); ?>
            <button type="submit" class="es-btn es-btn--primary" disabled><?php _e( 'Save changes', 'es' ); ?></button>
        </form>
    <?php endif; ?>

    <?php if ( $profile_form_tab == 'password' && ! es_is_user_registered_via_social_network( $user_entity->get_id() )  ) : ?>
	    <?php include es_locate_template( 'front/shortcodes/profile/partials/change-password-form.php' ); ?>
    <?php endif; ?>

    <?php do_action( 'es_profile_after_user_tab' ); ?>
</div>
<?php $post = $temp_post;
