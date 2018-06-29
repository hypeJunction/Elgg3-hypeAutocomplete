<?php

namespace hypeJunction\Autocomplete;

use Elgg\BadRequestException;
use Elgg\Database\QueryBuilder;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;
use ElggEntity;
use hypeJunction\Ajax\Context;
use Psr\Log\LogLevel;

class SearchEntities {

	/**
	 * Autocomplete tags
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws BadRequestException
	 */
	public function __invoke(Request $request) {

		if (elgg_is_xhr()) {
			Context::restore($request);
		} else {
			elgg_signed_request_gatekeeper();
		}

		$options['limit'] = 100;

		$type = $request->getParam('type');
		$subtype = $request->getParam('subtype');

		if ($type) {
			$options['types'] = $type;
			if (!$subtype) {
				$subtype = get_registered_entity_types($type);
			}
			$options['subtypes'] = $subtype;
		} else {
			$options['type_subtype_pairs'] = [
				'user' => null,
				'group' => null,
				'object' => get_registered_entity_types('object'),
			];
		}

		$options['owner_guids'] = $request->getParam('owner_guids');
		$options['container_guids'] = $request->getParam('container_guids');
		$options['guids'] = $request->getParam('guids');

		$query = $request->getParam('q');
		if ($query) {
			$options['metadata_name_value_pairs'][] = [
				'name' => ['title', 'name'],
				'value' => "%{$query}%",
				'operand' => 'like',
				'case_sensitive' => false,
			];
		}

		$metadata = $request->getParam('metadata');
		if (is_array($metadata)) {
			foreach ($metadata as $name => $value) {
				$options['metadata_name_value_pairs'][] = [
					'name' => $name,
					'value' => $value,
				];
			}
		}

		$value = $request->getParam('value', []);
		$exclude = $request->getParam('exclude', []);

		if (is_array($value) && !empty($value)) {
			$exclude = array_merge($value, $exclude);
		}

		if (!empty($exclude)) {
			$options['wheres'][] = function(QueryBuilder $qb) use ($exclude) {
				return $qb->compare('e.guid', 'NOT IN', $exclude, ELGG_VALUE_INTEGER);
			};
		}

		$exclude_subtypes = $request->getParam('exclude_subtypes', []);
		if (!empty($exclude_subtypes)) {
			$options['wheres'][] = function(QueryBuilder $qb) use ($exclude_subtypes) {
				return $qb->compare('e.subtype', 'NOT IN', $exclude_subtypes, ELGG_VALUE_STRING);
			};
		}

		$entities = elgg_get_entities($options);

		if (empty($entities)) {
			return elgg_ok_response(json_encode([]));
		}

		$data = array_map(function(ElggEntity $e) {
			return [
				'id' => $e->guid,
				'text' => $e->getDisplayName(),
				'iconUrl' => $e->getIconURL(['size' => 'small']),
			];
		}, $entities);

		return elgg_ok_response(json_encode($data));
	}
}