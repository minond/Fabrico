<? element('form_action', 'link') ?>
<?= content() ?>

<div>Add a post:</div>

<?= form_action::open() ?>
<table>
	<tr>
		<td>Subject:</td>
		<td>
			<input name="subject" />
		</td>
	</tr>
	<tr>
		<td>Body:</td>
		<td>
			<textarea name="body"></textarea>
		</td>
	</tr>
</table>
<?= form_action::close('add_post', '/posts', '/posts', __HTML__) ?>
<br />
<br />
<br />


<? foreach (Post::search() as $post): ?>

	<table>
		<tr>
			<td>id:</td>
			<td>
				<?= $post->id ?>
				<?= link_method::gen("(del:{$post->id})", 'delete_post', '/posts', array($post->id)) ?>
			</td>
		</tr>
		<tr>
			<td>subject:</td>
			<td><?= $post->subject ?></td>
		</tr>
		<tr>
			<td>body:</td>
			<td><?= $post->body ?></td>
		</tr>
	</table>

	<? foreach (Post::children($post->id) as $comment): ?>
		<div style="padding-left: 20px"><?= $comment->comment ?></div>
	<? endforeach ?>

	<br />

<? endforeach ?>
