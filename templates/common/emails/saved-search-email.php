<h1 style="font-size: 23px; font-family: 'Open Sans'; font-weight: 400; color: rgb(66, 66, 66);"><?php _e( 'These new homes match your saved search', 'es' ); ?>:</h1>

<?php

/**
 * @var $saved_search Es_Saved_Search
 * @var $properties Int[]
 */

if ( $query = $saved_search->get_formatted_query_string() ) : ?>
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
                <span style="display: inline-block; padding: 10px; border-radius: 2px; font-weight: 300; background-color: rgb(250, 250, 250); font-size: 16px; font-family: 'Open Sans'; color: rgb(189, 189, 189);">
                    <?php echo $query; ?>
                </span>
			</td>
		</tr>
	</table>
<?php endif; ?>

	<style>
        .property-thumbnail img {
            width: 100%;
            display: block;
            height: auto;
        }
	</style>

	<table cellpadding="10" cellspacing="0" border="0">
		<?php

        $properties = new WP_Query( array(
            'post_type' => 'properties',
            'post__in' => $properties,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ) );

        if ( $properties->have_posts() ) : $j = 0; ?>
			<?php while( $properties->have_posts() ) : $i = 0;
				$properties->the_post();

				$property = es_get_property( get_the_ID() ); ?>

				<?php if ( ! $j ) : ?><tr><?php endif; ?>

				<td valign="top" style="width: 50%;">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #ebeaea;">
						<tr>
							<td class="property-thumbnail">
                                <img alt="<?php es_the_title(); ?>" src="<?php echo es_get_the_featured_image_url( 'es-image-size-archive', get_the_ID() ); ?>"/>
							</td>
						</tr>
						<tr>
							<td style="padding: 20px;">
								<?php es_the_title( '<h2 style="margin: 0; font-size: 16px; font-family: \'Open Sans\', Arial; padding-bottom: 6px; color: rgb(66, 66, 66); font-weight: 400; line-height: 1;">', '</h2>' ); ?>
								<?php if ( $price = es_get_the_formatted_field( 'price' ) ) : ?>
									<h3 style='font-size: 16px; margin: 0; letter-spacing: 1px; font-weight: 400; font-family: "Open Sans"; color: rgb(66, 66, 66); text-transform: uppercase; line-height: 1;'><?php echo $price; ?></h3>
								<?php endif; ?>
								<table style="padding-top: 8px; padding-bottom: 15px; margin-left: -3px;">
									<?php if ( $address = es_get_the_address() ) : ?>
										<tr>
											<td colspan="2" style="font-size: 12px; font-weight: 200; font-family: 'Open Sans'; color: rgb(108, 108, 108);"><?php _e( 'Address', 'es' ); ?>: <?php echo $address; ?></td>
										</tr>
									<?php endif; ?>
									<?php if ( $property->bedrooms || $property->bathrooms ) : $cols = 0; ?>
										<tr>
											<?php if ( $property->bedrooms ) : $i++; ?>
												<td style="font-size: 12px; font-weight: 200; font-family: 'Open Sans'; color: rgb(108, 108, 108);"><?php _e( 'Bedrooms', 'es' ); ?>: <?php echo $property->bedrooms; ?></td>
											<?php endif; ?>
											<?php if ( $property->bathrooms ) : $i++ ?>
												<td style="font-size: 12px; font-weight: 200; font-family: 'Open Sans'; color: rgb(108, 108, 108); "><?php _e( 'Bathrooms', 'es' ); ?>: <?php echo $property->bathrooms; ?></td>
											<?php endif; ?>

											<?php if ( $i < 2 ) : ?><td></td><?php endif; ?>
										</tr>
									<?php endif; ?>
									<?php if ( $area = es_get_the_formatted_field( 'area' ) ) : ?>
										<tr>
											<td colspan="2" style="font-size: 12px; font-weight: 200; font-family: 'Open Sans'; color: rgb(108, 108, 108);"><?php _e( 'Area', 'es' ); ?>: <?php echo $area; ?></td>
										</tr>
									<?php endif; ?>
								</table>

								<a href="<?php echo get_permalink( $post ); ?>" style="background-color: <?php echo ests( 'main_color' ); ?>; border-radius: 2px; font-size: 14px; font-family: 'Open Sans'; color: rgb(255, 255, 255); line-height: 2.5; text-decoration: none; display: block; width: 100%; text-align: center;" target="_blank">
									<?php _e( 'View More Details', 'es' ); ?>
								</a>
							</td>
						</tr>
					</table>
				</td>

				<?php if ( $j == 1 ) : $j = -1; ?></tr><?php endif; ?>

				<?php $j++; endwhile; wp_reset_postdata(); ?>
		<?php endif; ?>
	</table>

<?php if ( ( $search_url = es_get_search_page_url() ) && ( $search_data = $saved_search->search_data ) ) : ?>
	<table style="width: 100%; margin-top: 20px;" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				<a style="border:1px solid <?php echo ests( 'main_color' ); ?>; border-radius: 2px; font-size: 14px; font-family: 'Open Sans'; color: <?php echo ests( 'main_color' ); ?>; line-height: 2.5; text-decoration: none; display: block; width: 100%; text-align: center;" target="_blank" href="<?php echo add_query_arg( $search_data, $search_url ); ?>"><?php _e( 'See All', 'es' ); ?></a>
			</td>
		</tr>
	</table>
<?php endif;
