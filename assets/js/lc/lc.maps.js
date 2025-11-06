'use strict';

/**
 * Load maps js library event
 * @param {object} element - html node
 * @returns {Promise}
 */
LC.loadMapsJs = (element) => {
    return new Promise((resolve, reject) => {
        if (element) {
            const data = $(element).data('lc-data');

            if (data?.mapKind === 'gmap' && data?.googleApiKey) {
                if (typeof window.google === 'undefined') {
                    const locale = LC.global.session.locale.split('_'),
                        language = locale[0] ?? 'en',
                        region = locale[1] ?? 'EN',
                        url = `//maps.googleapis.com/maps/api/js?key=${data.googleApiKey}&language=${language}&region=${region}&libraries=places,marker`;

                    LC.require.js(url, () => {
                        resolve();
                    });
                } else {
                    resolve();
                }
            } else {
                reject(Error('mapKind or googleApiKey data not exists'));
            }
        }
        else {
            reject(Error('LC.loadMapsJs {element} is null'));
        }
    });
};

/**
 * add the physicalLocations container
 * @param {Object} event
 */
LC.dataEvents.getPhysicalLocations = function (event) {
    const $element = $(event.currentTarget),
        selectData = $element.data('lc'),
        optionData = $element.find('option:selected').data('lc'),
        params = new urlParameterEncoder();

    if (!(selectData.physicalLocationsFilter instanceof Array)) {
        for (const param in selectData.physicalLocationsFilter) {
            params.addParameter(param, selectData.physicalLocationsFilter[param]);
        }
    }
    params.addParameter('countryCode', optionData.code);

    $.get(LC.global.routePaths.PHYSICAL_LOCATION + params.getParameters(), (result) => {
        LC.maps.reloadModules(result, optionData.code);
    }, 'html').fail(function (result) {
        LC.notify(LC.global.languageSheet.errorCodeInternalError, { type: 'danger' });
    });
};

LC.modalCallbacks.physicalLocationsProviderCallback = function ($element, $modal) {

    $modal.find('#pickupPointProviderId, #country, #postalCode').on('change keyup', (event) => {
        if ($modal.find('#pickupPointProviderId').val().length &&
            $modal.find('#country').val().length &&
            $modal.find('#postalCode').val().length
        ) {
            $modal.find('#getPickingDeliveryPoints').attr('disabled', false);
        } else {
            $modal.find('#getPickingDeliveryPoints').attr('disabled', true);
        }
    });

    $modal.find('#country').on('change', (event) => {
        var countryCode = $modal.find('#country').val();
        var $providerSelect = $modal.find('#pickupPointProviderId');
        if (countryCode.length) {
            var route = LC.global.routePaths.CHECKOUT_INTERNAL_SET_PICKUP_POINT_PROVIDERS;
            $.ajax({
                url: route,
                method: 'GET',
                data: { "countryCode": countryCode },
                success: (data) => {
                    var items = data?.data?.data?.items;
                    $providerSelect.empty();
                    if (items.length) {
                        items.forEach(p => {
                            var display = p.language?.name;

                            $('<option>', { value: p.id, text: display, })
                                .attr('text', display)
                                .attr('data-lc', JSON.stringify(p))
                                .appendTo($providerSelect);
                        });

                        $providerSelect.prop('disabled', false);
                    } else {
                        $('<option>', { value: '', text: '' })
                            .appendTo($providerSelect);
                        $providerSelect.prop('disabled', true);
                    }

                    $providerSelect.trigger('change');

                },
                error: () => {
                    $providerSelect.empty()
                        .append('<option value=""></option>')
                        .prop('disabled', true);
                }
            });
        }
    });

    const physicalLocationsMap = $modal.find('.physicalLocationsMap')[0];
    if (physicalLocationsMap) {
        LC.loadMapsJs(physicalLocationsMap)
            .then(() => {
                LC.maps.initialize($modal.find('.lcModalContainer')[0]);
            });
    }

    if ($modal.find('.physicalLocationItem').length == 0 && $modal.find('.physicalLocationNoItems').length == 0
        && globalThis?.lcCommerceSession?.postalCode.length
    ) {
        $modal.find('input[id="postalCode"]').val(lcCommerceSession.postalCode).change();
        $modal.find('button[id="getPickingDeliveryPoints"]').click();
    }

};

/**
 * add the physicalLocations from PickingDeliveryPoints container
 * @param {Object} event
 */
