<table id="footer" cellspacing="0" cellpadding="0">
	<tr>
        <?php if ( ests( 'pdf_qr' ) ) : ?>
            <td class="qr-code">
                <img style="margin-left: 40px;width: 80px; " alt="<?php strip_tags( get_the_title() ); ?> QR Code" src="<?php echo add_query_arg( array(
                    'es_qr' => get_permalink( get_the_ID() ),
                ), get_permalink( get_the_ID() ) ); ?>"/>
            </td>
        <?php endif; ?>
		<td style="padding-left: 20px;">
			<?php if ( ! empty( $contact_fields ) ) : ?>
                <h3><?php _e( 'Call us now', 'es' ); ?></h3>
                <table cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                    <?php foreach ( $contact_fields as $label => $value ) : ?>
                        <?php if ( ! empty( $value ) ) : ?>
                            <tr>
                                <td class="contact-label" style="padding: 3px; 0;"><?php echo $label; ?>: </td>
                                <td class="contact-value" style="padding: 3px; 0;"><?php echo $value; ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </td>
        <?php if ( $logo_attachment_id = ests( 'pdf_logo_attachment_id' ) ) : ?>
		<td class="pdf-logo">
			<?php echo wp_get_attachment_image( $logo_attachment_id, 'full', false, array(
				'class' => 'es-pdf-logo'
			) ); ?>
        </td>
        <?php endif; ?>
	</tr>
</table>