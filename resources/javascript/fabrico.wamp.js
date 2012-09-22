Fabrico.wamp = {};

/**
 * @var object
 */
Fabrico.wamp.pub = {};

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
Fabrico.wamp.open = function (info, subscriptions) {
	var uri;

	if ($.isPlainObject(info)) {
		if ("port" in info) {
			uri = sprintf("ws://%s:%s", location.host, info.port);
		}
	}

	ab.connect(uri, function (session) {
			// open
			Fabrico.wamp.connection = session;
			Fabrico.wamp.onOpen();

			// subscribe
			if (subscriptions) {
				for (var namespace in subscriptions) {
					for (subscription in subscriptions[ namespace ]) {
						Fabrico.wamp.connection.subscribe(
							sprintf("%s:%s", namespace, subscription),
							subscriptions[ namespace ][ subscription ]
						);
					}
				}
			}
		}, function (code, reason) {
			// close
			Fabrico.wamp.onClose(code, reason);
		}
	);

	// publishers
	if (subscriptions) {
		var method_storage = "saveto" in info ? info.saveto : Fabrico.wamp.pub;

		for (var namespace in subscriptions) {
			for (var subscription in subscriptions[ namespace ]) {
				if (!(namespace in method_storage)) {
					method_storage[ namespace ] = {};
				}

				(function (namespace, subscription) {
					method_storage[ namespace ][ subscription ] = function (args) {
						Fabrico.wamp.connection.publish(
							sprintf("%s:%s", namespace, subscription), args
						);
					};
				})(namespace, subscription);
			}
			
			if ("saveto" in info) {
				method_storage[ namespace ].socket = {
					connection: function () {
						return Fabrico.wamp.connection;
					},
					close: function () {
						this.connection().close();
					}
				};
			}
		}
	}
};


/**
 * samples:

Fabrico.wamp.open({ port: 9000 }, {
	chat: console.log.bind(console) 
});

 */
