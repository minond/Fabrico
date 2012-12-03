"use strict";

fabrico.controller = {
	/**
	 * controller method request
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
