Fabrico.controller = {};

/**
 * @name method
 * @param string method name
 * @param array optional arguments
 * @param object optional controller properties
 * @param function success handler
 * @param function error handler
 * @see request
 */
Fabrico.controller.method = function (method, args, env, callback, errback) {
	return this.request({
		_method: method
	}, args, env, callback, errback);
};

/**
 * @name action
 * @param string action name
 * @param array optional arguments
 * @param object optional global variabled
 * @param function success handler
 * @param function error handler
 * @see request
 */
Fabrico.controller.action = function (action, args, env, callback, errback) {
	return this.request({
		_action: action
	}, args, env, callback, errback);
};

/**
 * @name request
 * @param object default request variabled
 * @param array optional arguments
 * @param object optional controller properties
 * @param function success handler
 * @param function error handler
 * @see action
 * @see controller
 */
Fabrico.controller.request = function (req, args, env, callback, errback) {
	req._arg = args || [];
	req._env = env || {};

	return $.ajax({
		type: 'POST', 
		async: true, 
		url: location.href,
		success: callback || function () {},
		fail: errback || function () {},
		data: req
	});
}
