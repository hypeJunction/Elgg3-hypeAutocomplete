<?php

/**
 * GUID/Entity input
 *
 * @uses $vars['items'] Optional list of default items
 * @uses $vars['source'] Optional URL to fetch search results from
 * @uses $vars['options'] Options to send with the request
 * @uses $vars['value'] Value(s)
 * @uses $vars['multiple'] Allow multiple values
 * @uses $vars['limit'] Max number of results
 */
$items = elgg_extract('items', $vars);
unset($vars['items']);

$value = elgg_extract('value', $vars);
$guids = \Elgg\Values::normalizeGuids($value);
$vars['value'] = $guids;

if (!isset($items)) {
	$source = elgg_extract('source', $vars);
	if (!$source && $source !== false) {
		$source = 'autocomplete/guids';
	}

	$options = elgg_extract('options', $vars, []);
	$options['view'] = 'json';

	$source = elgg_http_add_url_query_elements($source, $options);
	$source = elgg_http_get_signed_url($source);
	$vars['data-source'] = $source;

	$vars['data-prop'] = 'guid';
}

$vars['config'] = (array) elgg_extract('config', $vars, []);
$vars['config']['containerCssClass'] = 'elgg-autocomplete-guids';
$vars['config']['minimumInputLength'] = 2;
$vars['config']['width'] = '100%';

$multiple = elgg_extract('multiple', $vars, true);
$limit = (int) elgg_extract('limit', $vars, 0);
if ($limit == 1) {
	$vars['multiple'] = false;
	$vars['config']['maximumSelectionLength'] = 1;
} else {
	$vars['multiple'] = true;
	unset($vars['config']['maximumSelectionLength']);
}

$vars['class'] = elgg_extract_class($vars, 'elgg-input-guids');

$vars['options_values'] = [];

foreach ($guids as $guid) {
	$entity = get_entity($guid);
	if ($entity) {
		$items[] = $entity;
	}
}

$items = \Elgg\Values::normalizeGuids($items);

foreach ($items as $guid) {
	$entity = get_entity($guid);
	if ($entity) {
		$vars['options_values'][] = [
			'value' => $entity->guid,
			'text' => $entity->getDisplayName(),
			'data-icon-url' => $entity->hasIcon('small') ? $entity->getIconURL(['size' => 'small']) : null,
		];
	}
}

echo elgg_view('input/select', $vars);