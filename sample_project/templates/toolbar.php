<? element('toolbar') ?>
<? element('link') ?>

<!-- toolbar start -->
<div class="toolbar noselect">
	<? if ($logged_in && $user_set): ?>
		<?= toolbar_item::gen("+{$user->first_name}") ?>
	<? endif ?>
	<?= toolbar_item::gen('Search') ?>
	<?= toolbar_item::gen('Images') ?>
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

<? if (Fabrico::$file !== 'login'): ?>
	<? if ($logged_in): ?>
		<?= link_method::gen('Logout', 'logout', 'search') ?>
	<? else: ?>
		<?= link_to::gen('Log in', 'login') ?>
	<? endif ?>
<? endif ?>
