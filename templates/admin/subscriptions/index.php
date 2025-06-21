<div class="es-navbar">
	<ul>
		<?php

		/**
		 * @var $current_tab string
		 * @var $tabs array
		 */

        foreach ( $tabs as $id => $tab ) :
            if ( empty( $tab['label'] ) || ! empty( $tab['navbar_hide'] ) ) continue;
			$current_tab_temp = $current_tab == 'plan-form' ? 'subscriptions' : $current_tab; ?>
			<li class="<?php es_active_class( $current_tab_temp, $id, 'active' ); ?>">
                <a href="<?php echo add_query_arg( array( 'tab' => $id, 'plan_id' => false ) ); ?>">
                    <?php echo $tab['label']; ?>
                </a>
            </li>
		<?php endforeach; ?>
	</ul>

    <?php if ( $url = es_get_page_url( 'pricing' ) ) : ?>
	    <a href="<?php echo $url; ?>" class="es-btn es-btn--align-right es-btn--secondary" target="_blank"><?php _e( 'Preview', 'es' ); ?></a>
    <?php endif; ?>
</div>

<div class="es-wrap">
    <div class="js-es-notifications">
        <?php $flashes = es_get_flash_instance( 'admin_subscriptions' );
        if ( $messages_list = $flashes->get_messages() ) :
            foreach ( $messages_list as $type => $messages ) {
                if ( ! empty( $messages ) ) {
                    foreach ( $messages as $message ) {
                        echo es_get_notification_markup( $message, $type );
                    }
                }
	            $flashes->clean_container();
            }
        endif;?>
    </div>
    <div class="es-wrap__inner">
        <h2><?php echo $tabs[ $current_tab ]['label']; ?></h2>
        <?php do_action( 'es_admin_subscriptions_before_tab_content', $current_tab ); ?>
        <?php if ( $tabs[ $current_tab ]['template'] ) : ?>
            <?php include $tabs[ $current_tab ]['template']; ?>
        <?php else : ?>
            <?php do_action( 'es_admin_subscriptions_tab_content', $current_tab ); ?>
        <?php endif; ?>
        <?php do_action( 'es_admin_subscriptions_after_tab_content', $current_tab ); ?>
    </div>
</div>
