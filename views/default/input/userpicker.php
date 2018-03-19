<?php
/**
 * User Picker.  Sends an array of user guids.
 **
 * @uses $vars['values'] Array of user guids for already selected users or null
 * @uses $vars['limit'] Limit number of users (default 0 = no limit)
 * @uses $vars['name'] Name of the returned data array (default "members")
 * @uses $vars['handler'] Name of page handler used to power search (default "livesearch")
 * @uses $vars['options'] Additional options to pass to the handler with the URL query
 *                        If using custom options, make sure to impose a signed request gatekeeper in the resource view
 * @uses $vars['only_friends'] If enabled, will turn the input into a friends picker
 *
 * Defaults to lazy load user lists in alphabetical order. User needs
 * to type two characters before seeing the user popup list.
 *
 * As users are selected they move down to a "users" box.
 * When this happens, a hidden input is created to return the GUID in the array with the form
 */
if (empty($vars['name'])) {
	$vars['name'] = 'members';
}

$guids = (array) elgg_extract('values', $vars, elgg_extract('value', $vars, []));
unset($vars['values']);
$guids = \Elgg\Values::normalizeGuids($guids);

$params = elgg_extract('options', $vars, []);
unset($vars['options']);

$friends_only = elgg_extract('only_friends', $vars);
unset($vars['only_friends']);

if ($friends_only) {
	$params['friends_only'] = true;
}

if (!empty($params)) {
	ksort($params);

	// We sign custom parameters, so that plugins can validate
	// that the request is unaltered, if needed
	$mac = elgg_build_hmac($params);
	$params['mac'] = $mac->getToken();
}

$handler = elgg_extract('handler', $vars, "livesearch/users");
$params['view'] = 'json'; // force json viewtype

$vars['data-source'] = elgg_http_add_url_query_elements($handler, $params);
$vars['data-prop'] = 'guid';

$vars['config'] = (array) elgg_extract('config', $vars, []);
$vars['config']['containerCssClass'] = 'elgg-autocomplete-users';
$vars['config']['minimumInputLength'] = 2;
$vars['config']['width'] = '100%';

$vars['multiple'] = true;

$limit = (int) elgg_extract('limit', $vars, 0);
$vars['config']['maximumSelectionLength'] = $limit;

$vars['class'] = elgg_extract_class($vars, 'elgg-user-picker');

$vars['value'] = [];
$vars['options_values'] = [];

foreach ($guids as $guid) {
	$entity = get_entity($guid);
	if ($entity) {
		$vars['value'][] = $entity->guid;
		$vars['options_values'][] = [
			'value' => $entity->guid,
			'text' => $entity->getDisplayName(),
			'data-icon-url' => $entity->getIconURL(['size' => 'small']),
		];
	}
}

echo elgg_view('input/select', $vars);