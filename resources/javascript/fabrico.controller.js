/**
 * @name controller
 * @var object
 */
Fabrico.controller = {
	response: {
		ERROR: "error",
		SUCCESS: "success",
		IN_PROCESS: "in_process",
		PRIVATE_METHOD: "private_method",
		UNKNOWN_METHOD: "unknown_method"
	}
};

/**
 * defalt request destination
 * @var string
 */
Fabrico.controller.DESTINATION = location.href;

/**
 * destination setter
 * @param string url
 * @return Fabrico controller object
 */
Fabrico.controller.receiver = function (src) {
	this.DESTINATION = src || location.href;
	return this;
};

/**
 * @name method
 * @param string method name
 * @param array optional arguments
 * @param object optional controller properties
 * @param function success handler
 * @param function error handler
 * @return Promise
 * @see request
 */
Fabrico.controller.method = function (method, args, env, callback, errback) {
	return this.request({
		_method: method
	}, args, env, callback, errback);
};

/**
 * @name request
 * @param object default request variables
 * @param array optional arguments
 * @param object optional controller/global properties
 * @param function success handler
 * @param function error handler
 * @return Promise
 * @see redirect
 */
Fabrico.controller.request = function (req, args, env, callback, errback) {
	var dest = this.DESTINATION;
	req._args = args || [];
	req._env = env || {};

	// reset
	this.receiver();

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
