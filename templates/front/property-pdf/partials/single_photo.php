<?php $property = es_get_the_property();
$image_url = es_get_the_featured_image_url( 'full' ); ?>
<style>
    #photo {
        background-size: cover;
        background-position: center;
        width: 100%;
        height: 400px;
    }

    .es-price-container {
        width: 200px;
        float: right;
        padding: 0 25px;
    }

    #wrapper {
        width: 100%;
    }

    #content {
        display: inline-block;
        padding: 25px;
    }

    #sidebar {
        display: inline-block;
        width: 250px;
        /*padding: 0 25px;*/
        background: red;
    }

    #wrapper table {
        width: 100%;
    }

    #sidebar {
        background: transparent;
    }

    .space {
        background: red;
        display: block;
        height: 50px;
        line-height: 50px;
        padding: 20px;
        margin: 20px;
    }

    .property-excerpt {
        padding-top: 20px;
    }
</style>

<div id="photo" style="background-image: url('<?php echo $image_url; ?>')">
    <div style="height: 350px;"></div>
	<?php es_the_price( '<div class="es-price-container">', '</div>' ); ?>
</div>

<div id="wrapper">
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td id="content" valign="top">
                <table>
                    <tr>
                        <td><?php es_the_title( '<h1 class="entry-title">', '</h1>' ); ?></td>
                    </tr>
                    <tr>
                        <td><span class="site-url"><?php echo home_url(); ?></span></td>
                    </tr>
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
            <td id="sidebar" valign="top">
				<?php es_load_template( 'front/property-pdf/partials/basic-fields.php' );

				$latitude = es_get_the_field( 'latitude' );
				$longitude = es_get_the_field( 'longitude' );
				$map_zoom = ests( 'pdf_map_zoom' );

				if ( ests( 'is_pdf_map_enabled' ) && $latitude && $longitude && ests( 'google_api_key' ) ) :
					$url = sprintf( "https://maps.googleapis.com/maps/api/staticmap?zoom=%s&size=%s&key=%s&markers=%s,%s", $map_zoom, '250x400', ests( 'google_api_key' ), $latitude, $longitude ); ?>
                    <img id="property-map" src="<?php echo $url; ?>" alt="">
				<?php endif; ?>
            </td>
        </tr>
    </table>
</div>
