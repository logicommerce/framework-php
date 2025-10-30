
/**************************************/
/*            MAP OBJECT              */
/**************************************/
var mapCanvas = function (htmlTarget, options) {
    this.initialize.apply(this, [htmlTarget, options]);
};
$.extend(true, mapCanvas.prototype, {
    initialize: function (htmlTarget, options) {
        // this.userMarker = ''
        this.markersDict = {};
        this.options = options || {};
        this.options.zoom = this.options.zoom ? Number(this.options.zoom) : 4;
        this.options.fullscreenControl = this.options.fullscreenControl ? this.options.fullscreenControl : false;
        this.options.dragging = this.options.dragging === false ? this.options.dragging : true;
        this.options.scrollWheelZoom = this.options.scrollWheelZoom === false ? this.options.scrollWheelZoom : true;
        this.options.doubleClickZoom = this.options.doubleClickZoom === false ? this.options.doubleClickZoom : true;
        this.options.keyboard = this.options.keyboard === false ? this.options.keyboard : true;
        this.options.show = this.options.show === false || this.options.show === 'false' ? false : true;
        this.options.showMap = this.options.showMap === false || this.options.showMap === 'false' ? false : true;
        this.mapLayer = htmlTarget;

        this.mapKind = this.options.mapKind;

        if (this.mapKind == 'baidu') {
            // Baidu default coordinates
            this.defCoordinates = [
                { lat: 43.072684, lng: 126.509854 },
                { lat: 43.072684, lng: 122.509854 },
            ];
        } else if (this.mapKind == 'gmap') {
            var iconUrl = LC.global.settings.commerceCdnPath + 'img/maps/icon.png';
            this.setIcon(iconUrl, null, '');

            var userIconUrl1 = LC.global.settings.commerceCdnPath + 'img/maps/user_icon.png';
            // var userIconUrl2 = LC.global.settings.commonCdnPath + 'images/maps/user_icon.png';
            // this.setIcon({'iconUrl': userIconUrl1, 'type': 'userIcon', 'fallback': userIconUrl2})
            this.setIcon(userIconUrl1, 'userIcon', '');
            // Gmaps default coordinates
            this.defCoordinates = [{ lat: 45.84482112, lng: 11.05171484 }];
        }
    },
    setIcon: function (iconUrl, type, fallback) {
        // var url = this.urlExists(iconUrl)? iconUrl : fallback? fallback: '';
        if (type == 'userIcon') {
            // this.userIcon = url;
            this.userIcon = iconUrl;
            return;
        }

        this.icon = iconUrl;
    },
    urlExists: function (url) {
        var http = new XMLHttpRequest();

        http.open('HEAD', url, false);
        http.send();

        return http.status != 404;
    },
    draw: function () {
        if (this.options.showMap != true) {
            //$('#map-canvas').hide();
            $('#mapInstance').hide();
        }

        if (this.mapKind == 'baidu') {
            this.drawBaiduMap();
        } else if (this.mapKind == 'gmap') {
            this.drawGMap();
        }
    },
    // Set marker. Specify lat, long, info to show and custom icon if needed.
    addMarker: function (lat, lng, index, title, info, icon, display) {
        lat = lat ? lat : this.defCoordinates[0].lat;
        lng = lng ? lng : this.defCoordinates[0].lng;

        info = info ? info : '';
        display = display === false ? display : true;
        icon = icon ? icon : this.icon;

        if (this.mapKind == 'gmap') {
            latLng = new google.maps.LatLng(lat, lng);
            marker = new google.maps.Marker({ map: this.map, position: latLng, title: title, icon: icon });
            this.markersDict[index] = marker;

            // On hover appears info
            marker.infoWindow = new google.maps.InfoWindow({ content: info });
        } else {
            point = new BMap.Point(lng, lat);
            this.markersDict[index] = point;
            marker = new BMap.Marker(point);

            var myIcon = new BMap.Icon(LC.global.settings.commerceCdnPath + 'img/maps/icon.png', new BMap.Size(60, 60), {});

            marker = new BMap.Marker(point, { icon: myIcon });

            markerLabel = new BMap.Label(info);
            markerLabel.setStyle({
                borderColor: '#808080',
                color: '#333',
                borderRadius: '3px',
                padding: '3px',
                boxShadow: '1px 1px 1px #666',
            });
            marker.setLabel(markerLabel);
            marker.getLabel().hide();
            if (display) this.map.addOverlay(marker);
        }

        return marker;
    },
    // Event action specification.
    addEventMarker: function (marker, event, action, parent, customEvent) {
        if (this.mapKind == 'gmap') {
            if (action == 'showInfo') {
                google.maps.event.addListener(marker, event, this.showInfo.bind(parent, this.map, marker));
            } else if (action == 'hideInfo') {
                google.maps.event.addListener(marker, event, this.hideInfo.bind(parent, this.map, marker));
            } else if (action == 'centerMarker') {
                google.maps.event.addListener(marker, event, this.centerMarker.bind(parent, this.map, marker));
            } else {
                google.maps.event.addListener(marker, event, customEvent);
            }
        } else {
            if (action == 'showInfo') {
                marker.addEventListener(event, function (e) {
                    e.currentTarget.getLabel().show();
                });
            } else if (action == 'hideInfo') {
                marker.addEventListener(event, function (e) {
                    e.currentTarget.getLabel().hide();
                });
            } else if (action == 'toggleInfo') {
                marker.addEventListener(event, function (e) {
                    if (e.currentTarget.getLabel()._visible) e.currentTarget.getLabel().hide();
                    else e.currentTarget.getLabel().show();
                });
            } else {
                marker.addEventListener(event, customEvent);
            }
        }
    },

    reSetMarkersMap: function (clean) {
        if (this.mapKind != 'gmap') return;

        var map = clean ? null : this.map;
        for (m in this.markersDict) {
            this.markersDict[m].setMap(map);
        }
        if (this.directionsDisplay) {
            this.directionsDisplay.setMap(null);
            this.directionsDisplay.setPanel(null);
            $('#toolbar').slideUp();
        }
        if (clean) this.markersDict = {};
    },
    // Display Map extrems.
    setMapsArea: function (minLatitude, minLongitude, maxLatitude, maxLongitude, counter, marker) {
        if (this.mapKind == 'gmap' && (counter > 0 || marker)) {
            if (marker || (minLatitude == maxLatitude && minLongitude == maxLongitude)) {
                if (marker) {
                    minLatitude = marker.getPosition().lat();
                    minLongitude = marker.getPosition().lng();
                }

                latLng = new google.maps.LatLng(minLatitude, minLongitude);
                this.map.panTo(latLng);
                this.map.setZoom(this.options.zoom);
                if (marker && marker.getAnimation() != google.maps.Animation.BOUNCE) {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(function () {
                        marker.setAnimation(null);
                    }, 1475);
                }
            } else {
                var bounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(minLatitude, minLongitude),
                    new google.maps.LatLng(maxLatitude, maxLongitude)
                );
                this.map.fitBounds(bounds);
            }
        } else if (this.mapKind == 'baidu') {
            if (!Object.keys(this.markersDict).length) {
                this.addMarker(this.defCoordinates[0].lat, this.defCoordinates[0].lng, null, null, null, null, false);
            }

            if (marker) {
                map_center = this.map.getViewport([marker]);
            } else {
                map_center = this.map.getViewport(Object.values(this.markersDict));
            }
            centerPoint = new BMap.Point(map_center.center.lng, map_center.center.lat);
            this.map.centerAndZoom(centerPoint, map_center.zoom);
        }
    },
    resizeMap: function () {
        if (this.mapKind == 'gmap') {
            google.maps.event.trigger(this.map, 'resize');
        }
    },
    drawGMap: function () {
        google.maps.visualRefresh = true;

        this.mapOptions = {
            center: new google.maps.LatLng(this.defCoordinates[0].lat, this.defCoordinates[0].lng),
            zoom: this.options.zoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            fullscreenControl: this.options.fullscreenControl,
            showMap: this.options.showMap,
        };
        this.map = new google.maps.Map(this.mapLayer, this.mapOptions);
    },
    drawBaiduMap: function () {
        this.map = new BMap.Map(this.mapLayer);
        //this.map = new BMap.Map(this.mapLayer, this.mapOptions);
        // not working mouse LC.global.settings :S
        if (this.options.dragging) this.map.enableDragging();
        if (this.options.scrollWheelZoom) this.map.enableScrollWheelZoom();
        if (this.options.doubleClickZoom) this.map.enableDoubleClickZoom();
        if (this.options.keyboard) this.map.enableKeyboard();
        var ctrl_nav = new BMap.NavigationControl({
            anchor: BMAP_ANCHOR_TOP_LEFT,
            type: BMAP_NAVIGATION_CONTROL_LARGE,
        });
        this.map.addControl(ctrl_nav);
        var ctrl_ove = new BMap.OverviewMapControl({ anchor: BMAP_ANCHOR_BOTTOM_RIGHT, isOpen: 1 });
        this.map.addControl(ctrl_ove);
        var ctrl_sca = new BMap.ScaleControl({ anchor: BMAP_ANCHOR_BOTTOM_LEFT });
        this.map.addControl(ctrl_sca);
    },
    showInfo: function (map, marker, event) {
        event.infoWindow.open(map, event);
    },
    hideInfo: function (map, marker, event) {
        event.infoWindow.close();
    },
    centerMarker: function (map, marker) {
        this.setMapsArea(null, null, null, null, null, marker);
    },
    getCurrentPosition: function (traceTravel) {
        if (this.userMarker) {
            this.calculateDistanceToStores();
            return;
        }

        if (navigator.geolocation && this.mapKind == 'gmap') {
            var self = this;
            self.traceTravelTmp = traceTravel;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    self.userMarker = self.addMarker(
                        position.coords.latitude,
                        position.coords.longitude,
                        null,
                        null,
                        null,
                        self.userIcon,
                        null
                    );
                    self.addEventMarker(LC.map.userMarker, 'click', 'centerMarker', LC.map);
                    self.calculateDistanceToStores();

                    if (self.traceTravelTmp) {
                        self.traceTravelTmp = null;
                        self.traceTravel();
                    }
                },
                function (err) {
                    if (self.traceTravelTmp) {
                        message = LC.global.languageSheet.locatorTraceTravelBrowserUnavailable;
                        $('.alert-warning').remove();
                        var error = $('<div />', {
                            class: 'alert alert alert-warning',
                            html: message,
                        }).insertAfter($('.how'));

                        $('#lcNotify')
                            .find('[data-lc-event]')
                            .dataEvent();
                    }
                },
                { timeout: 100000 }
            );
        } else {
            if (this.traceTravelTmp) {
                message = LC.global.languageSheet.locatorTraceTravelBrowserUnavailable;
                $('.alert-warning').remove();
                var error = $('<div />', {
                    class: 'alert alert alert-warning',
                    html: message,
                }).insertAfter($('.how'));

                $('#lcNotify')
                    .find('[data-lc-event]')
                    .dataEvent();
            }
        }
    },
    calculateDistanceToStores: function () {
        if (this.mapKind != 'gmap') return;

        // This is commented out because the displayed distance is from our current position to the
        // store. However, the first element in the list may have a greater distance than the second
        // one because the distances are calculated dynamically from the user's location.
        // The list, however, is sorted based on the center of the searched area.
        //
        // Example:
        // If you search for stores in Igualada, the list might look like this:
        //  - Position 1: Store A
        //  - Position 2: Store B
        // This happens because Store A is closer to Igualada's center (used by Google by default),
        // but its distance in kilometers might be greater than Store B's since the distances are
        // calculated from your current position, NOT from the center of the searched area!

        // for (storeId in this.data) {
        //     this.calculateDistanceTo(null, this.markersDict[storeId], storeId);
        // }
    },
    calculateDistanceTo: function (origin, destination, storeId) {
        var origin = origin ? origin : this.userMarker ? this.userMarker : '';

        if (Object.prototype.toString.call(origin) != '[object Array]') origin = [origin.getPosition()];
        if (Object.prototype.toString.call(destination) != '[object Array]') destination = [destination.getPosition()];

        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix(
            {
                origins: origin,
                destinations: destination,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                avoidHighways: false,
                avoidTolls: false,
            },
            function (response, status) {
                if (status == google.maps.DistanceMatrixStatus.OK) {
                    var query = response.rows[0].elements[0];
                    if (query.status == 'OK') {
                        document.getElementById('calculatedDistance' + storeId).innerHTML +=
                            ' / ' + query.distance.text;
                    }
                }
            }
        );
    },
    traceTravel: function (mode) {
        if (this.mapKind != 'gmap') return;
        var request,
            bounds = new google.maps.LatLngBounds();

        if (!this.userMarker) {
            this.getCurrentPosition((traceTravel = true));
            return;
        }

        if (typeof mode == 'undefined') {
            mode = 'DRIVING';
            $('#toolbar .btn').removeClass('isActive');
            $('#driving').addClass('isActive');
        }

        this.reSetMarkersMap();

        if (!this.directionsService || !this.directionsDisplay) {
            this.directionsService = new google.maps.DirectionsService();
            this.directionsDisplay = new google.maps.DirectionsRenderer();
        }

        request = {
            origin: this.userMarker.getPosition(),
            destination: this.markersDict[$('ol li.isSelected').data('id')].getPosition(),
            travelMode: google.maps.TravelMode[mode],
        };

        var self = this;
        this.directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                self.markersDict[$('ol li.isSelected').data('id')].setMap(null);
                $('#distance').text(response.routes[0].legs[0].distance.text);
                $('#duration').text(response.routes[0].legs[0].duration.text);
                self.directionsDisplay.setDirections(response);
                $('#toolbar').slideDown();
                self.directionsDisplay.setMap(self.map);
                self.directionsDisplay.setPanel(document.getElementById('directions-panel'));
            } else {
                message = LC.global.languageSheet.locatorTraceTravelUnavailable;

                $('.alert-warning').remove();
                var error = $('<div />', {
                    class: 'alert alert alert-warning',
                    html: message,
                }).insertAfter($('ol li.isSelected').find('.how'));

                $('#lcNotify')
                    .find('[data-lc-event]')
                    .dataEvent();
            }
        });

        bounds.extend(this.userMarker.getPosition());
        bounds.extend(this.markersDict[$('ol li.isSelected').data('id')].getPosition());

        this.map.fitBounds(bounds);
        if (this.map.getZoom() > this.mapOptions.zoom) {
            this.map.setZoom(this.mapOptions.zoom);
        }
    },
});

