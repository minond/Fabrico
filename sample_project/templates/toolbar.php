<? action('toolbar') ?>

<!-- toolbar start -->
<div class="toolbar noselect">
	<? if ($logged_in && $user_set): ?>
	<?= toolbar_item::gen("+{$user->first_name}") ?>
	<? endif ?>
	<?= toolbar_item::gen('Search') ?>
	<?= toolbar_item::gen('Maps') ?>
	<?= toolbar_item::gen('Play') ?>
	<?= toolbar_item::gen('YouTube') ?>
	<?= toolbar_item::gen('News') ?>
	<?= toolbar_item::gen('Gmail') ?>
	<?= toolbar_item::gen('Drive') ?>
	<?= toolbar_item::gen('Calendar') ?>
	<?= toolbar_item::gen('More +') ?>
</div>
<!-- toolbar end -->
