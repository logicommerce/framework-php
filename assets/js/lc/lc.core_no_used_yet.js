/**
 * ATTENTION
 * 
 * If you take a block of JS from here, remove it from the list or if you deprecate it,
 * make a note of it.
 * 
 * File classes:
 * - LC.initializeIncrementCounter      --> uncomment initialize in lc.initialize.js
 * - LC.setConfirmOrder                 --> uncomment initialize in lc.initialize.js
 */

/**
 * LC.initializeIncrementCounter
 * @todo controller php incrementCounter
 */
LC.initializeIncrementCounter = function() {
    var listIds = [];
    $('[data-lc-incrementCounter-data]').each(function() {
        var info = $(this).data('lc-incrementcounter-data');
        switch (info.type) {
            case 1: //take all id's to count the impressions
                if (!listIds.hasOwnProperty(info.item)) {
                    listIds[info.item] = info.id + ',';
                } else {
                    listIds[info.item] += info.id + ',';
                }
                break;

            case 2: //add the event click to count the clicks and redirect to a URL
                $(this).on('click mousedown', function(e) {
                    e.preventDefault();
                    if (e.type == 'mousedown' && e.which == 1) {
                        //to prevent double event and double click counter
                        return;
                    }
                    var redirect = 0;
                    if (e.which == 1) {
                        //only redirect if the click is with the left mouse button
                        redirect = 1;
                    }
                    var url = '/incrementCounter/' + info.type + '/' + info.id + '/' + info.item + '/' + redirect + '?target=' + encodeURIComponent($(this).attr('href'));

                    if (redirect) {
                        $(location).attr('href', url);
                    } else {
                        $.get(url, function() {}); //get doing ajax to don't reload the nav
                    }
                });
                break;

            default:
        }
    });

    for (var key in listIds) {
        if (listIds.hasOwnProperty(key) && listIds[key].length) {
            LC.objIncrementCounter(1, listIds[key], key);
        }
    }
};

/**
 * LC.setConfirmOrder
 * @todo all
 */
LC.setConfirmOrder = function(element) {
    $el = $(element);
    var data = $el.data('lcConfirmorder');

    $el.load('/checkout/setConfirmOrder/?' + data, {}, function(data, status, xhrResponse) {});
};