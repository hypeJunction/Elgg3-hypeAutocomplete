<?php
/**
 * Displays an autocomplete text input.
 *
 * @uses $vars['handler']      Data source URL, defaults to /livesearch handler
 * @uses $vars['multiple']     Allow multiple values
 * @uses $vars['options']      Options to be passed to the handler with the URL query
 *                             If using custom options, make sure to impose a signed request gatekeeper in the resource view
 * @uses $vars['value']        Current value for the text input
 * @uses $vars['match_on']     users|groups|friends
 * @uses $vars['match_owner']  Restrict search results to owned entities
 *                             Applies to group search. Owner will default to logged in user, unless match_target is set
 * @uses $vars['match_membership'] Restrict search results to entities that user is a member of
 *                                 Applies to group search. Unless match_target is set, will default to currently logge din user
 * @uses $vars['match_target'] Restrict search results to a specific target
 *                             Applies to friends search
 *                             Applies to group search with match_owner
 *                             Applies to group search with match_membership
 *                             Note that current user must be able to edit the target user for the results to show
 * @uses $vars['class']        Additional CSS class
 */

$vars['class'] = elgg_extract_class($vars, 'elgg-input-autocomplete');

$defaults = [
	'value' => '',
	'disabled' => false,
];

$vars = array_merge($defaults, $vars);

$params = elgg_extract('options', $vars, []);

if (!empty($params)) {
	ksort($params);

	// We sign custom parameters, so that plugins can validate
	// that the request is unaltered, if needed
	$mac = elgg_build_hmac($params);
	$params['mac'] = $mac->getToken();
}

$match_on = elgg_extract('match_on', $vars);
if (empty($match_on) || is_array($match_on)) {
	elgg_log('"input/autocomplete" must specify a single "match_on" parameter');
	return;
}

if ($match_on == 'all') {
	elgg_log('"input/autocomplete" no longer supports matching on "all" entities');
	return;
}

if ($match_on == 'friends') {
	$match_on = 'users';
	$params['friends_only'] = true;
}

$params['match_on'] = $match_on;
unset($vars['match_on']);

if (isset($vars['match_owner'])) {
	$params['match_owner'] = elgg_extract('match_owner', $vars);
	unset($vars['match_owner']);
}

if (isset($vars['match_membership'])) {
	$params['match_membership'] = elgg_extract('match_membership', $vars);
	unset($vars['match_membership']);
}

if (isset($vars['match_target'])) {
	$target = elgg_extract('match_target', $vars);
	if ($target instanceof ElggEntity) {
		$target_guid = $target->guid;
	} else {
		$target_guid = (int) $target;
	}
	$params['match_membership'] = $target;
	unset($vars['match_membership']);
}

$params['view'] = 'json'; // force json viewtype
$handler = elgg_extract('handler', $vars, "livesearch/$match_on");
$source = elgg_normalize_url(elgg_http_add_url_query_elements($handler, $params));

$vars['type'] = 'text';
$vars['data-source'] = $source;
$vars['data-prop'] = $match_on == 'users' ? 'username' : 'guid';

$values = (array) elgg_extract('value', $vars, []);
$vars['value'] = [];
$vars['options_values'] = [];

foreach ($values as $attr) {
	if (is_numeric($attr)) {
		$entity = get_entity($attr);
	} else {
		$entity = get_user_by_username($attr);
	}

	if ($entity) {
		$vars['value'][] = $entity instanceof ElggUser ? $entity->username : $entity->guid;
		$vars['options_values'][] = [
			'value' => $entity instanceof ElggUser ? $entity->username : $entity->guid,
			'text' => $entity->getDisplayName(),
			'data-icon-url' => $entity->getIconURL(['size' => 'small']),
			'selected' => true,
		];
	}
}

$vars['config'] = (array) elgg_extract('config', $vars, []);
$vars['config']['containerCssClass'] = 'elgg-autocomplete-entities';
$vars['config']['minimumInputLength'] = 2;
$vars['config']['width'] = '100%';

echo elgg_view('input/select', $vars);
