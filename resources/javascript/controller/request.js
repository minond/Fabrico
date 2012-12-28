"use strict";

fabrico.controller = {
	/**
	 * @see src/status/statusmanager.php
	 */
	status: {},

	/**
	 * should be overwritten
	 * @param jqXHR jqXHR
	 * @param string method
	 * @param array args
	 * @param function success
	 */
	on_error: function(jqXHR, method, args, success) {},

	/**
	 * asynchronous controller method request
	 * @param string method
	 * @param array args
	 * @param function success
	 * @return jqXHR
	 */
	request: function (method, args, success) {
		return $.ajax({
			success: success || new Function,
			data: {
				_controller: fabrico.page.controller,
				_method: method,
				_args: args || []
			}
		});
	}
};

/**
 * synchronous controller method request
 * @param string method
 * @param array args
 * @return object
 */
fabrico.controller.request.now = function(method, args) {
	return JSON.parse($.ajax({
		async: false,
		data: {
			_controller: fabrico.page.controller,
			_method: method,
			_args: args || []
		}
	}).responseText || "{}");
};
