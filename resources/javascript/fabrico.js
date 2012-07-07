var Fabrico = {};

function sprintf () {
	for (var i = 1, max = arguments.length; i < max; i++) {
		arguments[ 0 ] = arguments[ 0 ].replace("%s", arguments[ i ]);
	}

	return arguments[ 0 ];
}
