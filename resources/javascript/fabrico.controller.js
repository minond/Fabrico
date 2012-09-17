/**
 * @name controller
 * @var object
 */
Fabrico.controller = {
	response: {
		ERROR: "error",
		SUCCESS: "success",
		IN_PROCESS: "in_process",
		INVALID_SESSION: "invalid_session",
		PRIVATE_METHOD: "private_method",
		UNKNOWN_METHOD: "unknown_method"
	},

	std: {
		get_node_content: "get_node_content",
		session_id: null,
		destination: location.href
	}
};

/**
 * defalt request destination
 * @var string
 */
Fabrico.controller.DESTINATION = Fabrico.controller.std.destination;

/**
 * destination setter
 * @param string url
 * @return Fabrico controller object
 */
Fabrico.controller.receiver = function (src) {
	this.DESTINATION = src || this.std.destination;
	return this;
};

/**
 * element update helper
 * @param array of element ids
 * @param object optional controller properties
 * @param function before request is made
 * @param function success handler
 * @param function error handler
 */
Fabrico.controller.update = function (ids, env, before, callback, errback) {
	if (!$.isArray(ids)) {
		ids = [ ids ];
	}

	return this.request({
		_update: ids
	}, [], env, before, function (response, stat, promise) {
		if (response.status === Fabrico.controller.response.SUCCESS) {
			for (var i = 0, max = ids.length; i < max; i++)
				$(document.getElementById(ids[ i ])).html(response.response[ ids[ i ] ]);

			if (callback && callback instanceof Function) {
				callback(response, stat, promise);
			}
		}
	}, errback);
}

/**
 * @name method
 * @param string method name
 * @param array optional arguments
 * @param array of components to update
 * @param object optional controller properties
 * @param function before request is made
 * @param function success handler
 * @param function error handler
 * @return Promise
 * @see request
 */
Fabrico.controller.method = function (method, args, updates, env, before, callback, errback) {
	if (!$.isArray(updates)) {
		updates = [ updates ];
	}

	return this.request({
		_method: method,
		_update: updates
	}, args, env, before, function (response, stat, promise) {
		if (response.status === Fabrico.controller.response.SUCCESS) {
			if (updates && updates.length)
				for (var i = 0, max = updates.length; i < max; i++)
					$(document.getElementById(updates[ i ])).html(response.response[ updates[ i ] ]);

			if (callback && callback instanceof Function) {
				callback(response, stat, promise);
			}
		}
		else {
			if (errback && errback instanceof Function) {
				errback(response, stat, promise);
			}
		}
	}, errback);
};

/**
 * @name request
 * @param object default request variables
 * @param array optional arguments
 * @param object optional controller/global properties
 * @param function before request is made
 * @param function success handler
 * @param function error handler
 * @return Promise
 * @see redirect
 */
Fabrico.controller.request = function (req, args, env, before, callback, errback) {
	var dest = this.DESTINATION;
	req._args = args || [];
	req._env = env || {};
	req._session_id = this.std.session_id;

	// reset
	this.receiver();

	if (before && $.isFunction(before)) {
		before(req);
	}

	return $.ajax({
		type: "POST", 
		async: true, 
		url: dest,
		success: callback || function () {},
		fail: errback || function () {},
		data: req
	});
};

/**
 * @name method_redirect
 * @param string method name
 * @param string method redirect
 * @param array optional arguments
 * @param object optional controller properties
 * @param function form pre-submission callback
 * @return node form element
 * @see redirect
 */
Fabrico.controller.method_redirect = function (method, redirect, args, env, preback) {
	return this.redirect({
		_method: method,
		_success: redirect
	}, args, env, preback);
};

/**
 * @name redirect
 * @param object default request variables
 * @param array optional arguments
 * @param object optional controller/global properties
 * @param function form pre-submission callback
 * @return node form element
 * @see request
 */
Fabrico.controller.redirect = function (req, args, env, preback) {
	var form = $("<form method='post'>");
	var fieldstr = "<input type='hidden' name='%s' value='%s' />";

	req._args = args || [];
	req._env = env || {};
	req._success = req._success || location.href;
	req._session_id = this.std.session_id;

	for (var i = 0, max = req._args.length; i < max; i++) {
		if ($.isPlainObject(req._args[ i ]) || $.isArray(req._args[ i ])) {
			req._args[ i ] = JSON.stringify(req._args[ i ]);
		}
	}

	for (var prop in req._env) {
		if ($.isPlainObject(req._env[ prop ]) || $.isArray(req._env[ prop ])) {
			req._env[ prop ] = JSON.stringify(req._env[ prop ]);
		}
	}

	form.attr("action", this.DESTINATION);

	$.each(req, function (key, value) {
		if ($.isArray(value))
			$.each(value, function (i, val) {
				$(sprintf(fieldstr, sprintf("%s[]", key), val)).appendTo(form);
			});
		
		else if ($.isPlainObject(value))
			$.each(value, function (prop, val) {
				$(sprintf(fieldstr, sprintf("%s[%s]", key, prop), val)).appendTo(form);
			});

		else
			$(sprintf(fieldstr, key, value)).appendTo(form);
	});

	if ($.isFunction(preback)) {
		preback(req, args, env, form);
	}

	return form.hide().appendTo(document.body).submit();
}
