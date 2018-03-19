<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

if (get_input('format') == 'select2') {
	$prop = get_input('entity_prop', 'guid');
	echo json_encode([
		'id' => $entity->$prop,
		'text' => $entity->getDisplayName(),
		'iconUrl' => $entity->getIconURL(['size' => 'small']),
	]);
	return;
}

$type = $entity->getType();
$subtype = $entity->getSubtype();

$views = [
	"search/$type/$subtype",
	"search/$type/default",
];

foreach ($views as $view) {
	if (elgg_view_exists($view)) {
		echo elgg_view($view, $vars);
		return;
	}
}

echo json_encode($entity->toObject());