/**************************************/
/*          MAP INIT                  */
/*************************************/
/*
* @class initListMap takes care of initializing a map and displaying
* some stores on it and a list of them next to the map
*/
var initListMap = function (ajaxSubmitedForm) {
    // Method to that parse stores data, draws markers on the map and creates stores list
    var initStores = function (response) {
        var htmlNbr = '<p></p>';
        var data;
        var allMarkers = [];

        if (response.status.code == 200) {
            data = response.data ? response.data : '';
        }

        var showStockWarehouses = $('#showStockWarehouses').val();
        if (!showStockWarehouses) showStockWarehouses = 1;

        if (response.length > 1) {
            $('#viewAllMap').show();
            $('#viewAllMap').click(function () {
                LC.map.setMapsArea(
                    LC.map.minLatitude,
                    LC.map.minLongitude,
                    LC.map.maxLatitude,
                    LC.map.maxLongitude,
                    LC.map.counter,
                    null
                );
                goToByScroll($('#map-canvas'), 100);
                $('.isSelected').removeClass('isSelected');
                LC.map.reSetMarkersMap();
                $('.additionalInfo').slideUp();
            });
            htmlNbr = '<p>' + response.length + ' ' + LC.global.languageSheet.locatorResults + '</p>';
        } else {
            htmlNbr = '<p>' + response.length + ' ' + LC.global.languageSheet.locatorResult + '</p>';
        }

        $('#result').html(htmlNbr);
        $('#stores-list').empty();

        $('#changeStoreLocatorCriteria')
            .find('a')
            .removeAttr('href');
        $('#changeStoreLocatorCriteria').css('cursor', 'pointer');

        $('#changeStoreLocatorCriteria').click(function (event) {
            $('.locatorForm').show();
            // $('#backToCommerce').hide();
            // $('#changeStoreLocatorCriteria').hide();
            // $('#storeLocatorListTitle').hide();
            goToByScroll($('.locatorForm'), 100);
        });

        LC.map.minLatitude = 9999999;
        LC.map.minLongitude = 9999999;
        LC.map.maxLatitude = -9999999;
        LC.map.maxLongitude = -9999999;
        LC.map.counter = 0;

        // Sorting array of stores by proximity
        var sortable = [];
        for (var store in data) sortable.push([store, data[store]]);

        sorted = sortable.sort(function (a, b) {
            return a[1].proximity - b[1].proximity;
        });

        // Looping over the stores and adding markers to te map and creating <li> with stores info
        for (var item in sorted) {
            var store = sorted[item][1];
            item = sorted[item][0];

            if (!item || !store) break;

            if (!store.stockAvailable && showStockWarehouses == 3) {
                continue;
            }

            // Check map extremes to be shown.
            LC.map.minLatitude = LC.map.minLatitude < store.latitude ? LC.map.minLatitude : store.latitude;
            LC.map.minLongitude = LC.map.minLongitude < store.longitude ? LC.map.minLongitude : store.longitude;
            LC.map.maxLatitude = LC.map.maxLatitude > store.latitude ? LC.map.maxLatitude : store.latitude;
            LC.map.maxLongitude = LC.map.maxLongitude > store.longitude ? LC.map.maxLongitude : store.longitude;

            // Add store marker.
            var marker = LC.map.addMarker(store.latitude, store.longitude, item, null, null, null, null);
            allMarkers.push(marker);

            LC.map.addEventMarker(
                marker,
                'click',
                'centerAndDisplay',
                LC.map,
                function (item) {
                    selectLi(
                        $("ol li[data-id='" + item + "']"),
                        false,
                        [$("ol li[data-id='" + item + "']"), 120],
                        allMarkers
                    );

                    if (LC.map.mapKind == 'gmap') {
                        var marker = LC.map.markersDict[item];

                        if (marker.getAnimation() != google.maps.Animation.BOUNCE) {
                            marker.setAnimation(google.maps.Animation.BOUNCE);
                            setTimeout(function () {
                                marker.setAnimation(null);
                            }, 1475);
                        }

                        for (var i = 0; i < allMarkers.length; i++) {
                            var iconUrl = LC.global.settings.commerceCdnPath + 'img/maps/icon.png';
                            var markerSelected = LC.global.settings.commerceCdnPath + 'img/maps/marker_selected_icon.png';

                            allMarkers[i].setIcon(iconUrl);
                        }
                        marker.setIcon(markerSelected);
                    }
                }.bind(this, item)
            );

            // Create list of stores.
            if (LC.mapOptions.showList) {
                $('#stores-list').append(createStoreTr(store, allMarkers));
            }

            LC.map.counter += 1;
        }

        if (LC.mapOptions.showList) {
            $('#stores-list').append(createStoreTr('', allMarkers));
        }

        // Setting maps visible area.
        LC.map.setMapsArea(
            LC.map.minLatitude,
            LC.map.minLongitude,
            LC.map.maxLatitude,
            LC.map.maxLongitude,
            LC.map.counter,
            null
        );

        if (LC.mapOptions.showList) {
            $('.print').click(function () {
                window.print();
            });

            $('.setPickup').click(function (b) {
                var element = $(this).find('a');

                var d = element.data('id');
                var g = element.data('shippingsectionid');
                var h = element.data('droppoint');
                if (!h) h = {};

                var url = LC.global.routePaths.CHECKOUT;

                if (g) {
                    url += 'useDropPoint/' + d + '/' + g;
                } else url += 'useWarehouse/' + d;

                var a = $.post(url, h, {}, 'json');
                a.done(function (e) {
                    if (e.status.code == 200 && e.response.found == 1) {
                        $('#modalSelectWarehouse').modal('hide');

                        var $form = $('#basketForm');

                        if (e.response.name) {
                            var f = '<div class="warehouse warehouseSelected">';
                            f += '<span>' + e.response.name + '</span>';
                            f += '</div>';
                            $form
                                .find('[data-pickup=1]')
                                .parent()
                                .append(f);
                        }

                        $form
                            .find('[data-pickup=1]')
                            .parent()
                            .find('.warehouse')
                            .remove();

                        enableButtonSubmitForm();

                        if ($form.length) $form = $form.get(0); // avoid normal checkout / osc error
                        $form.submit();
                    }
                });
            });

            $('.how').click(function () {
                LC.map.traceTravel();
                // goToByScroll($("#map-canvas"), 100);
            });

            $('ol li:eq(0)')
                .addClass('isSelected')
                .find('.additionalInfo')
                .show();

            if (findBootstrapEnvironment() == 'Large') {
                if ($('#storeLocatorList').height() > $('#map-canvas').height())
                    $('#map-canvas').height($('#storeLocatorList').height());
                // $("#storeLocatorList").height(height);
            }

            goToByScroll($('#storeLocatorListTitle'), 100);

            // Calculate distances
            LC.map.data = data;
        }
    };

    // Method to create a store <li> on the list.
    var createStoreTr = function (store, allMarkers) {
        if (!store) {
            var li = $('<li />', {
                'data-id': -2,
                class: 'list-group-item storeLi emptyStore',
            });
            return li;
        }

        var liClass = 'list-group-item storeLi';

        if (!store.stockAvailable) {
            liClass += ' noStockStore';
        }

        var showStockWarehouses = $('#showStockWarehouses').val();
        if (!showStockWarehouses) showStockWarehouses = 1;

        if (!store.stockAvailable && showStockWarehouses == 2) {
            liClass += ' disabled';
        }

        var li = $('<li />', {
            'data-id': store.id,
            class: liClass,
        }).click(function () {
            selectLi($(this), true, ['#map-canvas', 400], allMarkers);
        });

        // StoreName
        $('<div />', {
            html: '<h2>' + store.name + '<span id="calculatedDistance' + store.id + '" class="distance"></span></h2>',
            class: store.proximity ? 'storeTitle' : '',
            style: 'cursor: pointer',
        }).appendTo(li);

        // Description
        var description = $('<div />', {
            class: 'description row',
            id: 'description',
        }).appendTo(li);

        var addresses = $('<div />', {
            class: 'col col-sm-12 col-md-6 col-lg-12',
        }).appendTo(description);

        $('<div />', {
            class: 'address',
            id: 'address',
            html: '<p>' + store.address + ' </p><p>' + store.zip + ' ' + store.country + ' - ' + store.city + '</p>',
        }).appendTo(addresses);

        $('<div />', {
            class: 'address',
            id: 'info',
            html: '<p>' + store.info + '</p>',
        }).appendTo(addresses);

        var additionalInfo = $('<div />', {
            class: 'col col-sm-12 col-md-6 col-lg-12 additionalInfo',
        })
            .appendTo(description)
            .hide();

        if (typeof store.phone != 'undefined' && store.phone.length) {
            $('<div />', {
                class: 'info',
                id: 'phone',
                html:
                    '<p><span class="subject">' +
                    LC.global.languageSheet.phone +
                    ': </span>' +
                    store.phone +
                    (store.fax
                        ? '</p><p><span class="subject">' + LC.global.languageSheet.fax + ': </span>' + store.fax + '</p>'
                        : ''),
            }).appendTo(additionalInfo);
        }

        if (store.email.length) {
            $('<div />', {
                class: 'info',
                id: 'email',
                html: '<p><span class="email">' + LC.global.languageSheet.email + ': </span>' + store.email + '</p>',
            }).appendTo(additionalInfo);
        }

        if (typeof store.hours !== 'undefined' && store.hours.length) {
            var hours = $.parseJSON(store.hours);

            if (hours.length !== 0) {
                var timetable = $('<div />', {
                    class: 'info timetable',
                }).appendTo(additionalInfo);

                hours.forEach(function (el) {
                    var hoursContent = el.hours;
                    var additionalClass = ' ' + el.day;

                    if (hoursContent.length === 0) {
                        hoursContent += LC.global.languageSheet.closed;
                        additionalClass += ' closed';
                    }

                    $('<div />', {
                        class: 'day' + additionalClass,
                        // FIXME LC.global.languageSheet[el.day.toUpperCase()] || el.day es trencarà perque el label es dinamic
                        html:
                            '<span class="name">' +
                            (LC.global.languageSheet[el.day.toUpperCase()] || el.day) +
                            '</span> <span class="hours">' +
                            hoursContent +
                            '</span>',
                    }).appendTo(timetable);
                });
            }
        }

        if (LC.map.mapKind == 'gmap') {
            var how = $('<div />', {
                class: 'how',
                id: 'how',
                html:
                    '<a>' +
                    LC.global.languageSheet.locatorHowToGetThere +
                    ' </a><span class="glyphicon glyphicon-pushpin"></span>',
                style: 'cursor: pointer',
            }).appendTo(additionalInfo);
        }

        var print = $('<div />', {
            class: 'print',
            id: 'print',
            html: '<a>' + LC.global.languageSheet.printProdCompare + ' </a><span class="glyphicon glyphicon-print"></span>',
            style: 'cursor: pointer',
        }).appendTo(additionalInfo);

        if (LC.mapOptions.usePickup == 'true' && (store.stockAvailable || showStockWarehouses == 1)) {
            var htmlContent = '<a data-id=' + store.id;
            if (store.shippingSectionId) {
                htmlContent += ' data-shippingSectionId=' + store.shippingSectionId;
                htmlContent += " data-dropPoint='" + JSON.stringify(store) + "'";
            }
            htmlContent += ' class="' + BTN_DEFAULT_CLASS + '">' + LC.global.languageSheet.basketStepWarehouseSelectionButton + '</a>';

            var setPickup = $('<div/>', {
                class: 'setPickup',
                id: 'setPickup',
                html: htmlContent,
                style: 'cursor: pointer',
            }).appendTo(additionalInfo);
        }
        return li;
    };

    // Action to be triggered when a <li> tag is clicked.
    var selectLi = function ($el, centerMap, scrollTo, allMarkers) {
        var liSelect = $('ol li.isSelected');

        for (var i = 0; i < allMarkers.length; i++) {
            var iconUrl = LC.global.settings.commerceCdnPath + 'img/maps/icon.png';
            var markerSelected = LC.global.settings.commerceCdnPath + 'img/maps/marker_selected_icon.png';

            allMarkers[i].setIcon(iconUrl);
        }

        if (LC.map.mapKind !== 'baidu') LC.map.markersDict[$el.data('id')].setIcon(markerSelected);

        if (centerMap) {
            var marker = LC.map.markersDict[$el.data('id')];
            LC.map.setMapsArea(null, null, null, null, null, marker);
        }

        if ($el.hasClass('isSelected')) return true;

        if (liSelect.length > 0) {
            if (LC.map.directionsDisplay) {
                LC.map.directionsDisplay.setMap(null);
                LC.map.directionsDisplay.setPanel(null);
                $('#toolbar').slideUp();
            }
            liSelect.removeClass('isSelected');
        }
        $el.addClass('isSelected');

        $('.additionalInfo').slideUp();
        $el.find('.additionalInfo').slideDown(500, function () {
            $('.storeLocatorList').animate(
                {
                    scrollTop: $('.storeLocatorList').scrollTop() + $el.position().top - 15,
                },
                'slow'
            );
        });

        //if (scrollTo) {
        //  goToByScroll($(scrollTo[0]), scrollTo[1]? scrollTo[1]:0);
        //}
    };

    // Returns the bootstrap environ that is currently being displayed.
    var findBootstrapEnvironment = function () {
        var envs = ['ExtraSmall', 'Small', 'Medium', 'Large'];
        var envValues = ['xs', 'sm', 'md', 'lg'];

        var $el = $('<div>');
        $el.appendTo($('body'));

        for (var i = envValues.length - 1; i >= 0; i--) {
            var envVal = envValues[i];

            $el.addClass('d-' + envVal + '-none'); // BS5
            if ($el.is(':hidden')) {
                $el.remove();
                return envs[i];
            }
        }
    };

    // Method to scroll to some object.
    var goToByScroll = function (object, minus) {
        if (findBootstrapEnvironment() == 'Large') {
            return;
        }
        var minus = minus ? minus : 0;
        $('.storeLocatorList').animate(
            {
                scrollTop: object.offset().top - minus,
            },
            'slow'
        );
    };

    var callbackSuccess = function (response) {
        initStores(response);

        if (typeof initStoresCallback === 'function') {
            initStoresCallback($('#stores-list'));
        }

        LC.map.getCurrentPosition();
    };

    LC.mapOptions = $('#map-canvas').data('lcMapOptions');

    $('#listMap').show();
    $('#backToCommerce').show();
    $('#changeStoreLocatorCriteria').show();
    $('#storeLocatorListTitle').show();
    $('#addressComplete').val('');

    // if (!LC.map) {
    LC.map = new mapCanvas(document.getElementById('map-canvas'), LC.mapOptions);
    LC.map.draw();
    // }

    LC.map.reSetMarkersMap(true);

    LC.mapSubmit = $('#map-canvas').data('lcMapSubmit');
    $('.locatorForm').slideUp();

    var place = LC.mapSubmit.place;
    var country = LC.mapSubmit.country;
    var state = LC.mapSubmit.state;
    var city = LC.mapSubmit.city;
    var zip = LC.mapSubmit.zip;
    var proximity = LC.mapSubmit.proximity === true ? true : false;

    // Check that minimum information is passed to the lcMapSubmit parameter to display some stores.
    if (!(place || country || state || city)) return;

    if (proximity) {
        var display = LC.map.mapKind == 'gmap' ? true : false;
        LC.map.userMarker = LC.map.addMarker(
            place.center.lat,
            place.center.lng,
            -1,
            null,
            null,
            LC.map.userIcon,
            display
        );
        LC.map.addEventMarker(LC.map.userMarker, 'click', 'centerMarker', LC.map);
    }

    $('#driving').click(function () {
        LC.map.traceTravel('DRIVING');
        $('#toolbar .btn').removeClass('isActive');
        $(this).addClass('isActive');
    });
    $('#walking').click(function () {
        LC.map.traceTravel('WALKING');
        $('#toolbar .btn').removeClass('isActive');
        $(this).addClass('isActive');
    });

    var shippingSectionId = parseInt($('input[type="hidden"][name="shippingSectionId"]').val());

    $.ajax({
        url: 'storeLocator/stores', // controller::getStores
        data: {
            city: city,
            state: state,
            country: country,
            zip: zip,
            place: JSON.stringify(place),
            mapOptions: JSON.stringify(LC.mapOptions),
            shippingSectionId: shippingSectionId,
        },
        type: 'post',
        crossDomain: true,
    }).done(callbackSuccess.bind(LC));
};

