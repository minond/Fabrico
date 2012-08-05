<?

// load field layouts
element('form/field');

// field def defaults
$defs = (object) array(
	'id' => rand(),
	'hidden' => 'hidden',
	'type' => 'text',
	'value' => '',
	'submitlabel' => 'Save'
);

// field properties
$prop = (object) array(
	'id' => 'id',
	'type' => 'type',
	'label' => 'label'
);

// list of fields and information about them
// such as their type, label, and value
$fieldlist = array();

// object values
$values = $data->getdata();

// model field information
$type = $data->gettype();
$schema = FabricoModel::getinfo($type);
$fielddata = FabricoModel::getfielddata($type);
$primarykey = $schema->primary_key;
$actions = array_merge($fielddata->actions, $actions);
$object_id = 0;

// field order information
// priority follows: parameter, model, table schema
if (!count($order)) {
	if (count($fielddata->order)) {
		$order = $fielddata->order;
	}
	else {
		$order = array_keys($schema->column_names);
	}
}

$genname = function ($name) {
	return ucwords(preg_replace('/_/', ' ', $name));
};

// go through each field
foreach ($order as $field) {
	$info = new stdClass;
	$fieldinfo = $schema->column_names[ $field ];

	// check for model definition
	$userdef = array_key_exists($field, $fielddata->editing) ?
	           $fielddata->editing[ $field ] : array();

	$info->name = $field;
	$info->value = property_exists($values, $field) ? $values->{ $field } : $defs->value;

	if ($field === $primarykey) {
		$info->type = $defs->hidden;
		$object_id = $info->value;
	}
	else {
		$info->type = isset($userdef[ $prop->type ]) ? $userdef[ $prop->type ] : $defs->type;
	}

	$info->label = isset($userdef[ $prop->label ]) ? $userdef[ $prop->label ] : $genname($field);
	$info->id = isset($userdef[ $prop->id ]) ? $userdef[ $prop->id ] : $info->name . $defs->id;
	$info->hidden = $info->type === $defs->hidden;
	
	// save this field
	$fieldlist[] = $info;
}

// submit action label
if (isset($actions['submit'])) {
	$defs->submitlabel = $actions['submit'];
	unset($actions['submit']);
}

?>

<? if ($center): ?>
<center>
<? endif ?>
<? form\method::open() ?>
	<? foreach ($fieldlist as $index => $field): ?>
		<? if (!$field->hidden): ?>
			<label for="<?=$field->id?>"><?=$field->label?></label>
		<? endif ?>
		<? call_user_func(array('form\field', $field->type), array(
			'id' => $field->id,
			'checked' => $field->value,
			'value' => $field->value,
			'name' => $field->name
		)) ?>
	<? endforeach ?>

	<center>
		<div class="formmessage">
			<center>
				<table>
					<tr>
						<td>
							<div class="formmessageimg"></div>
						</td>
						<td>
							<div class="formmessagetext">&nbsp;</div>
						</td>
					</tr>
				</table>
			</center>
		</div>
		<?= form\submitbutton::gen($defs->submitlabel) ?>
		<? foreach ($actions as $action): ?>
			<?= space(2) ?>
			<?= form\methodbutton::gen($action[0], $action[1], array($object_id)) ?>
		<? endforeach ?>
	</center>
<?= form\method::close($method, __HTML__) ?>
<? if ($center): ?>
</center>
<? endif ?>
