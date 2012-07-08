//Fabrico.controller.method("test", ["fd", "aa"], { name: "minond" }, function (txt, sts, pro) {
//	console.log(JSON.parse(txt));
//});

var log = function () {
	Fabrico.controller.action("filelog", arguments);
};
