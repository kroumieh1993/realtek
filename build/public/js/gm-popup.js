( function() {

    google.maps.Map.prototype.panToWithOffset = function( latlng, offsetX, offsetY ) {
        var map = this;
        var ov = new google.maps.OverlayView();
        ov.onAdd = function() {
            var proj = this.getProjection();
            var aPoint = proj.fromLatLngToContainerPixel( latlng );
            aPoint.x = aPoint.x+offsetX;
            aPoint.y = aPoint.y+offsetY;
            map.panTo( proj.fromContainerPixelToLatLng( aPoint ) );
        };
        ov.draw = function() {};
        ov.setMap( this );
    };

    function EsGoogleMapPriceMarker( property_config, callback ) {
        this.position = property_config.position;

        // This zero-height div is positioned at the bottom of the tip.
        this.containerDiv = document.createElement( 'div' );
        this.containerDiv.classList.add( 'es-price-marker-wrap' );
        this.containerDiv.innerHTML = "<div class='es-price-marker es-price-marker--" + property_config.post_id + " content-font'>" + property_config.price + "</div>";

        // Optionally stop clicks, etc., from bubbling up to the map.
        google.maps.OverlayView.preventMapHitsAndGesturesFrom( this.containerDiv );

        google.maps.event.addDomListener( this.containerDiv, 'click', function() {
            document.querySelectorAll( '.es-price-marker--active' ).forEach(function(i) {
                i.classList.remove('es-price-marker--active');
            });
            this.querySelector( '.es-price-marker' ).classList.add( 'es-price-marker--active' );
        } );

        if ( callback ) {
            google.maps.event.addDomListener( this.containerDiv, 'click', callback );
        }
    }

    /**
     * Custom realtek google maps popup for properties.
     *
     * @param property_config
     * @constructor
     */
    function EsGoogleMapPopup( property_config ) {
        this.position = property_config.position;

        // This zero-height div is positioned at the bottom of the tip.
        this.containerDiv = document.createElement( 'div' );
        this.containerDiv.classList.add( 'es-map-popup' );
        this.containerDiv.innerHTML = property_config.content;

        // Optionally stop clicks, etc., from bubbling up to the map.
        google.maps.OverlayView.preventMapHitsAndGesturesFrom( this.containerDiv );
    }

    // ES5 magic to extend google.maps.OverlayView.
    EsGoogleMapPopup.prototype = Object.create( google.maps.OverlayView.prototype );
    EsGoogleMapPriceMarker.prototype = Object.create( google.maps.OverlayView.prototype );

    /** Called when the popup is added to the map. */
    EsGoogleMapPopup.prototype.onAdd = function() {
        this.getPanes().floatPane.appendChild( this.containerDiv );
    };

    /** Called when the popup is added to the map. */
    EsGoogleMapPriceMarker.prototype.onAdd = function() {
        this.getPanes().floatPane.appendChild( this.containerDiv );
    };

    /** Called when the popup is removed from the map. */
    EsGoogleMapPopup.prototype.onRemove = function() {
        if (this.containerDiv.parentElement) {
            this.containerDiv.parentElement.removeChild( this.containerDiv );
        }
    };

    /** Called when the popup is removed from the map. */
    EsGoogleMapPriceMarker.prototype.onRemove = function() {
        if (this.containerDiv.parentElement) {
            this.containerDiv.parentElement.removeChild( this.containerDiv );
        }
    };

    /** Called each frame when the popup needs to draw itself. */
    EsGoogleMapPopup.prototype.draw = function() {
        var divPosition = this.getProjection().fromLatLngToDivPixel( this.position );

        // Hide the popup when it is far out of view.
        var display =
            Math.abs( divPosition.x ) < 4000 && Math.abs( divPosition.y ) < 4000 ?
                'block' :
                'none';

        if ( display === 'block' ) {
            this.containerDiv.style.left = divPosition.x + 'px';
            this.containerDiv.style.top = divPosition.y + 'px';
        }
        if ( this.containerDiv.style.display !== display ) {
            this.containerDiv.style.display = display;
        }
    };

    EsGoogleMapPriceMarker.prototype.getPosition = function() {
        return this.position;
    };

    EsGoogleMapPriceMarker.prototype.draw = function() {
        var divPosition = this.getProjection().fromLatLngToDivPixel( this.position );

        // Hide the popup when it is far out of view.
        var display =
            Math.abs( divPosition.x ) < 4000 && Math.abs( divPosition.y ) < 4000 ?
                'block' :
                'none';

        if ( display === 'block' ) {
            this.containerDiv.style.left = divPosition.x + 'px';
            this.containerDiv.style.top = divPosition.y + 'px';
        }
        if ( this.containerDiv.style.display !== display ) {
            this.containerDiv.style.display = display;
        }
    };

    window.EsGoogleMapPopup = EsGoogleMapPopup;
    window.EsGoogleMapPriceMarker = EsGoogleMapPriceMarker;
} )();