LC.dataEvents.getPickingDeliveryPoints = function (event) {
    const $element = $(event.currentTarget),
        params = new urlParameterEncoder(),
        $pickingDeliveryPointsSearch = $element.closest('.pickingDeliveryPointsSearch');
    $element.attr('disabled', true);

    params.addParameter('pickupPointProviderId', $pickingDeliveryPointsSearch.find('#pickupPointProviderId').val());
    params.addParameter('countryCode', $pickingDeliveryPointsSearch.find('#country').val());
    params.addParameter('postalCode', $pickingDeliveryPointsSearch.find('#postalCode').val());

    $.get(LC.global.routePaths.CHECKOUT_INTERNAL_PICKING_DELIVERY_POINTS + params.getParameters(), (result) => {
        let data = $pickingDeliveryPointsSearch.find('#pickupPointProviderId').data('lc');
        if (data && data.showPickupPointProviderMapMarkers) {
            let pId = $pickingDeliveryPointsSearch.find('#pickupPointProviderId').find(':selected')?.data('lc')?.pId;
            LC.maps.icon = `${LC.global.settings.commerceCdnPath}/img/maps/icon_${pId}.png`;
            LC.maps.iconSelected = `${LC.global.settings.commerceCdnPath}/img/maps/marker_selected_icon_${pId}.png`;
        }
        LC.maps.reloadModules(result);
        LC.maps.setMarkers();
        LC.maps.centerMarkersOnMap();
        $element.attr('disabled', false);
    }, 'html').fail(function (result) {
        LC.notify(LC.global.languageSheet.errorCodeInternalError, { type: 'danger' });
        $element.attr('disabled', false);
    });

};

/**
 * Filter the Physical Locations
 * @param {Object} event
 */
LC.dataEvents.filterPhysicalLocations = function (event) {
    const $element = $(event.currentTarget);
    let optionValue = $element.find('option:selected').val(),
        optionData = JSON.parse($element.find('option:selected').attr('data-lc')),
        $childrenSelector = [],
        $selectors = $element.closest('.physicalLocationSelectors'),
        setDefault = 0,
        $locationSearch = $selectors.find('#locationSearch');

    if ($locationSearch.length) {
        $locationSearch.data('lc-country', optionData.countryCode);
    }

    if ($element.prop('setDefault') != undefined) {
        setDefault = $element.prop('setDefault');
        $element.prop('setDefault', 0);
        $element.find('option').each((index, element) => {
            let data = JSON.parse($(element).attr('data-lc'));
            if ($.inArray('physicalLocation' + setDefault, data.ids) >= 0) {
                $(element).prop('selected', true);
            }
        });
    }

    if ($element.attr("name") === 'country') {
        $childrenSelector = $selectors.find('#state');
        if (!$childrenSelector.length)
            $childrenSelector = $selectors.find('#city');
        if (!$childrenSelector.length)
            $childrenSelector = $selectors.find('#postalCode');
    }
    if ($element.attr("name") === 'state') {
        $childrenSelector = $selectors.find('#city');
        if (!$childrenSelector.length)
            $childrenSelector = $selectors.find('#postalCode');
    } else if ($element.attr("name") === 'city') {
        $childrenSelector = $selectors.find('#postalCode');
    }

    if ($childrenSelector.length) {
        let selectDefault = false,
            allOptionIds = [];
        $childrenSelector.find('option').each((i, element) => {
            const data = JSON.parse($(element).attr('data-lc')),
                $physicalLocationItem = $(element).closest('.physicalLocationItem');
            if (data.parent === optionValue || $(element).val() === 'ALL') {
                $(element).prop('disabled', false).show();
                $physicalLocationItem.addClass('show');
                if (!selectDefault && (setDefault === 0 || (setDefault > 0 && $.inArray('physicalLocation' + setDefault, data.ids) >= 0))) {
                    $childrenSelector.val($(element).val());
                    selectDefault = true;
                }
                if ($(element).val() != 'ALL') {
                    allOptionIds = $.merge(allOptionIds, data.ids);
                }
            } else {
                $(element).prop('disabled', true).hide();
                $physicalLocationItem.removeClass('show');
            }
        });

        if ($childrenSelector.find('option[value="ALL"]').length) {
            if (!allOptionIds.length) {
                $childrenSelector.find('option[value="ALL"]').attr('data-lc', $element.find('option:selected').attr('data-lc'));
            } else {
                $childrenSelector.find('option[value="ALL"]').attr('data-lc', JSON.stringify({ ids: allOptionIds, parent: optionValue }));
            }
        }

        if (setDefault > 0) {
            $childrenSelector.prop('setDefault', setDefault);
        }
        $childrenSelector.change();
    } else {
        let selectDefault = false,
            $physicalLocationItems = $element.closest('.physicalLocations').find('.physicalLocationItems');
        if (!$physicalLocationItems.length) {
            $physicalLocationItems = $('.physicalLocations').find('.physicalLocationItems');
        }

        $physicalLocationItems.find('.physicalLocationItem').removeClass('show selected');
        $physicalLocationItems.find('[name="physicalLocation"]').prop('checked', false);

        for (let i = 0; i < optionData.ids.length; i++) {
            const $physicalLocation = $physicalLocationItems.find(`input#${optionData.ids[i]}`),
                $physicalLocationItem = $physicalLocation.closest('.physicalLocationItem');

            $physicalLocationItem.addClass('show');

            if (!selectDefault && (setDefault > 0 && optionData.ids[i] === `physicalLocation${setDefault}`)) {
                $physicalLocationItem.addClass('selected');
                selectDefault = true;
            }
        }
    }

    if (LC.maps.map) {
        LC.maps.setMarkers();
        LC.maps.centerMarkersOnMap();
    }
};

