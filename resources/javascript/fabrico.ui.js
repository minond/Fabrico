/**
 * @name ui
 * @var object
 */
Fabrico.ui = {
	classes: {
		INVALID: "invalid",
		ACTIVE: "active"
	}
};

/**
 * method and action links always have an href of hash
 * disable them, as js runs when they're clicked.
 *
 * @name no_action_links
 * @return array of action links
 */
Fabrico.ui.no_action_links = function () {
	return $("a[href='#']").click(function (e) {
		e.preventDefault();
	});
};

/**
 * @name handle_submit_form_error
 * @param string form id
 */
Fabrico.ui.handle_submit_form_error = function (formid) {
	// ...
};

/**
 * submits a form to a controller method and handles the response 
 *
 * @name submit_form
 * @param string form id to place a submit event listener on
 * @param string method name to send form data to
 */
Fabrico.ui.listen_submit_form = function (formid, methodname) {
	$(formid).submit(function (e) {
		e.preventDefault();

		Fabrico.controller.method(methodname, [Fabrico.helper.form2args(formid)], {}, function (data) {
			var error = false;
			Fabrico.helper.form_reset_display(formid);

			try {
				data = JSON.parse(data);
			}
			catch (t_error) {
				data = {};
				error = true;
			}

			if ($.isNumeric(data.response)) {
				// object id, valid request
				alert("Success");
			}
			else if ($.isArray(data.response)) {
				//  array of errors, invalid request
				Fabrico.helper.form_error_display(formid, data.response);
			}
			else {
				Fabrico.ui.handle_submit_form_error(formid);
			}
		});
	});
};
