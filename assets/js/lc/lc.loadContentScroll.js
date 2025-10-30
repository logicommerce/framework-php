(function($) {
    'use strict';

    LC.fn('lcScroll', {
        options: {
            baseUrl: '',
            autoTrigger: true,
            launchBase: true,
            dataType: 'html',
            loadingHtml: '<div>Loading...</div>',
            loadingClass: 'lc-scroll-isLoading',
            wrapper: 'div',
            callback: false,
            padding: 100,
            debug: false,
        },
        Constructor: function(element, options) {
            var self = this;
            var $window, $document, loading, request, loadingHtml;

            self.init = function() {
                debug('info', options);
                $window = $(window);
                $document = $(document);
                loadingHtml = '<div class="lc-scroll-loading">' + options.loadingHtml + '</div>';

                if (options.autoTrigger) self.initScroll();
            };

            self.initScroll = function() {
                setNextUrl();
                setLoading(false);

                if (options.launchBase) scroll(true);

                $window.scroll(scroll.bind(self, false));
            };

            function scroll(ignorePosition) {
                if (!canLoad(ignorePosition)) return;

                setLoading(true);
                var $wrapper = prepareWrapper();

                request = getData(getNextUrl());

                request.done(function(data, status, jqXHR) {
                    debug('info', 'request.done', arguments);
                    onLoad($wrapper, data, status, jqXHR);
                });

                request.fail(function() {
                    debug('error', 'request.fail', arguments);
                    $wrapper.remove();
                });
            }

            function getData(url) {
                return $.ajax({
                    url: url,
                    dataType: options.dataType,
                    method: 'GET',
                });
            }

            function canLoad(ignorePosition) {
                if (isLoading() || !getNextUrl()) return false;

                if (ignorePosition) return true;

                return $window.scrollTop() >= $document.height() - $window.height() - options.padding;
            }

            function isLoading() {
                return loading;
            }

            function setLoading(status) {
                loading = status;
                if (status) element.addClass(options.loadingClass);
                else element.removeClass(options.loadingClass);
            }

            function onLoad($wrapper, data, status, jqXHR) {
                setLoading(false);

                if (status == 'success') {
                    var nextUrl = $(data).data('lcScrollNextpage');

                    if (!nextUrl) nextUrl = jqXHR.getResponseHeader('lc-nextPage');

                    setNextUrl(nextUrl);
                    $wrapper.html(data);
                } else $wrapper.remove();

                if (options.callback && typeof options.callback === 'function')
                    options.callback(element, $wrapper, data, status, jqXHR);
            }

            function setNextUrl(nextUrl) {
                if (!nextUrl && !self.hasOwnProperty('nextUrl')) nextUrl = options.baseUrl || element.data('lcScroll');
                else if (typeof nextUrl === 'object') nextUrl = nextUrl.data('nexturl');

                self.nextUrl = nextUrl;
            }
            this.setNextUrl = setNextUrl;

            function getNextUrl() {
                return self.nextUrl ? self.nextUrl : '';
            }

            function prepareWrapper() {
                var wrapperTag = '<' + (options.wrapper || div) + '/>';
                var $wrapper = $(wrapperTag, { html: loadingHtml }).appendTo(element);
                return $wrapper;
            }

            function debug(dbg) {
                if (
                    options.debug &&
                    typeof console === 'object' &&
                    (typeof dbg === 'object' || typeof console[dbg] === 'function')
                ) {
                    if (typeof dbg === 'object') {
                        var args = [];
                        for (var prop in dbg) {
                            if (typeof console[prop] === 'function') {
                                args = dbg[prop].length ? dbg[prop] : [dbg[prop]];
                                console[prop].apply(console, args);
                            } else {
                                console.log.apply(console, args);
                            }
                        }
                    } else {
                        console[dbg].apply(console, Array.prototype.slice.call(arguments, 1));
                    }
                }
            }
        },
    });

    LC.initQueue.enqueue(function() {
        $('[data-lc-scroll]').each(function(index, el) {
            var $el = $(el);
            var baseUrl = $el.data('lcScroll');

            if (!baseUrl) return;

            $el.lcScroll({
                baseUrl: baseUrl,
                launchBase: $el.data('lcScrollLaunchbase') || true,
                autoTrigger: $el.data('lcScrollAutotrigger') || true,
                padding: $el.data('lcScrollPadding'),
                debug: $el.data('lcScrollDebug'),
                dataType: $el.data('lcScrollDatatype'),
                loadingHtml: $el.data('lcScrollLoadinghtml'),
                loadingClass: $el.data('lcScrollLoadingclass'),
                wrapper: $el.data('lcScrollWrapper'),
                callback: LC.carryMethod($el.data('lcScrollCallback')),
            });
        });
    });
})(jQuery);
