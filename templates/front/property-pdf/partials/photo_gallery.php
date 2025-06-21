<style>
    #photo {
        background-image-resize: 6;
        height: 450px;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        width: 100%;
    }

    .thumb-photo {
        height: 153px;
        background-repeat: no-repeat;
        background-image-resize: 6;
    }

    .es-price-container {
        width: 200px;
        padding: 0 25px;
    }

    #header {
        width: 100%;
    }

    #header-address {
        background-color: <?php echo ests( 'main_color' ) ?>;
        padding: 15px;
        color: #fff;
    }
</style>

<?php $property = es_get_the_property();
$images_count = is_array( $property->gallery ) ? count( $property->gallery ) : 1;
$images_count = $images_count > 3 ? 3 : $images_count; ?>

<table width="100%" id="header" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="header__title" width="75%" style="padding: 9px 0 9px 9px;">
            <table cellspacing="0" cellpadding="1" class="sup-line" style="margin-bottom: 9px;"><tr><td></td></tr></table>
			<?php es_the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <span class="site-url"><?php echo home_url(); ?></span>
        </td>
		<?php
		es_the_address( '<td style="padding: 0 10px 0;" width="25%" valign="top"><table border="0" cellspacing="0" cellpadding="0" id="header-address"><tr><td>', '</td></tr></table></td>' );
		?>
    </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="75%" valign="top" style="padding: 9px 0 9px 9px;">
            <table border="0" width="100%" cellpadding="0" cellspacing="1">
                <tr>
                    <td id="photo" valign="bottom" style="background-image: url('<?php echo es_get_the_featured_image_url('large'); ?>')">
						<?php es_the_price( '<table class="es-price-container"><tr><td>', '</td></tr></table>' ); ?>
                    </td>
                </tr>
            </table>
        </td>
		<?php if ( $images_count > 1 ) : ?>
            <td width="25%" valign="top">
                <table border="0" width="100%" cellpadding="0" cellspacing="10">
					<?php foreach ( $property->gallery as $key => $value ) : if ( ! $key ) continue; if ( ! $images_count ) break; ?>
                        <tr>
                            <td class="thumb-photo" style="background-image: url('<?php echo wp_get_attachment_image_url( $value ); ?>')"></td>
                        </tr>
						<?php $images_count--; endforeach; ?>
                </table>
            </td>
		<?php endif; ?>
    </tr>
</table>

<table width="100%" id="content" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" class="" width="75%" style="padding: 10px 0 10px 10px;">
            <table>
				<?php if ( $excerpt = get_the_excerpt() ) : ?>
                    <tr>
                        <td class="property-excerpt">
							<?php echo get_the_excerpt(); ?>
                        </td>
                    </tr>
				<?php endif; ?>
				<?php if ( $property->agent_id ) : ?>
                    <tr>
                        <td>
							<?php foreach ( $property->agent_id as $agent_id ) : ?>
								<?php include es_locate_template( 'front/property-pdf/partials/agent.php' ); ?>
							<?php endforeach; ?>
                        </td>
                    </tr>
				<?php endif; ?>
            </table>
        </td>
        <td style="padding: 0 10px 0;" width="25%" valign="top">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td>
						<?php es_load_template( 'front/property-pdf/partials/basic-fields.php' );

						$latitude = es_get_the_field( 'latitude' );
						$longitude = es_get_the_field( 'longitude' );
						$map_zoom = ests( 'pdf_map_zoom' );

						if ( ests( 'is_pdf_map_enabled' ) && $latitude && $longitude && ests( 'google_api_key' ) ) :
							$url = sprintf( "https://maps.googleapis.com/maps/api/staticmap?zoom=%s&size=%s&key=%s&markers=%s,%s", $map_zoom, '178x280', ests( 'google_api_key' ), $latitude, $longitude ); ?>
                            <img id="property-map" src="<?php echo $url; ?>" alt="">
						<?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
