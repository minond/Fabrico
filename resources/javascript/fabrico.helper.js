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

	$("input[type='checkbox']", formid).each(function () {
		fields[ this.name ] = +this.checked;
	});

	delete fields._method;
	delete fields._success;
	delete fields._fail;

	return fields;
}

Fabrico.helper.form_reset_display = function (formid) {
	$(":input", formid).removeClass(Fabrico.ui.classes.INVALID);
};

/**
 * @name form_error_display
 * @param string form id
 * @param array of errors
 */
Fabrico.helper.form_error_display = function (formid, errors) {
	Fabrico.helper.form_reset_display(formid);
	
	$.each(errors, function (i, error) {
		$(Fabrico.helper.sprintf("[name='$0']", error), formid).addClass(
			Fabrico.ui.classes.INVALID
		)
	});
};

/**
 * @name sprintf
 * @param string template
 * @param string merge fields*
 * @return string
 */
Fabrico.helper.sprintf = function (str) {
	for (var i = 1, max = arguments.length; i < max; i++) {
		str = str.replace(new RegExp("\\$" + (i - 1), "g"), arguments[ i ]);
	}

	return str;
};
