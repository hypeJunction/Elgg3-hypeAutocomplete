<?php
/**
 * Elgg tag input
 * Displays a tag input field
 *
 * @uses $vars['disabled']
 * @uses $vars['class']    Additional CSS class
 * @uses $vars['value']    Array of tags or a string
 * @uses $vars['entity']   Optional. Entity whose tags are being displayed (metadata ->tags)
 */

$vars['class'] = elgg_extract_class($vars, 'elgg-input-tags');

$defaults = [
	'value' => '',
	'disabled' => false,
	'autocapitalize' => 'off',
	'type' => 'text'
];

if (isset($vars['entity'])) {
	$defaults['value'] = elgg_extract('entity', $vars)->tags;
	unset($vars['entity']);
}

$vars = array_merge($defaults, $vars);

if (is_array($vars['value'])) {
	$tags = [];

	foreach ($vars['value'] as $tag) {
		if (is_string($tag)) {
			$tags[] = $tag;
		} else {
			$tags[] = $tag->value;
		}
	}

	$vars['value'] = implode(", ", $tags);
}

$tags = elgg_get_tags([
	'limit' => 50,
	'owner_guid' => elgg_get_logged_in_user_guid(),
]);

$value = elgg_extract('value', $vars, '');
$vars['value'] = string_to_tag_array($value);
$vars['options'] = $vars['value'];

if (!isset($vars['placeholder'])) {
	$vars['placeholder'] = elgg_echo('autocomplete:tags:placeholder');
}

$vars['multiple'] = true;

$source = elgg_extract('source', $vars, elgg_normalize_url('autocomplete/tags'));
unset($vars['source']);

$vars['data-source'] = $source;

$vars['config'] = (array) elgg_extract('config', $vars, []);
$vars['config']['tags'] = true;
$vars['config']['containerCssClass'] = 'elgg-autocomplete-tags';
$vars['config']['tokenSeparators'] = [',', ';'];
$vars['config']['minimumInputLength'] = 2;
$vars['config']['width'] = '100%';

echo elgg_view('input/select', $vars);
