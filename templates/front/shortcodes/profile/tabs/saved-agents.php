<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
	<?php if ( ! empty( $tabs[ $current_tab ]['label'] ) ) : ?>
		<h2 class="heading-font"><?php echo $tabs[ $current_tab ]['label']; ?></h2>
	<?php endif;

	$have_posts = false;
	$wishlist = es_get_wishlist_instance( 'agent' );
	$items = $wishlist->get_items_ids();
	/** @var Es_My_Listing_Shortcode $listings */
	$listings = es_get_shortcode_instance( 'es_my_agents', array(
        'layout' => 'grid',
		'disable_navbar' => true,
		'ajax_response_mode' => true,
		'wishlist_confirm' => true,
		'posts_per_page' => ests( 'wishlist_agents_per_page' ),
		'agents_id' => $items ? implode( ',', $items ) : -1,
	) );

	$query = $listings->get_query();
	$have_posts = $query->have_posts(); ?>

	<div class="js-es-no-posts <?php echo ! $have_posts ? '' : 'es-hidden'; ?>">
		<p class="es-subtitle"><?php _e( 'You haven’t saved any agents yet.', 'es' ); ?></p>
		<p><?php _e( 'Start searching for agents to add now.', 'es' ); ?></p>
		<?php if ( $url = es_get_search_page_url() ) : ?>
			<a href="<?php echo $url; ?>" class="es-btn es-btn--secondary">
				<span class="es-icon es-icon_search"></span><?php _e( 'Go to search', 'es' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( $have_posts ) : ?>
		<?php echo $listings->get_content(); ?>
	<?php endif; ?>
</div>
