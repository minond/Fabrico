<?= Template::content() ?>
<?= Resource::add('main.js') ?>

<? foreach ($posts as $post): ?>
	<div><?= $post ?></div>
<? endforeach ?>
