<div class="es-actions-container es-hidden">
    <?php do_action( 'es_pm_before_bulk_actions' ); ?>
    <div class="es-selected">
		<span class="js-es-selected-num es-num">0</span>
		<b><?php _e( 'selected listings', 'es' ); ?></b>
	</div>
	<ul class="es-actions-buttons">
        <?php if ( current_user_can( 'delete_es_properties' ) ) : ?>
            <li><a href="#" data-action="delete" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-delete-bulk"><span class="es-icon es-icon_trash"></span><?php _e( 'Delete', 'es' ); ?></a></li>
        <?php endif; ?>
        <?php if ( current_user_can( 'copy_properties' ) ) : ?>
		    <li><a href="#" data-action="copy" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-duplicate-bulk"><span class="es-icon es-icon_copy"></span><?php _e( 'Copy', 'es' ); ?></a></li>
        <?php endif; ?>
	</ul>
    <?php do_action( 'es_pm_after_bulk_actions' ); ?>
</div>
