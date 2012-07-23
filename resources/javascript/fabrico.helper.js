/**
 * @name helper
 * @var object
 */
Fabrico.helper = {};

/**
 * @name form2args
 * @param string form id
 * @return object of form fields
 */
Fabrico.helper.form2args = function (formid) {
	var fields = {};

	$.map($(formid).serializeArray(), function (data) {
		fields[ data.name ] = data.value;
	});

	return fields;
}
