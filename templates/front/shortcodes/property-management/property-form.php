<div class="es-wrap et_smooth_scroll_disabled">
    <div class="es-property-management es-property-management--form content-font" id="es-property-management-form">
        <?php $flashes = es_get_flash_instance( 'prop-management' ); $flashes->render_messages(); ?>
        <form action="" method="POST" class="es-property-form js-es-ajax-form" enctype="multipart/form-data">
            <?php
            $btn_label = __( 'Submit new home', 'es' );
            $next_label = _x( 'Next', 'next tab button label', 'es' );
            $back_label = _x( 'Back', 'next tab button label', 'es' );

            if ( ! empty( $id ) ) $btn_label = __( 'Save home', 'es' );

            $manage_buttons = "<div class='es-manage-buttons'>
                <a href='#' class='es-secondary-color js-es-nav-link' data-type='prev'><span class='es-icon es-icon_chevron-left'></span>{$back_label}</a>
                <button type='button' data-type='next' class='es-btn es-btn--primary es-btn--right-icon js-es-nav-link'>{$next_label}<span class='es-icon es-icon_arrow-right'></span></button>
                <button type='submit' class='es-btn es-btn--primary js-es-save-listing-secondary es-hidden'>{$btn_label}</button>
                <button type='submit' class='es-btn es-btn--default js-es-save-listing'>{$btn_label}</button></div>";

            es_framework_view_render( 'tabs', array(
                'tabs' => $tabs,
                'use_data_attr_tab_id' => true,
                'show_logo' => false,
                'before_content_tabs' => "<h3 class='heading-font es-tabs__title'>" . __( 'Add new home', 'es' ) . "</h3>",
                'after_content_tabs' => $manage_buttons
            ) ); ?>

            <input type="hidden" name="post_id" value="<?php echo $id; ?>"/>
            <?php wp_nonce_field( 'es_save_property', 'es_save_property' ); ?>
            <?php wp_nonce_field( 'es_save_property_ajax', 'es_save_property_ajax' ); ?>
            <input type="hidden" name="action" value="es_ajax_save_property"/>
        </form>
    </div>
</div>
