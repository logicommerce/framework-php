LC.mediaqueries = {
    registerMQ: function(name, mq) {
        if (typeof name !== 'string' || typeof mq !== 'string' || typeof this[name] === 'object') return false;
        this[name] = {
            firstime: true,
            match_cb: [],
            unmatch_cb: [],
            setup_cb: [],
            init: function() {
                //setup mq passing this as context, and mq as an argument
                LC.mediaqueries.setupMQ.call(this, mq);
            },
            setup: function(cb, args) {
                //if matches executes function if not deffers it to first match
                if (typeof cb === 'function') {
                    if (!this.firstime) {
                        cb.apply(cb, args);
                    } else {
                        this.setup_cb.push([cb, args]);
                    }
                }
            },
            match: function(cb, args) {
                //add callback to matches callback array
                if (this.mediaQuery.matches) {
                    cb.apply(cb, args);
                }
                if (typeof cb === 'function') {
                    this.match_cb.push([cb, args]);
                }
            },
            unmatch: function(cb, args) {
                //add callback to unmatches callback array
                if (typeof cb === 'function') {
                    this.unmatch_cb.push([cb, args]);
                }
            },
            remove: function(type, cb) {
                //remove callback to maches callback array
                LC.mediaqueries.removeCallback(this[type + '_cb'], cb);
            },
        };

        this[name].init();
    },
    setupMQ: function(mq) {
        var self = this;
        //setup mediaquery
        self.mediaQuery = window.matchMedia(mq);
        //setup listeners and its callbacks
        self.mediaQuery.addListener(function(changed) {
            if (changed.matches) {
                //setup is only executed when mediaquery is matched for first time
                if (self.firstime) {
                    LC.mediaqueries.run(self.setup_cb);
                    self.firstime = false;
                }
                LC.mediaqueries.run(self.match_cb);
            } else {
                LC.mediaqueries.run(self.unmatch_cb);
            }
        });
    },
    removeCallback: function(arr, cb) {
        if (Array.isArray(arr)) {
            arr.forEach(function(e, i) {
                if (e[0] === cb) {
                    arr.splice(i, 1);
                    return arr;
                }
            });
        } else {
            return false;
        }
    },
    run: function(cbs) {
        cbs.forEach(function(cb) {
            cb[0].apply(cb[0], cb[1]);
        });
    },
};
