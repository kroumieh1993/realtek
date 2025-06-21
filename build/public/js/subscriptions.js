( function($) {
    'use strict';

    $( function() {
        $( '.js-es-switch-plan-period' ).change( function() {
            $( '.js-es-plan' ).addClass( 'es-hidden' );

            if ( $( this ).is( ':checked' ) ) {
                $( '.es-plan--has-annual-price' )
                    .removeClass( 'es-hidden' )
                    .addClass( 'es-plan--annual-active' );
            } else {
                $( '.es-plan--has-monthly-price' )
                    .removeClass( 'es-hidden' )
                    .removeClass( 'es-plan--annual-active' );
            }
        } );

        $( '.js-es-switch-payment-type' ).change( function() {
            var $el = $( this );

            if ( $el.is( ':checked' ) ) {
                $( '.js-es-subscription-container' ).addClass( 'es-hidden' );

                $( '.es-' + $el.val() + '-container' ).removeClass( 'es-hidden' );
            }
        } ).trigger( 'change' );

        $( '.js-es-calculate-total' ).change( function() {
            var $form = $( this ).closest( 'form' );
            var basic_count = +$( '[name="basic_listings_count"]', $form ).val();
            var basic_price = +$( '[name="basic_listings_count"]', $form ).data( 'price' );
            var featured_total = +$( '[name="featured_listings_count"]', $form ).val();
            var featured_price = +$( '[name="featured_listings_count"]', $form ).data( 'price' );

            var free_featured = +$( '.js-es-free-featured-count', $form ).val();
            var per_basic_count = +$( '.js-es-per-basic-count', $form ).val();
            var $free_featured_container = $( '.js-es-free-featured-total', $form );

            var sum = 0;

            if ( per_basic_count && free_featured && basic_count && basic_count >= per_basic_count ) {
                var n = Math.floor( basic_count / per_basic_count );
                var free_featured_sum = free_featured * n;

                $free_featured_container.html( free_featured_sum + ' x ' + EstatikSubscriptions.tr.free );

            } else {
                $free_featured_container.html( '' );
            }

            if ( basic_count && basic_price ) {
                sum += basic_count * basic_price;
            }

            if ( featured_total && featured_price ) {
                sum += featured_total * featured_price;
            }

            sum = EstatikFormatter.price( sum );

            $( '.js-es-total', $form ).html( sum );

        } ).trigger( 'change' );
    } );
} )( jQuery );
