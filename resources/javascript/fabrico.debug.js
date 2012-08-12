/**
 * @name debug
 * @var object
 */
Fabrico.debug = {};

/**
 * @name json
 * @var object
 */
Fabrico.debug.json = {};

/**
 * @name clean
 * @var object
 */
Fabrico.debug.json.clean = {
	typecheck: /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
	replace: {
		"&amp;": /&/g,
		"&lt;": /</g,
		"&gt;": />/g
	}
};

Fabrico.debug.json.classes = function (match) {
	var cls = "number";

	if (/^"/.test(match)) {
		if (/:$/.test(match)) {
			cls = 'key';
		} else {
			cls = 'string';
		}
	} else if (/true|false/.test(match)) {
		cls = 'boolean';
	} else if (/null/.test(match)) {
		cls = 'null';
	}

	return '<span class="' + cls + '">' + match + '</span>';
}

Fabrico.debug.json.output = function (json) {
	var str = JSON.stringify(json, undefined, 4);

	for (var replace in this.clean.replace) {
		str = str.replace(this.clean.replace[ replace ], replace);
	}

	return str.replace(this.clean.typecheck, this.classes);
};