LC.modalCallbacks.physicalLocationsCallback = function (element, modal) {
    LC.dataEvents.initFilterPhysicalLocations($(modal.find('[name="initSelectors"]')[0]));
};

/**
 * Initialize Physical Locations filter
 * @param {Object} element
 * 
 */
LC.dataEvents.initFilterPhysicalLocations = function (element) {
    const $element = $(element),
        data = $element.data('lc'),
        $selector = $element.closest('.physicalLocationSelectors').find('#' + data);
    $element.closest('.modal-content').find('button.savePickingSelectionButton').prop('disabled', true);

    if ($element.val() > 0) {
        $selector.prop('setDefault', $element.val());
        $element.closest('.modal-content').find('button.savePickingSelectionButton').prop('disabled', false);
    }

    $selector.change();

    const physicalLocationsMap = element.closest('.physicalLocations')[0].querySelector('.physicalLocationsMap');
    if (physicalLocationsMap) {
        LC.loadMapsJs(physicalLocationsMap)
            .then(() => {
                LC.maps.initialize(physicalLocationsMap);
            });
    }
};

/**
 * Select the Physical Location
 * @param {Object} event
 */
LC.dataEvents.selectPhysicalLocation = function (event) {
    const $element = $(event.currentTarget),
        data = $element.data('lc'),
        $physicalLocationItems = $element.closest('.physicalLocations').find('.physicalLocationItems'),
        $physicalLocationItem = $element.closest('.physicalLocationItem');

    $physicalLocationItems.find('.physicalLocationItem').removeClass('selected');
    $physicalLocationItem.addClass('selected');

    const physicalLocationsMap = event.currentTarget.closest('.physicalLocationsMap');
    if (physicalLocationsMap) {
        LC.maps.selectPhysicalLocation($(event.currentTarget), true, data.id);
    }
    $element.closest('.modal-content').find('button.savePickingSelectionButton').prop('disabled', false);
};

/**
 * LC maps object
 */
