<?= Template::content() ?>
<?= Resource::add('main.js') ?>

<div>my name is <?= $name ?></div>

<? foreach ($posts as $post): ?>
	<div><?= $post ?></div>
<? endforeach ?>
