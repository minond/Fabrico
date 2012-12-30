fabrico.register("controller", function(f) {
	"use strict";

	var gdata, pretty_up, me = this;

	/**
	 * @param string method
	 * @param array args
	 * @return object
	 */
	gdata = function(method, args) {
		return {
			_controller: f.information.controller,
			_method: method,
			_args: args || []
		};
	};

	/**
	 * @param jqXHR jqXHR
	 * @param string method
	 * @param array args
	 * @param function done
	 * @param function fail
	 * @return jqXHR
	 */
	pretty_up = function(ajax, method, args, done, fail) {
		return ajax
			.done(function(text) {
				var json;

				try {
					json = JSON.parse(text);
				} catch (ignore) {
					json = null;
				}

				(done || me.on_done)(ajax, text, json, method, args);
			})
			.fail(function(me, status) {
				(fail || me.on_fail)(me, status, method, args);
			});
	};

	/**
	 * @see src/status/statusmanager.php
	 */
	this.status = {};

	/**
	 * should be overwritten
	 * @param jqXHR jqXHR
	 * @param string text_status
	 * @param string method
	 * @param array args
	 */
	this.on_fail = function(jqXHR, text_status, method, args) {};

	/**
	 * should be overwritten
	 * @param jqXHR jqXHR
	 * @param string text_response
	 * @param string method
	 * @param array args
	 */
	this.on_done = function(jqXHR, text_response, json_response, method, args) {};

	/**
	 * asynchronous controller method request
	 * @param string method
	 * @param array args
	 * @param function done
	 * @param function fail
	 * @return jqXHR
	 */
	this.request = function (method, args, done, fail) {
		return pretty_up($.ajax({
			data: gdata(method, args)
		}), method, args, done, fail);
	};

	/**
	 * synchronous controller method request
	 * @param string method
	 * @param array args
	 * @param function done
	 * @param function fail
	 * @return object
	 */
	this.request.now = function(method, args, done, fail) {
		return JSON.parse(pretty_up($.ajax({
			async: false,
			data: gdata(method, args)
		}), method, args, done, fail).responseText || "{}");
	};
});
