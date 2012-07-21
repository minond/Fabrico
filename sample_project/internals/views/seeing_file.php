<?= FabricoPage::start() ?>
<?= FabricoPageResource::add(
	FabricoPageResource::internal('normalize.css'),
	FabricoPageResource::internal('jquery.min.js'),
	FabricoPageResource::internal('jquery-ui.min.js'),
	FabricoPageResource::internal('underscore-min.js'),
	FabricoPageResource::internal('fabrico.js'),
	FabricoPageResource::internal('fabrico.controller.js'),
	FabricoPageResource::internal('fabrico.ui.js'),
	'main.css',
	'main.js'
); ?>

<link rel="shortcut icon" href="<?= imgsrc('favicon.ico') ?>" />