/****************************************/
/*       LS TAGS JS OBJECTS             */
/****************************************/

/**
* @class LC.selectStoreLocatorForm
* @memberOf LC
* @extends {LC.Form}
*/
LC.selectStoreLocatorForm = LC.Form.extend({
    name: 'selectStoreLocatorForm',
    options: {},
    initialize: function () {
        if (this.el.$form.find('#country').length) {
            if (this.el.$form.find('#state').length) {
                this.el.$form.find('#country').on('change', this.loadStates.bind(this));
            }
        } else if (this.el.$form.find('#state').length) {
            this.loadStates();
        } else if (this.el.$form.find('#city').length) {
            this.loadCities();
        }
    },

    loadStates: function (event) {
        var self = this;
        if (event) {
            var parentType = event.currentTarget.name;
            var parentId = event.currentTarget.value;
        }

        $(this.el.$form.find('#searchBySelect')).prop('disabled', true);

        if (!self.el.$form.find('#state').length)
            if ($('#country option:selected').attr('value'))
                $(this.el.$form.find('#searchBySelect')).prop('disabled', false);

        if (self.el.$form.attr('restricted') == 'false') {
            if (
                $(this.el.$form.find('#country option:selected')).attr('value') ||
                $(this.el.$form.find('#state option:selected')).attr('value')
            )
                $(this.el.$form.find('#searchBySelect')).prop('disabled', false);
        }

        var callbackSuccess = function (response) {
            var options = '';
            if (response.status.code == 200 && response.data.data) {
                var arrStates = [];
                $.each(response.data.data, function (k, state) {
                    arrStates.push(state);
                });

                arrStates = arrStates.sort(function (a, b) {
                    return a.value > b.value ? 1 : -1;
                });

                options += '<option class="default" value="">' + LC.global.languageSheet.state + '</option>';
                $.each(arrStates, function (k, state) {
                    options += '<option class="default" value="' + state.locationId + '">' + state.value + '</option>';
                });

                $(self.el.$form.find('#state')).empty();
                if (self.el.$form.find('#city').length) {
                    self.el.$form
                        .find('#state')
                        .append(options)
                        .on('change', self.loadCities.bind(self));
                } else {
                    $(self.el.$form.find('#state'))
                        .append(options)
                        .on(
                            'change',
                            function () {
                                $(self.el.$form.find('#searchBySelect')).prop('disabled', false);
                            }.bind(self)
                        );
                }

                $(self.el.$form.find('#state'))
                    .prop('disabled', false)
                    .parent()
                    .removeClass('disabled');

                $(self.el.$form.find('#state option:first-child')).prop('selected', true);
            }
        };

        $(this.el.$form.find('[parent^=state]'))
            .prop('disabled', true)
            .parent()
            .addClass('disabled');
        if (parentType == 'country') {
            $.getJSON(
                LC.global.routePaths.PHYSICAL_LOCATION_INTERNAL_STATES + '?countryCode=' + encodeURIComponent(parentId),
                callbackSuccess.bind(this));
        } else {
            $.getJSON(LC.global.routePaths.PHYSICAL_LOCATION_INTERNAL_STATES, callbackSuccess.bind(this));
        }
    },

    loadCities: function (event) {
        var self = this;
        if (event) {
            var parentType = event.currentTarget.name;
            var parentId = event.currentTarget.value;
            var countryId = this.el.$form.find('#country').val();
        }
        $(this.el.$form.find('#searchBySelect')).prop('disabled', true);

        if (!self.el.$form.find('#city').length)
            if ($('#country option:selected').attr('value'))
                $(this.el.$form.find('#searchBySelect')).prop('disabled', false);

        if (self.el.$form.attr('restricted') == 'false') {
            if (
                $(this.el.$form.find('#country option:selected')).attr('value') ||
                $(this.el.$form.find('#state option:selected')).attr('value')
            )
                $(this.el.$form.find('#searchBySelect')).prop('disabled', false);
        }

        var callbackSuccess = function (response) {
            var options = '';
            if (response.status.code == 200 && response.data.data) {
                var arrCities = [];
                $.each(response.data.data, function (k, city) {
                    arrCities.push(city);
                });
                arrCities = arrCities.sort(function (a, b) {
                    return a.value > b.value ? 1 : -1;
                });

                options += '<option class="default" value="">' + LC.global.languageSheet.city + '</option>';
                $.each(arrCities, function (k, city) {
                    options += '<option class="default" value="' + city.locationId + '">' + city.value + '</option>';
                });

                $(self.el.$form.find('#city')).empty();
                $(self.el.$form.find('#city'))
                    .append(options)
                    .change(
                        function () {
                            $(self.el.$form.find('#searchBySelect')).prop('disabled', false);
                        }.bind(self)
                    );

                $(self.el.$form.find('#city'))
                    .prop('disabled', false)
                    .parent()
                    .removeClass('disabled');

                $(self.el.$form.find('#city option:first-child')).prop('selected', true);
            }
        };
        var arrParams = [];

        var isPickup = $('#outputSelectStoreLocatorFormUsePickup').val();
        if (isPickup) arrParams.push('isPickup=' + isPickup);

        var shippingSectionId = parseInt($('input[type="hidden"][name="shippingSectionId"]').val());
        if (shippingSectionId > 0) arrParams.push('shippingSectionId=' + shippingSectionId);

        var countriesUrl = LC.global.routePaths.PHYSICAL_LOCATION_INTERNAL_CITIES;
        paramChar = '?';
        if (arrParams.length) {
            countriesUrl += '?' + arrParams.join('&');
            paramChar = '&';
        }

        if (parentType == 'state') {
            $.getJSON(countriesUrl + paramChar + 'countryCode=' + countryId + '&state=' + encodeURIComponent(parentId), callbackSuccess.bind(this));
        } else if (parentType == 'country') {
            $.getJSON(countriesUrl + paramChar + 'countryCode=' + encodeURIComponent(parentId), callbackSuccess.bind(this));
        } else {
            $.getJSON(countriesUrl, callbackSuccess.bind(this));
        }
    },

    submit: function (event) {
        // MAP Exists on same page, no real submit needed
        if ($('#map-canvas').length) {
            var arrDataForm = this.el.$form.serializeArray();
            var dataForm = {};
            for (var i = 0; i < arrDataForm.length; i++) {
                if (!(arrDataForm[i].name in dataForm)) {
                    dataForm[arrDataForm[i].name] = [];
                }

                dataForm[arrDataForm[i].name].push(arrDataForm[i].value);
            }

            for (var i in dataForm) {
                dataForm[i] = dataForm[i].join();
            }

            $('#map-canvas').data('lcMapSubmit', dataForm);
            initListMap();
        } else {
            this.el.form.submit();
        }

        event.preventDefault();
    },
});

