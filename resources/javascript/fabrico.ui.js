/**
 * @name ui
 * @var object
 */
Fabrico.ui = {};

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
