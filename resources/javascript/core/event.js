fabrico.register("event", function(f) {
	"use strict";

	var trigger, registered = {};

	/**
	 * @param string ename
	 * @param function action
	 */
	this.on = function(ename, action) {
		if (ename in registered) {
			registered[ ename ].push(action);
		}
		else {
			registered[ ename ] = [ action ];
			document.addEventListener(ename, function(ev) {
				trigger(ename, [ev], this);
			});
		}
	};

	/**
	 * @param string ename
	 * @param array args
	 * @param mixed scope
	 */
	this.trigger = trigger = function(ename, args, scope) {
		var actions = [],
			args = args || [],
			scope = scope || f;

		if (ename in registered) {
			actions = registered[ ename ];

			for (var i = 0, l = actions.length; i < l; i++) {
				actions[ i ].apply(scope, args);
			}
		}
	};
});
