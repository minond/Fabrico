/**
 * @name ui
 * @var object
 */
Fabrico.ui = {
	classes: {
		INVALID: "invalid",
		VALID: "valid",
		ACTIVE: "active"
	},

	/**
	 * popup center on window resize listener
	 */
	popup_resize_center: function () {
		$(window).resize(function () {
			$(".popup_modal_border", ".popup_modal:visible").each(function () {
				var $this = $(this);

				$this.css({
					top: (innerHeight - $this.height()) / 2 - ($this.height() / 2),
					left: (innerWidth - $this.width()) / 2
				});
			});
		});

		this.popup_resize_center = function () {};
	},

	/**
	 * prevent submit action with submit event lister
	 * that triggers the submit button's click action
	 */
	no_submit_ajax_form: function () {
		return $("form.ajax").live("submit", function (e) {
			$sub = $("input.submit", this);

			if ($sub.length) {
				e.preventDefault();
				$sub.trigger("click");
				return false;
			}
		});
	},

	/**
	 * method and action links always have an href of hash
	 * disable them, as js runs when they're clicked.
	 *
	 * @name no_action_links
	 * @return array of action links
	 */
	no_action_links: function () {
		return $("a[href='#']").live("click", function (e) {
			e.preventDefault();
		});
	},

	/**
	 * @name handle_submit_form_error
	 * @param string form id
	 */
	handle_submit_form_error: function (formid) {
		// ...
	},

	/**
	 * handles actions taken right before submiting a form
	 *
	 * @param string form id
	 */
	form_pre_submit: function (formid) {
		$("*:input", formid).attr("readonly", true);
		$("input[type='submit']", formid).attr("disabled", true);
	},

	/**
	 * handles actions taken right after submiting a form
	 *
	 * @param string form id
	 */
	form_post_submit: function (formid) {
		$("*:input", formid).attr("readonly", false);
		$("input[type='submit']", formid).attr("disabled", false);
	},

	/**
	 * set a standard class on a form element
	 *
	 * @param string form id
	 * @param string class name
	 */
	form_set_class: function (formid, classname) {
		var form = $(formid);

		for (var klass in this.classes) {
			form.removeClass(this.classes[ klass ]);
		}

		form.addClass(classname || "");
	},

	/**
	 * submits a form to a controller method and handles the response 
	 *
	 * @name submit_form
	 * @param string form id to place a submit event listener on
	 * @param string method name to send form data to
	 */
	listen_submit_form: function (formid, methodname) {
		$(formid).submit(function (e) {
			e.preventDefault();
			Fabrico.ui.form_pre_submit(formid);
			Fabrico.ui.form_message(formid, "Sending request...");
			Fabrico.ui.form_set_class(formid, Fabrico.ui.classes.ACTIVE);

			Fabrico.controller.method(methodname, [Fabrico.helper.form2args(formid)], {}, function (data) {
				var error = false;
				Fabrico.helper.form_reset_display(formid);
				Fabrico.ui.form_post_submit(formid);

				try {
					data = JSON.parse(data);
				}
				catch (t_error) {
					data = {};
					error = true;
				}

				if ($.isNumeric(data.response)) {
					// object id, valid request
					Fabrico.ui.form_set_class(formid, Fabrico.ui.classes.VALID);
					Fabrico.ui.form_message(formid, "Success!", true);
				}
				else if ($.isPlainObject(data.response)) {
					//  array of errors, invalid request
					Fabrico.helper.form_error_display(formid, data.response);
					Fabrico.ui.form_set_class(formid, Fabrico.ui.classes.INVALID);
					Fabrico.ui.form_message(formid, "Missing fields.");
				}
				else {
					Fabrico.ui.handle_submit_form_error(formid);
					Fabrico.ui.form_set_class(formid, Fabrico.ui.classes.INVALID);
					Fabrico.ui.form_message(formid, "There was an error while saving.");
				}
			});
		});
	},

	/**
	 * get all active elements
	 *
	 * @param node element holder, defaults to document.body
	 * @return jQuery array of elements
	 */
	active_children: function (father) {
		return $(father || document.body).children("." + Fabrico.ui.classes.ACTIVE);
	},

	/**
	 * get the first active element
	 *
	 * @param node element holder, defaults to document.body
	 * @return jQuery element
	 * @see Fabrico.ui.active_children
	 */
	active_child: function (father) {
		return $(this.active_children(father).get(0));
	},

	/**
	 * handles message displays for form elements
	 *
	 * @param string form id
	 * @param string message
	 * @param bool clearable message
	 */
	form_message: function (formid, msg, clearable) {
		$(".formmessage .formmessagetext", formid).html(msg || "");
		this.form_message_clear_able[ formid ] = clearable || false;
		this.form_message_queue_clear(formid);
	},

	/**
	 * used to track if messages have been set in a form
	 *
	 * @var object
	 */
	form_message_clear_able: {},

	/**
	 * clears the form element's message section if no other messages
	 * have been set.
	 *
	 * @param string form id
	 * @param int delay
	 */
	form_message_queue_clear: function (formid, delay) {
		if (Fabrico.ui.form_message_clear_able[ formid ]) {
			setTimeout(function () {
				if (Fabrico.ui.form_message_clear_able[ formid ]) {
					Fabrico.ui.form_message(formid);
					Fabrico.ui.form_set_class(formid);
				}
			}, delay || 10000);
		}
	},

	/**
	 * handles auto complete searches
	 *
	 * @name autocomplete
	 * @param string input field id
	 * @param string method name
	 * @param boolean force request, no caching
	 */
	autocomplete: function (fieldid, methodname, force) {
		var lastsearch;
		var elem = $(fieldid);
		var results = elem.next();
		var idvalue = $(elem.siblings()[ 0 ]);

		var tpl = {
			hash: "result_$0_$1",
			main: "<div class='resultitemmain'>$0</div>",
			text: "<div class='resultitemtext' data-id='$1'>$0</div>",
			desc: "<div class='resultitemdesc'>$0</div>"
		};

		var elems = {
			id: "id",
			results: ".autocompleteresults",
			main: ".resultitemmain",
			text: ".resultitemtext",
			desc: ".resultitemdesc"
		};

		var displayresults = function (ret) {
			var resultstr, allresults = "";

			for (var i = 0, max = ret.length; i < max; i++) {
				result = ret[ i ];

				if (result.text) {
					resultstr = Fabrico.helper.sprintf(tpl.text, result.text, result.id);

					if (result.desc) {
						resultstr += Fabrico.helper.sprintf(tpl.desc, result.desc);
					}

					allresults += Fabrico.helper.sprintf(tpl.main, resultstr);
				}
			}

			results.html(allresults);

			if (allresults) {
				results.show();
			}
		};

		elem.keyup(function (e) {
			var search = this.value;
			var hash = Fabrico.helper.sprintf(tpl.hash, methodname, search);
			var selection = Fabrico.ui.active_child(results);
			var index = selection.index();

			// arrows of enter checks
			switch (e.keyCode) {
				// tab, space, left and right
				case 9:
				case 32:
				case 37:
				case 39:
					return true;

				// escape
				case 27:
					results.hide();
					break;

				// up
				case 38:
					var allitems = $(results).children();
					index = !index || index === -1 ? allitems.length - 1 : index - 1;
					var newselection = $(allitems.get(index)).addClass(Fabrico.ui.classes.ACTIVE);
					selection.removeClass(Fabrico.ui.classes.ACTIVE);

					if (newselection.length) {
						newselection[ 0 ].scrollIntoViewIfNeeded();
					}

					e.preventDefault();
					return true;

				// down
				case 40:
					var allitems = $(results).children();
					index = index + 1 === allitems.length ? 0 : index + 1;
					var newselection = $(allitems.get(index)).addClass(Fabrico.ui.classes.ACTIVE);
					selection.removeClass(Fabrico.ui.classes.ACTIVE);

					if (newselection.length) {
						newselection[ 0 ].scrollIntoViewIfNeeded();
					}

					e.preventDefault();
					return true;

				// enter
				case 13:
					if (selection.length) {
						var selected = selection.children(elems.text);
						elem.val(selected.text());
						idvalue.val(selected.data(elems.id))
						results.hide();
					}

					e.preventDefault();
					return true;
			}

			if (!search) {
				results.hide();
				return true;
			}
			else if (search === lastsearch) {
				return true;
			}

			lastsearch = search;

			if (hash in Fabrico.ui.autocompletecache && !force) {
				displayresults(Fabrico.ui.autocompletecache[ hash ]);
			}
			else {
				Fabrico.controller.method(methodname, [ search ], {}, function (strdata) {
					var response = JSON.parse(strdata).response;
	
					if (!force) {
						Fabrico.ui.autocompletecache[ hash ] = response;
					}
	
					displayresults(response);
				});
			}
		});

		if (!Fabrico.ui.autocompleteready) {
			Fabrico.ui.autocompleteready = true;

			$(elems.main).live("mouseover", function () {
				var active = Fabrico.ui.classes.ACTIVE;
				$("." + active, this.parentNode).removeClass(active);
				$(this).addClass(active);
			});
		
			$(elems.main).live("mouseout", function () {
				$(this).removeClass(Fabrico.ui.classes.ACTIVE);
			});
			
			$(elems.main).live("click", function () {
				var selected = $(elems.text, this);
				elem.val(selected.text());
				idvalue.val(selected.data(elems.id));
				results.hide();
			});

			$(document.body).keydown(function (e) {
				switch (e.keyCode) {
					case 27:
						$(elems.results).hide();
						break;
				}
			});

			$(document.body).click(function () {
				$(elems.results).hide();
			});
		}
	},

	/**
	 * auto complete search results cache
	 *
	 * @var object
	 */
	autocompletecache: {},

	/**
	 * have the event listeners been places
	 *
	 * @var boolean
	 */
	autocompleteready: false
};
