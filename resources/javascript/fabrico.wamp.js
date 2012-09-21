Fabrico.wamp = {};

/**
 * @var Autobahn Session
 */
Fabrico.wamp.connection = null;

/**
 * @abstract
 */
Fabrico.wamp.onOpen = function () {};

/**
 * @abstract
 */
Fabrico.wamp.onClose = function (code, reason) {};

/**
 * opens a websocket and subscribes events and their handlers
 * when ready
 *
 * @param object uri
 * @param object subscriptions
 */
Fabrico.wamp.open = function (uri, subscriptions) {
	if ($.isPlainObject(uri)) {
		if ("port" in uri) {
			uri = sprintf("ws://%s:%s", location.host, uri.port);
		}
	}

	ab.connect(
		uri,

		// open
		function (session) {
			Fabrico.wamp.connection = session;
			Fabrico.wamp.onOpen();

			// subscribe
			if (subscriptions) {
				for (var subscription in subscriptions) {
					Fabrico.wamp.connection.subscribe(subscription, subscriptions[ subscription ]);
				}
			}
		},

		// close
		function (code, reason) {
			Fabrico.wamp.onClose(code, reason);
		}
	);
};


/**
 * samples:

Fabrico.wamp.open({ port: 9000 }, {
	chat: console.log.bind(console) 
});

 */