/**
* @class LC.selectMapKindForm
* @memberOf LC
* @extends {LC.Form}
*/
LC.SelectMapKindForm = LC.Form.extend({
    name: 'selectMapKindForm',
    popupName: '#selectMapKindModalPopup',
    cookiePopupName: COOKIE_SELECT_MAP_KIND_POPUP,

    initialize: function (form) {
        if (this.el.form.initialized) return;

        var objSelectMapKindPopup = $('#selectMapKindModalPopup');
        if (objSelectMapKindPopup.length == 1) {
            this.el.popup = objSelectMapKindPopup;
        } else {
            this.el.popupInclusted = $(E(this.includedPopupName));
            this.el.popupMark = $(E(this.includedPopupMarkName));
        }

        $('.setMapKindDiv').click(this.submit.bind(this));

        this.el.form.initialized = true;
        this.trigger('initializeBefore');
        this.callback = this.callback.bind(this);
    },

    submit: function (event) {
        $('#mapKindInput').val(event.target.id == 'setMapKindBaidu' ? 'baidu' : 'gmap');

        // Setting cookie if not exist only when the user click submit. if we put it into the init section or onLoad the cookie, with f5 we can bypass the "coockie restriction" because
        // it's defined without enter the information.
        Cookies(this.cookiePopupName, 0, { path: '/' });

        // After set the cookie we call the super element function submit to submit the form.
        this.superForm('submit', event);
    },

    callback: function (response) {
        //This variable is for the LC Tag attribute internalRedirectUrl (/home,/categories/.....)
        var internalRedirectUrl = $('#internalRedirectUrl').val();
        // We save the url to get the correct url in case the user comes to http://home.com/pants and not to http://home.com directly
        var urlComeFrom = window.location.href;

        var resultOfTypeOfPageValidation = this.internalRedirectUrlValidator(internalRedirectUrl);

        if (typeof response !== 'undefined') {
            if (response.status.code == 200) {
                // If response.response.success is 201 means that mapKind has changed so page needs to be refreshed
                if (response.response.success == 201) {
                    if (
                        internalRedirectUrl != null &&
                        internalRedirectUrl.length > 0 &&
                        resultOfTypeOfPageValidation == true
                    ) {
                        window.location.href = internalRedirectUrl;
                        return;
                    }

                    window.location.href = urlComeFrom;
                } else if (response.response.success != 200) {
                    window.location.href = urlComeFrom;
                    return;
                }

                if (typeof this.el.popup !== 'undefined') {
                    this.el.popup.modal('hide');
                }
            } else {
                this.el.$message.text(response.response.message).addClass('alert alert-danger');
            }
        }
    },

    internalRedirectUrlValidator: function (urlToValidate) {
        var myRegExp = /^[\/][\w]+/gim;

        if (!myRegExp.test(urlToValidate)) {
            return false;
        } else {
            return true;
        }
    },
});

