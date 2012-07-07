/**
 * @name Fabrico
 * @var object
 */
var Fabrico = {};

/**
 * @name sprintf
 * @param string template
 * @param string* merge
 * @return string updated template string
 */
var sprintf = function (template) {
	for (var i = 1, max = arguments.length; i < max; i++) {
		template = template.replace("%s", arguments[ i ]);
	}

	return template;
}
