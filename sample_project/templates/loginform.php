<? action('button') ?>
<? action('checkbox') ?>
<? action('link') ?>

<form>
	<div class="loginform_holder noselect">
		<div class="loginform_header_title">Sign in</div>
		<!--
		<img class="loginform_header_img" src="https://ssl.gstatic.com/accounts/ui/google-signin-flat.png" />
		-->

		<div class="loginform_item_holder">
			<div>Email</div>
			<input name="email" autocomplete="off" />
		</div>

		<div class="loginform_item_holder">
			<div>Password</div>
			<input name="password" autocomplete="off" type="password" />
		</div>

		<div class="loginform_item_section">
			<?= button::gen('Sign In', button::ACTION) ?>
			<span class="loginform_item_offset_left">
				<?= checkbox::gen('stay', 'Stay signed in') ?>
			</span>
		</div>

		<div class="loginform_item_section">
			<?= link_to::gen('Can\'t access your accout?', '/'); ?>
		</div>
	</div>
</form>
