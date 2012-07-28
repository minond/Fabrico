<?php

$id = $name . '_' . rand();
$force = isset($force) && $force === true ? 'true' : 'false';
FabricoPageResource::onready("Fabrico.ui.autocomplete('#$id', '$method', $force)");

?>

<?= cssfile(corefile('search.css')) ?>

<div class="autocompleteholder">
	<input type="hidden" name="<?= $name ?>" id="<?= $id ?>_id" />
	<input type="text" name="<?= $name ?>_text" id="<?= $id ?>" class="autocompleteinput" />
	<div id="<?= $id ?>_results" class="autocompleteresults"></div>
	<div class="autocompleterunsearch"></div>
</div>
