<?= Template::content() ?>
<?= Resource::add('main.js') ?>

<div>my name is <?= $name ?>, my id is <?= $id ?></div>

<? foreach ($posts as $post): ?>
	<div><?= $post ?></div>
<? endforeach ?>
