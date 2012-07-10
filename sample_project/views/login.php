<?= Template::content() ?>

<? if ($logged_in): ?>
	<? redirect('search', true) ?>
<? endif ?>

<? include template('standard_js') ?>
<? include template('toolbar') ?>
<? include template('loginform') ?>
