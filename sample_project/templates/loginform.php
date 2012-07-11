<? element('button', 'checkbox', 'link', 'form_action', 'input_field') ?>

<?= form_action::open() ?>
	<div class="loginform_holder noselect">
		<div class="loginform_header_title">Sign in</div>
		<img class="loginform_header_img" src="https://ssl.gstatic.com/accounts/ui/google-signin-flat.png" />

		<div class="loginform_item_holder">
			<div>Email</div>
			<?= input_field::gen('email') ?>
		</div>

		<div class="loginform_item_holder">
			<div>Password</div>
			<?= password_field::gen('password') ?>
		</div>

		<? if ($login_invalid): ?>
			<br />
			<div class="error_string">The username or password you entered is<br />incorrect.</div> 
		<? endif ?>

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
<?= form_action::close('login', 'homepage', 'login', __HTML__) ?>
