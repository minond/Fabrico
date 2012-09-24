Fabrico.element = {};

/**
 * @param string wrapper
 * @param object map
 * @return FabricoElement
 */
Fabrico.element.factory = function (wrapper, map) {
	var wrapper = wrapper;
	var map = map;

	// constructor
	var FabricoElement = function (attrs) {
		for (var attr in attrs) {
			this[ attr ] = attrs[ attr ];
		}
	};

	// html output
	FabricoElement.prototype.toString = function () {
		var html = "";

		for (var template in map) {
			html += sprintf(map[ template ], template in this ? this[ template ] : "");
		}

		return html;
	};

	// jquery output
	FabricoElement.prototype.toElement = function () {
		return $(wrapper).append(this.toString());
	};

	return FabricoElement;
};
