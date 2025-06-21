<style>
    .section {
        width: 100%;
        padding: 20px 20px 10px;
    }

    .es-appointments li {
        height: 30px;
    }

    .es-field--appointments b {
        margin-bottom: 10px;
        display: block;
    }
</style>
<?php $fbuilder = es_get_sections_builder_instance();
if ( $sections = es_get_pdf_sections() ) : ?>
    <div class="sections-container">
        <?php foreach ( $sections as $id => $section_info ) :
            if ( ! empty( $section_info['is_pdf_visible'] ) ) :
                if ( ! empty( $sections_info[ $id ]['pdf_render_action'] ) ) : ?>
                    <?php do_action( $sections_info[ $id ]['render_action'] . '_pdf', $id, $sections_info[ $id ] ); ?>
                <?php else:
                    include es_locate_template( 'front/property-pdf/partials/section.php' );
                endif;
            endif;?>
        <?php endforeach; ?>
    </div>
<?php endif;