/**
* @class LC.addressLocatorForm
* @memberOf LC
* @extends {LC.Form}
*/
LC.addressLocatorForm = LC.Form.extend({
    name: 'addressLocatorForm',
    options: {},
    initialize: function () {
        // if (!$('.storeLocatorTag').length) {
        $('.locatorForm').show();
        // }

        $(this.el.$form.find('#searchByAddress')).prop('disabled', true);

        //Activate Autocomplementation
        this.autocomplete = new google.maps.places.Autocomplete($(this.el.$form.find('#addressComplete'))[0], {
            types: ['geocode'],
        });

        this.el.$form.find('#addressComplete').on(
            'keydown',
            function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            }.bind(this)
        );

        google.maps.event.addListener(
            this.autocomplete,
            'place_changed',
            function () {
                $(this.el.$form.find('#searchByAddress')).prop('disabled', false);
                var place = this.autocomplete.getPlace();
                this.place = this.getPlaceCoords(place);
            }.bind(this)
        );
    },
    getPlaceCoords: function (place) {
        if (!place.geometry) return;
        if (typeof place.geometry.viewport === 'undefined') {
            return {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng(),
            };
        } else {
            var neCoords = place.geometry.viewport.getNorthEast(),
                swCoords = place.geometry.viewport.getSouthWest();

            return {
                center: {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                },
                bounds: {
                    maxLat: neCoords.lat(),
                    minLat: swCoords.lat(),
                    maxLng: neCoords.lng(),
                    minLng: swCoords.lng(),
                },
            };
        }
    },
    submit: function (event) {
        event.preventDefault();
        $('.pac-container').hide();
        if (this.place == null) {
            if ($(this.el.$form.find('#address')).val() == '') {
                $(this.el.$form.find('#addressForm')).addClass('error');
                return;
            }

            var service = new google.maps.places.AutocompleteService();
            var self = this;
            service.getPlacePredictions(
                { input: $(this.el.$form.find('#address')).val(), types: ['geocode'] },
                function (predictions, status) {
                    if (status != google.maps.places.PlacesServiceStatus.OK) {
                        $(self.el.$form.find('#addressForm')).addClass('error');
                        return;
                    }

                    var placeService = new google.maps.places.PlacesService(document.getElementById('service'));

                    placeService.getDetails({ placeId: predictions[0].place_id }, function (result, status) {
                        self.place = self.getPlaceCoords(result);
                    });

                    self.submitForm();
                }
            );
        } else {
            this.submitForm();
        }
    },
    submitForm: function () {
        $(this.el.$form.find('#addressForm')).removeClass('error');
        // MAP Exists on same page, no real submit needed
        if ($('#map-canvas').length) {
            var newSubmit = { place: this.place, proximity: 'false' };
            $('#map-canvas').data('lcMapSubmit', newSubmit);
            initListMap();
        } else {
            // this.el.form.place.value = this.place;
            this.el.form.place.value = JSON.stringify(this.place);
            this.el.form.submit();
        }
    },
});