LC.maps = {
    options: {
        zoom: 4,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: true,
        streetViewControl: true,
        rotateControl: false,
        fullscreenControl: false,
        dragging: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        keyboard: true,
        show: true,
        showMap: true,
        maxZoom: 18,
        /**
         * Enable/disable Info Windows on marker click
         * https://developers.google.com/maps/documentation/javascript/infowindows
         * @type {boolean}
         */
        showMarkerInfo: false,
        /**
         * Define Info Windows custom template.
         * @see LC.maps.getInfoWindowContent()
         * @type {string|null}
         */
        showMarkerInfoTemplate: null,
        infoWindowOptions: {
            maxWidth: 350,
        },
        mapId: ''
    },
    infoWindow: null,

    container: null,
    mapLayer: null,
    mapKind: 'gmap',

    icon: `${LC.global.settings.commerceCdnPath}/img/maps/icon.png`,
    iconSelected: `${LC.global.settings.commerceCdnPath}/img/maps/marker_selected_icon.png`,
    userIcon: `${LC.global.settings.commerceCdnPath}/img/maps/user_icon.png`,

    /**
     * @typedef {Object} Marker
     * @property {Object} obj google marker object
     * @property {number} id physical location id
     */

    /**
     * @type {Marker[]}
     */
    markers: [],

    /**
     * @type {google.maps.Map}
     */
    map: null,

    /**
     * @type {bool}
     */
    markerSelectedOnLoad: false,

    /**
     * @type {google.maps.marker.AdvancedMarkerElement}
     */
    userMarker: null,

    /**
     * @type {google.maps.DirectionsService}
     */
    directionsService: null,

    /**
     * @type {google.maps.DirectionsRenderer}
     */
    directionsRenderer: null,

    initialize(target, options = {}) {
        if (target) {
            this.options = { ...this.options, ...options };
            this.container = target;
            this.mapLayer = target.querySelector('.mapInstance');

            if (this.mapLayer) {
                if (options.mapKind) {
                    this.mapKind = options.mapKind;
                }

                if (this.mapKind === 'gmap') {
                    this.initGoogleMaps();
                }

                if (!$(this.container).find('.physicalLocationItem.show').length) {
                    $(this.container).find('.mapPhysicalLocationsList').hide();
                } else {
                    $(this.container).find('.mapPhysicalLocationsList').show();
                }
            }
        }
    },

    /**
     * Initializes google maps
     */
    initGoogleMaps() {
        this.options.mapId = $(this.mapLayer).attr('id') ? $(this.mapLayer).attr('id') : 'gmap';
        this.map = new google.maps.Map(this.mapLayer, this.options);
        this.setMarkers();

        const modal = document.getElementById('physicalLocations');
        const modalPickupPointProviders = document.getElementById('pickupPointProviders');
        if (modal) {
            modal.addEventListener('shown.bs.modal', (event) => {
                this.whereCenterTheMap();
                this.getCurrentPosition();
            })
        }
        if (modalPickupPointProviders) {
            modalPickupPointProviders.addEventListener('shown.bs.modal', (event) => {
                this.whereCenterTheMap();
                this.getCurrentPosition();
            })
        }
        else if (document.querySelector('.returnProductsPopupModal.show')) {
            this.whereCenterTheMap();
            this.getCurrentPosition();
        } else if (document.querySelector('.physicalLocationsMap')) {
            this.whereCenterTheMap();
            this.getCurrentPosition();
        }

        this.placesAutocomplete.init();

        // Global listeners
        $(this.container).on('click', '.physicalLocationItem .directions', (event) => {
            event.preventDefault();
            this.setDirections();
        });
        $(this.container).on('click', '.traceTravelToolbar .driving', (event) => {
            event.preventDefault();
            this.setDirections('DRIVING');
        });
        $(this.container).on('click', '.traceTravelToolbar .walking', (event) => {
            event.preventDefault();
            this.setDirections('WALKING');
        });
        $(this.container).on('click', '.showAllMapMarkers', (event) => {
            event.preventDefault();
            this.centerMarkersOnMap();
        });

        if ($(this.container).find('[data-lc-check-visibility="true"]').length) {
            this.map.addListener('bounds_changed', this.checkVisiblePoints);
        }

    },

    checkVisiblePoints() {
        let bounds = LC.maps.map.getBounds(),
            $container = $(LC.maps.container),
            countLocations = 0;
        if (!bounds) return;
        for (let i = 0; i < LC.maps.markers.length; i++) {
            let latLng = new google.maps.LatLng(LC.maps.markers[i].lat, LC.maps.markers[i].lng),
                $physicalLocation = $container.find(`#physicalLocation${LC.maps.markers[i].id}`),
                $physicalLocationItem = $physicalLocation.closest('.physicalLocationItem');

            if (!$physicalLocationItem.hasClass('selected')) {
                if (bounds.contains(latLng)) {
                    $physicalLocationItem.show();
                    countLocations += 1;
                } else {
                    $physicalLocationItem.hide();
                }
            } else {
                countLocations += 1;
            }
        }
        $container.find('.physicalLocationsMapResults').html(
            `${countLocations} ${LC.global.languageSheet.locatorResults}`
        );
    },

    /**
     * Choose the center of the map to display depending on several factors.
     */
    whereCenterTheMap() {
        if (this.markerSelectedOnLoad) {
            let $selected = $(this.container).find('.physicalLocationItem.selected').find('input[name="physicalLocation"');
            if (!$selected.length) {
                $selected = $(this.container).find('[name="physicalLocation"]:checked');
            }
            const selectedMarker = this.getMarker();
            if ($selected.length && selectedMarker) {
                LC.maps.selectPhysicalLocation($selected, true, selectedMarker.id);
            }
        } else {
            this.centerMarkersOnMap();
        }
    },

    placesAutocomplete: {

        /**
         * Requires google maps places api
         * @type {google.maps.places.Autocomplete}
         */
        autocomplete: null,

        /**
         * Requires google maps places api
         * @type {google.maps.places.AutocompleteService}
         */
        autocompleteService: null,

        /**
         * HTMl input text node
         * @type {object}
         */
        placesAutocompleteInput: null,

        /**
         * Start the places search box input if exists
         */
        init() {
            this.placesAutocompleteInput = document.querySelector('[name="placesAutocomplete"]');

            if (this.placesAutocompleteInput) {
                this.autocomplete = new google.maps.places.Autocomplete(this.placesAutocompleteInput, {
                    types: ['geocode']
                });
                this.autocompleteService = new google.maps.places.AutocompleteService();

                // Bias the SearchBox results towards current map's viewport.
                LC.maps.map.addListener("bounds_changed", () => {
                    this.autocomplete.setBounds(LC.maps.map.getBounds());
                });

                this.placesChanged();
            }
        },

        placesChanged() {
            this.autocomplete.addListener("place_changed", () => {
                let place = this.autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    this.autocompleteService.getPlacePredictions({
                        input: this.placesAutocompleteInput.value,
                        types: ['geocode'],
                    }, (predictions, status) => {
                        if (status != google.maps.places.PlacesServiceStatus.OK) {
                            this.placesAutocompleteInput.closest('.form-group').classList.add('has-error');
                            return;
                        }

                        const placeService = new google.maps.places.PlacesService(document.createElement('div'));

                        placeService.getDetails({
                            placeId: predictions[0].place_id
                        }, (result, status) => {
                            place = result;
                            this.submitPlace(place);
                        });
                    });
                } else {
                    this.submitPlace(place);
                }
            });
        },

        submitPlace(place) {
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                LC.maps.map.fitBounds(place.geometry.viewport);
            } else {
                LC.maps.map.setCenter(place.geometry.location);
                // LC.maps.map.setZoom(17);
            }
        },
    },

    /**
     * Search all valid physicalLocations and save markers
     */
    setMarkers() {
        this.removeMarkers();

        $(this.container).find('.physicalLocationItem.show').each((index, el) => {
            const data = $(el).find('input[name="physicalLocation"], input[name="returnDelivery"]').data('lc'),
                latitude = data.location.coordinate.latitude,
                longitude = data.location.coordinate.longitude;
            let icon = document.createElement('img');
            icon.src = this.icon;
            const marker = new google.maps.marker.AdvancedMarkerElement({
                id: index,
                map: this.map,
                position: { lat: latitude, lng: longitude },
                content: icon
            });

            this.addEventMarker(marker, 'click', 'centerAndDisplay', data);
            if (this.options.showMarkerInfo) {
                this.addEventMarker(marker, 'click', 'showMarkerInfo', data);
            }

            this.markers.push({
                id: data.id,
                obj: marker,
                lat: latitude,
                lng: longitude
            });

            if ($(el).hasClass('selected') || $(el).find('[name="physicalLocation"]:checked').length) {
                this.markerSelectedOnLoad = true;
            }
        });
        this.setTotalResults();

        $(this.container).find('button.savePickingSelectionButton');
        if ($(this.container).find('.physicalLocationItem.show').length && $(this.container).find('[name="physicalLocation"]:checked').length) {
            $(this.container).closest('.modal-content').find('button.savePickingSelectionButton').prop('disabled', false);
            this.whereCenterTheMap();
        } else {
            $(this.container).closest('.modal-content').find('button.savePickingSelectionButton').prop('disabled', true);
        }
    },

    /**
     * Bounce animation on marker
     * @param {object} marker google maps marker
     */
    bounceMarker(marker) {
        setTimeout(() => {
            if (!marker.content.classList.contains("map-marker-bounce")) {
                marker.content.classList.add("map-marker-bounce");
                setTimeout(function () {
                    marker.content.classList.remove("map-marker-bounce");
                }, 1475);
            }
        }, 0);
    },

    /**
     * Set total markers results
     */
    setTotalResults() {
        $(this.container).find('.physicalLocationsMapResults').html(
            `${this.markers.length} ${LC.global.languageSheet.locatorResults}`
        );
    },

    /**
     * Find and return marker by physicalLocationId
     * @param {number} [physicalLocationId ]
     * @returns {Marker|null}
     */
    getMarker(physicalLocationId) {
        if (!physicalLocationId) {
            let $selected = $(this.container).find('.physicalLocationItem.selected');
            if (!$selected.length) {
                physicalLocationId = $(this.container).find('[name="physicalLocation"]:checked').data('lc').id;
            } else {
                physicalLocationId = $selected.find('input[name="physicalLocation"], input[name="returnDelivery"]').data('lc').id;
            }
        }
        for (let i = 0; i < this.markers.length; i++) {
            if (this.markers[i].id === physicalLocationId) {
                return this.markers[i];
            }
        }
        return null;
    },

    /**
     * Show all markers map
     */
    showMarkers() {
        for (let i = 0; i < this.markers.length; i++) {
            this.markers[i].obj.map = this.map;
        }
    },

    /**
     * Remove all markers of map and reset global property
     */
    removeMarkers() {
        for (let i = 0; i < this.markers.length; i++) {
            this.markers[i].obj.map = null;
        }
        this.markerSelectedOnLoad = false;
        this.removeDirectionsRender();
        this.markers = [];
    },

    removeDirectionsRender() {
        if (this.directionsRenderer) {
            this.directionsRenderer.setMap(null);
            this.directionsRenderer.setPanel(null);
        }
    },

    /**
     * Center map with all visible markers
     */
    centerMarkersOnMap() {
        const bounds = new google.maps.LatLngBounds();
        for (let i = 0; i < this.markers.length; i++) {
            const marker = this.markers[i].obj;
            bounds.extend(marker.position);
        }
        if (this.userMarker) {
            bounds.extend(this.userMarker.position);
        }
        this.map.setCenter(bounds.getCenter());
        this.map.fitBounds(bounds);
    },

    /**
     * Center map on marker
     * @param {object} marker
     */
    centerMarkerOnMap(marker) {
        const latLng = new google.maps.LatLng(marker.position.lat, marker.position.lng);
        this.map.panTo(latLng);
        this.setMapSmoothZoom(this.options.maxZoom, this.map.getZoom());
    },

    /**
     * Center map on latitude and longitude checking de markers shows in the map
     * @param {object} marker
     * @param {object} marker
     * @param {numeric} zooms
     * @param {numeric} minResults
     * @param {numeric} maxResults
     */
    centerLatLngOnMap(lat, lng, zooms, minResults = 0, maxResults = 0) {
        this.centerMarkersOnMap();
        const latLng = new google.maps.LatLng(lat, lng);
        this.map.panTo(latLng);
        this.setMapSmoothZoom(zooms, this.map.getZoom(), minResults, maxResults);
    },

    /**
     * Marker event manager
     * @param {object} marker Google marker object
     * @param {string} eventType 'click'
     * @param {string} action event identifier name
     * @param {object} [data] 
     */
    addEventMarker(marker, eventType, action, data = {}) {
        if (action === 'centerAndDisplay') {
            marker.addListener(eventType, (item) => {
                const $physicalLocation = $(this.container)
                    .find('[name="physicalLocation"], input[name="returnDelivery"]')
                    .filter((index, el) => parseInt(el.value) === data.id);

                this.selectPhysicalLocation($physicalLocation, false, data.id);
                this.setSelectedIconMarker(marker);
                this.bounceMarker(marker);
            });

        } else if (action === 'center') {
            marker.addListener(eventType, (item) => {
                this.centerMarkerOnMap(marker);
                this.bounceMarker(marker);
            });

        } else if (action === 'showMarkerInfo') {
            marker.addListener(eventType, (item) => {
                this.showMarkerInfo(marker, data);
            });
        }
    },

    /**
     * Show marker info window
     * @param {object} marker Google marker object
     * @param {object} data physical point html el data attr
     */
    showMarkerInfo(marker, data) {
        this.closeOpenInfoWindow();
        this.infoWindow = new google.maps.InfoWindow({
            ...{
                content: this.getInfoWindowContent(data),
            }, ...this.options.infoWindowOptions
        });
        this.infoWindow.open({
            anchor: marker,
            map: this.map,
            shouldFocus: false,
        });
    },

    /**
     * If exist opened infoWindow closes
     */
    closeOpenInfoWindow() {
        if (this.infoWindow) {
            this.infoWindow.close();
            this.infoWindow = null;
        }
    },

    /**
     * Update map options
     * @param {object} options google map options
     */
    updateMapOptions(options) {
        this.map.setOptions(options);
        this.options = { ...this.options, ...options };
    },

    /**
     * Select physical location on map
     * @param {object} $physicalLocation jQuery physical location input node
     * @param {boolean} centerMap center marker on map
     * @param {number} physicalLocationId 
     */
    selectPhysicalLocation($physicalLocation, centerMap, physicalLocationId) {
        const $physicalLocationItems = $physicalLocation.closest('.physicalLocationItems'),
            marker = this.getMarker(physicalLocationId);

        $physicalLocationItems.find('.physicalLocationItem').removeClass('selected');
        $physicalLocation.click();
        $physicalLocation.closest('.physicalLocationItem').addClass('selected');

        $physicalLocationItems.animate({
            scrollTop: $physicalLocationItems.scrollTop() + $physicalLocation.closest('.physicalLocationItem').position().top,
        }, 500);

        this.toggleTraceToolbar(false);
        this.removeDirectionsRender();
        this.setSelectedIconMarker(marker);
        marker.obj.map = this.map; // prevent hide marker from calculate route
        if (this.options.showMarkerInfo) {
            setTimeout(() => {
                this.showMarkerInfo(marker.obj, $physicalLocation.data('lc'));
            }, this.getZoomDurationTime());
        }

        if (centerMap) {
            this.centerMarkerOnMap(marker.obj);
        }
    },

    /**
     * Set selected marker icon and reset all other markers
     * @param {Marker|object} marker
     */
    setSelectedIconMarker(marker) {
        for (var i = 0; i < this.markers.length; i++) {
            let icon = document.createElement('img');
            icon.src = this.icon;
            this.markers[i].obj.content = icon;
        }
        let iconSelected = document.createElement('img');
        iconSelected.src = this.iconSelected;
        iconSelected.classList.add('map-marker-bounce');

        if (marker.obj) {
            marker.obj.content = iconSelected;
        } else {
            marker.content = iconSelected;
        }
    },

    /**
     * Check the markers before sets zoom
     * @param {number} newZoomLevel 
     */
    checkZoomMarkers(newZoomLevel) {
        const currentZoom = this.map.getZoom();
        this.map.setZoom(newZoomLevel);
        const newBounds = this.map.getBounds();
        this.map.setZoom(currentZoom);
        const visibleMarkers = this.markers.filter(marker => newBounds.contains(new google.maps.LatLng(marker.lat, marker.lng)));
        return visibleMarkers.length;
    },

    /**
     * Smoth google maps animated zoom 
     * @param {number} max 
     * @param {number} cnt 
     * @param {number} minResults 
     * @param {number} maxResults 
     */
    setMapSmoothZoom(max, cnt, minResults = 0, maxResults = 0) {
        let shouldZoom = cnt < max;
        if (minResults > 0 || maxResults > 0) {
            let newZoomMarkers = this.checkZoomMarkers(cnt);
            if (shouldZoom && minResults > 0 && newZoomMarkers < minResults) {
                shouldZoom = false;
            }
            if (cnt >= max && maxResults > 0) {
                shouldZoom = newZoomMarkers >= maxResults;
            }
        }
        if (!shouldZoom) {
            return;
        } else {
            const z = google.maps.event.addListener(this.map, 'zoom_changed', (event) => {
                google.maps.event.removeListener(z);
                this.setMapSmoothZoom(max, cnt + 1, minResults, maxResults);
            });
            // 80ms is what I found to work well on my system -- it might not work well on all systems
            setTimeout(() => {
                this.map.setZoom(cnt, { duration: 50 });
            }, 80);
        }
    },

    /**
     * Get map.setZoom duration time between current zoom and max zoom
     */
    getZoomDurationTime() {
        const currentZoom = (18 - this.map.getZoom()); // 18 is the maximum zoom range
        let zoomResponseTime = 100; //time in ms (50 ms by default * 2)
        if (currentZoom !== 0) {
            const timeOutZoomDuration = currentZoom * 80;
            const zoomDuration = currentZoom * 50;

            zoomResponseTime = (timeOutZoomDuration + zoomDuration);
        }
        return zoomResponseTime
    },

    /**
     * Get current position and add marker
     */
    getCurrentPosition() {
        const errorMessage = LC.global.languageSheet.locatorTraceTravelBrowserUnavailable;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                let userIcon = document.createElement('img');
                userIcon.src = this.userIcon;
                this.userMarker = new google.maps.marker.AdvancedMarkerElement({
                    position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                    map: this.map,
                    content: userIcon
                });
                this.addEventMarker(this.userMarker, 'click', 'center');
                $(this.container).find('.physicalLocationItem .directions').removeClass('d-none');
                this.whereCenterTheMap();
            }, (error) => {
                this.showCurrentPositionError(error.message, error);
            }, {
                timeout: 100000,
            });
        } else {
            this.showCurrentPositionError(errorMessage);
        }
    },

    /**
     * Manages geolocation get curren position errors
     * @param {string} errorMessage 
     * @param {GeolocationPositionError|null} [error] 
     */
    showCurrentPositionError(errorMessage, error = null) {
        if (error && error.code !== error.PERMISSION_DENIED) {
            LC.notify(errorMessage, { type: 'danger' });
        } else if (errorMessage.length && error == null) {
            LC.notify(errorMessage, { type: 'danger' });
        }
    },

    /**
     * Set route from user location and store
     * @param {string} [mode] DRIVING|WALKING
     */
    setDirections(mode = 'DRIVING') {
        if (!this.userMarker) {
            this.getCurrentPosition();
            return;
        }

        this.setMarkers();

        if (!this.directionsService || !this.directionsRenderer) {
            this.directionsService = new google.maps.DirectionsService();
            this.directionsRenderer = new google.maps.DirectionsRenderer();
        }

        const selectedMarker = this.getMarker().obj,
            request = {
                origin: this.userMarker.position,
                destination: selectedMarker.position,
                travelMode: google.maps.TravelMode[mode],
            };

        this.directionsService.route(request, (response, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                selectedMarker.map = null;
                this.toggleTraceToolbar(true, mode, response.routes[0].legs[0].duration.text, response.routes[0].legs[0].distance.text);
                this.directionsRenderer.setDirections(response);
                this.directionsRenderer.setMap(this.map);
            } else {
                const errorMessage = LC.global.languageSheet.locatorTraceTravelUnavailable;
                LC.notify(errorMessage, { type: 'danger' });
            }
        });

        const bounds = new google.maps.LatLngBounds();
        bounds.extend(this.userMarker.position);
        bounds.extend(selectedMarker.position);
        this.map.fitBounds(bounds);
    },

    /**
     * Show/hide traceToolbar element
     * @param {bool} show 
     * @param {string} mode 
     * @param {string} duration 
     * @param {string} distance 
     */
    toggleTraceToolbar(show, mode, duration, distance) {
        if (show) {
            $(this.container).find('.traceTravelToolbarButton').removeClass('active');
            if (mode === 'DRIVING') {
                $(this.container).find('.driving').addClass('active');
            } else {
                $(this.container).find('.walking').addClass('active');
            }
            $(this.container).find('.traceTravelToolbarData .duration').html(duration);
            $(this.container).find('.traceTravelToolbarData .distance').html(distance);
            $(this.container).find('.traceTravelToolbar').slideDown(function () {
                $(this).attr('aria-hidden', false);
            });
        } else {
            $(this.container).find('.traceTravelToolbar').slideUp(function () {
                $(this).attr('aria-hidden', true);
            });
        }
    },

    /**
     * Returns info window content
     * @param {object} data - physicalLocation data-lc data object
     * @returns {string}
     */
    getInfoWindowContent(data) {
        let content = '';

        if (typeof this.options.showMarkerInfoTemplate === 'string') {
            content = this.options.showMarkerInfoTemplate;
            content = content.replaceAll('{{name}}', data.name ?? '');
            content = content.replaceAll('{{address}}', data.address ?? '');
            content = content.replaceAll('{{postalCode}}', data.postalCode ?? '');
            content = content.replaceAll('{{city}}', data.city ?? '');
            content = content.replaceAll('{{state}}', data.state ?? '');
            content = content.replaceAll('{{country}}', data.country ?? '');
            content = content.replaceAll('{{phone}}', data.phone ?? '');
            content = content.replaceAll('{{email}}', data.email ?? '');
            content = content.replaceAll('{{information}}', data?.language?.information ?? '');
        } else {
            if (data?.name) content += `<div class="marker name"><b>${data?.name}</b></div>`;
            if (data?.address) content += `<div class="marker address">${data?.address}</div>`;
            if (data?.postalCode || data?.city || data?.state || data?.country)
                content += `<div class="marker proximity">${data?.postalCode} ${data?.city} ${data?.state} ${data?.country}</div>`;
            if (data?.phone) content += `<div class="marker phone">${data.phone}</div>`;
            if (data?.email) content += `<div class="marker email">${data.email}</div>`;
            if (data?.language?.information) content += `<div class="marker info">${data.language.information}</div>`;
        }

        return content;
    },

    /**
     * Refresh all physicalLocations html modules least map
     * @param {string} html - XHR physicalLocations html
     */
    reloadModules(html, countryCode = '') {
        const $result = $(html);
        const $noItems = $result.find('.physicalLocationNoItems');
        const $locationSearch = $result.find('#locationSearch');

        if (countryCode.length && $locationSearch) {
            $locationSearch.data('lc-country', countryCode);
        }

        $(this.container).find('.mapPhysicalLocationsList').show();

        if ($noItems.length) {
            if (LC.maps.map) {
                LC.maps.removeMarkers();
            }
            if (document.querySelector('.returnProductsPopupModal.show') ||
                location.pathname.includes(LC.global.routePaths.PHYSICAL_LOCATION_STORES)
            ) {
                LC.notify(LC.global.languageSheet.physicalLocationNone, {
                    type: 'danger',
                });
            }
            $(this.container).find('.mapPhysicalLocationsList').html($noItems);
        }

        let physicalLocations = $(this.container).find('.physicalLocations');
        if (physicalLocations.length === 0) {
            physicalLocations = $(this.container).closest('.physicalLocations');
        }

        physicalLocations.each((index, el) => {
            const $physicalLocationSelectors = $(el).find('.physicalLocationSelectors');
            const $traceTravelToolbar = $(el).find('.traceTravelToolbar');
            const $mapPhysicalLocationsList = $(el).find('.mapPhysicalLocationsList');

            if ($result.find('.physicalLocationSelectors').length) {
                $physicalLocationSelectors
                    .replaceWith($result.find('.physicalLocationSelectors'));
            } else {
                $physicalLocationSelectors.html('');
            }

            if ($result.find('.traceTravelToolbar').length) {
                $traceTravelToolbar
                    .replaceWith($result.find('.traceTravelToolbar'));
            } else {
                $traceTravelToolbar.html('');
            }

            if ($result.find('.mapPhysicalLocationsList').length) {
                $mapPhysicalLocationsList
                    .replaceWith($result.find('.mapPhysicalLocationsList'));
            } else if (!$noItems.length) {
                $mapPhysicalLocationsList.html('');
            }
        }).find('[data-lc-event]').dataEvent();
    },
};
