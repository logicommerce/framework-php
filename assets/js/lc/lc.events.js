'use strict';
LC.events = {
    callbacks: {},
    scroll: {},
    resize: {},
    scroll_rates: [],
    resize_rates: [],

    addEvent: function(name, config) {
        var self = this,
            event = config.event && (config.event === 'scroll' || config.event === 'resize') ? config.event : false,
            method = config.method || false,
            rate = config.rate || 50,
            trailing = typeof config.trailing === 'boolean' ? config.trailing : false;

        if (!event || !method) {
            throw 'Bad formed event config in config.json events';
        }

        //register debounce/throttle function
        self[event][name] = $[method].apply($[method], [
            rate,
            trailing,
            function() {
                $(window).trigger(name);
            },
        ]);

        //create event callbacks array
        self.callbacks[name] = [];

        //save rate
        self[event + '_rates'].push({ rate: rate, name: name });
    },
    addCallback: function(event, cb, args) {
        // prevent fails if callback array doesn't exist i.e. the event has not been defined in config.json
        if (!Array.isArray(LC.events.callbacks[event])){
            return;
        }
        LC.events.callbacks[event].push([cb, args]);
    },
    removeCallback: function(event, cb) {
        var self = this,
            arr = self.callbacks[event];

        if (Array.isArray(arr)) {
            arr.forEach(function(e, i) {
                if (e[0] === cb) {
                    arr.splice(i, 1);
                }
            });
        } else {
            return false;
        }
    },
    getMinimumRate: function(event) {
        //select minimum rate for updating scrollTop and Window size, and add corresponding callback depending on event
        var self = this;
        //extract rates array
        var rates = self[event + '_rates'].map(function(ev) {
            return ev.rate;
        });
        //extract events array
        var events = self[event + '_rates'].map(function(ev) {
            return ev.name;
        });

        if (rates.length === 0) {
            return -1;
        }

        var min = rates[0];
        var minIndex = 0;

        for (var i = 1; i < rates.length; i++) {
            if (rates[i] < min) {
                minIndex = i;
                min = rates[i];
            }
        }

        self.addCallback(events[minIndex], self[event + '_cb'].bind(self));
        return true;
    },

    resize_cb: function() {
        // get window size
        this.windowWidth = document.documentElement.clientWidth;
        this.windowHeight = document.documentElement.clientHeight;
    },
    scroll_cb: function() {
        // get scrollTop
        this.scrollTop = $(this.scrollContainer).scrollTop();
    },
    init: function() {
        // if there's no events config defined do not initialize
        if (typeof themeConfiguration.events !== 'object' || typeof themeConfiguration.events.setup !== 'object') {
            return;
        }

        var self = this;

        self.scrollContainer = themeConfiguration.events.scrollContainer || window;

        //add events from config JSON
        Object.keys(themeConfiguration.events.setup).forEach(function(name) {
            self.addEvent(name, themeConfiguration.events.setup[name]);
        });

        self.getMinimumRate('scroll');
        self.getMinimumRate('resize');

        self.resize_cb();
        self.scroll_cb();

        // attach a listener to scroll control
        $(self.scrollContainer).on('scroll', function() {
            Object.keys(LC.events.scroll).forEach(function(key) {
                LC.events.scroll[key]();
            });
        });
        // attach a listener to window resize
        $(window).on('resize', function() {
            Object.keys(LC.events.resize).forEach(function(key) {
                LC.events.resize[key]();
            });
        });

        //execute callbacks array on each callback
        Object.keys(LC.events.callbacks).forEach(function(key) {
            $(window).on(key, function() {
                LC.events.callbacks[key].forEach(function(cb) {
                    //cb[0] = function, cb[1], args array
                    if (typeof cb[0] !== 'undefined' && typeof cb[0] == 'function') {
                        cb[0].apply(cb[0], cb[1]);
                    }
                });
            });
        });
    },
};

LC.initQueue.enqueue(function() {
    LC.events.init();
});
