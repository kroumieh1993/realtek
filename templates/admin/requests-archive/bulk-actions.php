<div class="es-actions__container es-hidden">
	<?php do_action( 'es_request_before_bulk_actions' ); ?>
	<div class="es-selected">
		<span class="js-es-selected-num es-num">0</span>
		<b><?php _e( 'selected requests', 'es' ); ?></b>
	</div>
	<ul class="es-actions-buttons">
		<li><a href="#" data-action="viewed" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-action-bulk"><span class="es-icon es-icon_eye"></span><?php _e( 'Mark as read', 'es' ); ?></a></li>
		<li><a href="#" data-action="draft" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-action-bulk"><span class="es-icon es-icon_eye-no"></span><?php _e( 'Archive', 'es' ); ?></a></li>
		<li><a href="#" data-action="delete" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-delete-bulk"><span class="es-icon es-icon_trash"></span><?php _e( 'Delete', 'es' ); ?></a></li>
	</ul>
	<?php do_action( 'es_request_after_bulk_actions' ); ?>
</div>
