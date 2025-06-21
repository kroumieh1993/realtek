<?php if ( ! empty( $links ) ) : ?>
    <ul class="es-social-links es-control">
        <?php foreach ( $links as $key => $link ) : ?>
            <li class="es-control__item">
                <a href="<?php echo esc_url( $link ); ?>" target="_blank" class="es-btn es-btn--default es-btn--icon es-btn--small">
                    <span class="es-icon es-icon_<?php echo $key; ?>"></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif;
