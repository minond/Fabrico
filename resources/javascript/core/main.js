/**
 * fabrico modules "namespace"
 */
var fabrico = {
	/**
	 * holds page information
	 * @var object
	 */
	information: {},

	/**
	 * add new fabrico "modules"
	 * @param string name
	 * @param function runner
	 */
	register: function(name, runner) {
		runner.apply(this[ name ] = {}, [ this ]);
	}
};
