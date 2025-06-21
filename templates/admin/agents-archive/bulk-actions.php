<div class="es-actions__container es-hidden">
	<?php do_action( 'es_agent_before_bulk_actions' ); ?>
	<div class="es-selected">
		<span class="js-es-selected-num es-num">0</span>
		<b><?php _e( 'selected agents', 'es' ); ?></b>
	</div>
	<ul class="es-actions-buttons">
        <li><a href="#" data-action="publish" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-action-bulk"><span class="es-icon es-icon_eye" style="position: relative;top: 4px;"></span><?php _e( 'Publish', 'es' ); ?></a></li>
        <li><a href="#" data-action="draft" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-action-bulk"><span class="es-icon es-icon_eye-no"></span><?php _e( 'Draft', 'es' ); ?></a></li>
		<li><a href="#" data-action="delete" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>" class="js-es-delete-bulk"><span class="es-icon es-icon_trash"></span><?php _e( 'Delete', 'es' ); ?></a></li>
	</ul>
	<?php do_action( 'es_agent_after_bulk_actions' ); ?>
</div>
