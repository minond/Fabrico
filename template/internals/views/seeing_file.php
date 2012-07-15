<?= Template::start() ?>
<?= Resource::add(
	Resource::internal('normalize.css'),
	Resource::internal('jquery.min.js'),
	Resource::internal('jquery-ui.min.js'),
	Resource::internal('underscore-min.js'),
	Resource::internal('fabrico.js'),
	Resource::internal('fabrico.controller.js'),
	Resource::internal('fabrico.ui.js'),
	'main.css',
	'main.js'
); ?>

<link rel="shortcut icon" href="<?= imgsrc('favicon.ico') ?>" />
