<?php if ( ests( 'is_agents_register_enabled' ) && ests( 'is_agents_enabled' ) ) : ?>
	<div class="es-auth-footer">
		<div class="es-auth-footer__inner">
			<div class="es-auth-footer__left">
				<span class="es-icon es-icon_glasses es-secondary-color"></span>
				<div class="es-auth-footer__content">
					<b><?php _e( 'Real estate professional?', 'es' ); ?></b>
					<p><?php _e( 'Manage your listings, profile and more.', 'es' ); ?></p>
				</div>
			</div>
			<a href="#" data-auth-item="agent-register-buttons" class="js-es-auth-item__switcher es-btn es-btn--secondary es-btn--bordered"><?php _e( 'Sign up as agent', 'es' ); ?></a>
		</div>
	</div>
<?php endif; ?>