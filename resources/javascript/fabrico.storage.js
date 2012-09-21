/**
 * session/local storage helper
 */
Fabrico.storage = {
	/**
	 * session storage checker
	 *
	 * @return bool
	 */
	has_session_storage: function () {
		return "sessionStorage" in window;
	},

	/**
	 * session setter
	 *
	 * @param string key
	 * @param mixed value
	 */
	set: function (key, value) {
		if (this.has_session_storage()) {
			sessionStorage.setItem(key, JSON.stringify({ val: value }));
		}
	},

	/**
	 * session getter
	 *
	 * @param string key
	 * @return mixed
	 */
	get: function (key) {
		var val;

		if (this.has_session_storage()) {
			val = sessionStorage.getItem(key);

			if (val) {
				return JSON.parse(val).val;
			}
		}
	}
};
