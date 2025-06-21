<div class="<?php echo $wrapper_class; ?>">
    <div class="js-es-properties__map es-properties__map es-properties__map--visible">
        <?php include es_locate_template( 'front/property/map.php' ); ?>
    </div>
    <div data-entity="listings" class="js-es-listings js-es-entities es-hidden" data-hash="<?php esc_attr_e( $hash ); ?>"></div>
</div>
