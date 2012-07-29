<select multiple="true" name="<?=$name?>" id="<?=$id?>" class="basicmultiselectfield <?=$class?>">
<? foreach ($values as $option): ?>
	<? $value = isset($option['value']) ? $option['value'] : '' ?>
	<? $label = isset($option['label']) ? $option['label'] : $value ?>
	<? $selected = isset($option['selected']) ? $option['selected'] : false ?>
	<option value="<?=$value?>" <?= $selected ? 'selected' : '' ?>><?=$label?></option>
<? endforeach ?>
</select>
