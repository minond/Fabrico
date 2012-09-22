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
Fabrico.wamp.connect = function (info, subscriptions) {
	var uri;

	if (!info) {
		info = {};
	}

	if ("port" in info) {
		uri = sprintf("ws://%s:%s", location.host, info.port);
	}

	if (!subscriptions && "bind" in info) {
		info.saveto = info.bind.socket;
		subscriptions = {};
		subscriptions[ info.bind.namespace ] = info.bind.handle;
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
 * wamp helper
 *
 * @param string namespace
 */
Fabrico.wamp.manager = function (namespace) {
	// for subscriptions
	this.namespace = namespace || "send";

	// ui related methods go here
	this.ui = {};

	// standard methods go here
	this.action = {};

	// socket publishers go here
	this.socket = {};

	// socket subscriptions go here
	this.handle = {};
};


/**
 * samples:

Fabrico.wamp.connect({ port: 9000 }, {
	chat: console.log.bind(console) 
});

 */
