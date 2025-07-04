( function( $ ) {
    'use strict';

    if ( typeof elementorFrontend !== 'undefined' ) {
        $( window ).on( 'elementor/frontend/init', function () {
            if ( typeof window.ElementorInlineEditor == 'undefined' ) {
                return;
            }
            // Initialize js for properties hfm.
            elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( e, $scope ) {
                RealtekResponsinator.init();

                if ( $scope.find( '.js-es-p-slideshow') ) {
                    initPropertiesSlideshow();
                }

                if ( $scope.find( '.js-es-locations-slick' )  ) {
                    initLocationsCarousel();
                }

                if ( $scope.find( '.js-es-slick' ) ) {
                    RealtekProperties.initCarousel( e, $scope );
                }

                if ( $scope.find( '.js-es-properties' ) ) {
                    $( document ).find( '.js-es-properties__map.es-properties__map--visible' ).each( function() {
                        var $properties_wrap = $( this ).closest( '.js-es-properties' );
                        var map_instance = new RealtekHalfMap( $properties_wrap );
                        var $listings_wrapper = $properties_wrap.find( '.js-es-listings' );
                        var hash = new RealtekEntitiesHash( $listings_wrapper.data( 'hash' ) );

                        RealtekProperties.halfMapInstances[ hash.getValue( 'loop_uid' ) ] = map_instance;

                        if ( map_instance ) {
                            map_instance.init();
                        }
                    } );
                }
            } );
        } );
    }
} )( jQuery );