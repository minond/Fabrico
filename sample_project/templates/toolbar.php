<? action('toolbar') ?>

<!-- toolbar start -->
<div class="toolbar noselect">
	<? if ($logged_in && $user_set): ?>
	<?= toolbar_item("+{$user->first_name}") ?>
	<? endif ?>
	<?= toolbar_item('Search') ?>
	<?= toolbar_item('Maps') ?>
	<?= toolbar_item('Play') ?>
	<?= toolbar_item('YouTube') ?>
	<?= toolbar_item('News') ?>
	<?= toolbar_item('Gmail') ?>
	<?= toolbar_item('Drive') ?>
	<?= toolbar_item('Calendar') ?>
	<?= toolbar_item('More +') ?>
</div>
<!-- toolbar end -->