/**
* @class LC.geoLocatorForm
* @memberOf LC
* @extends {LC.Form}
*/
LC.geoLocatorForm = LC.Form.extend({
    name: 'geoLocatorForm',
    options: {},
    initialize: function () {
        $(this.el.$form.find('#searchByGeoloc')).click(this.submit.bind(this));
        // if (!$('.storeLocatorTag').length) {
        $('.locatorForm').show();
        // }
    },

    checkAcquiredLocation: function () {
        if (!this.place || !this.place.center || !this.place.center.lat || !this.place.center.lng) {
            message = LC.global.languageSheet.locatorGeoLocatorError;
            this.el.$message.html(message).addClass('alert alert-danger');
            $('#lcNotify')
                .find('[data-lc-event]')
                .dataEvent();
            return false;
        }

        return true;
    },

    submit: function (event) {
        event.preventDefault();
        if (typeof this.yourLat == 'undefined' && typeof this.yourLng == 'undefined') {
            if (!navigator.geolocation) {
                this.checkAcquiredLocation();
                return;
            }

            var self = this;
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    self.yourLat = position.coords.latitude;
                    self.yourLng = position.coords.longitude;
                    self.submitForm();
                },
                function (err) {
                    // ALWAYS timeouts
                    if (!self.checkAcquiredLocation()) return;
                    // self.submitForm();
                },
                { enableHighAccuracy: true, timeout: 100000 }
            );
        } else {
            if (!this.checkAcquiredLocation()) return;

            this.submitForm();
        }
    },
    submitForm: function () {
        this.place = { center: { lat: this.yourLat, lng: this.yourLng } };

        // MAP Exists on same page, no real submit needed
        if ($('#map-canvas').length) {
            $('#map-canvas').data('lcMapSubmit', { place: this.place, proximity: true });
            initListMap();
        } else {
            this.el.form.place.value = JSON.stringify(this.place);
            this.el.form.submit();
        }
    },
});