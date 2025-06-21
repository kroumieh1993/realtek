<div class="es-hit-limit">
    <span class="es-icon es-icon_info"></span>
    <p class="es-subtitle" class="heading-font">
        <?php _e( 'You’re hit your listing limits.', 'es' ); ?><br>
        <?php printf( wp_kses( __( 'Please <a href="%s">upgrade your plan</a> to publish more listings.', 'es' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( es_get_page_url( 'pricing' ) ) ); ?>
    </p>
</div>
