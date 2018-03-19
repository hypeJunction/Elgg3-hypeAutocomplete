<?php

namespace hypeJunction\Autocomplete;

use Elgg\BadRequestException;
use Elgg\Database\QueryBuilder;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

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

		elgg_signed_request_gatekeeper();

		$query = $request->getParam('q');

		if (!$query) {
			throw new BadRequestException();
		}

		$options['limit'] = 100;
		$options['query'] = $query;

		$type = $request->getParam('type');
		if ($type) {
			$options['type'] = $type;
			$options['subtype'] = $request->getParam('subtype');
		} else {
			$options['type'] = 'object';
			$options['subtypes'] = get_registered_entity_types('object');
		}

		$options['owner_guids'] = $request->getParam('owner_guids');
		$options['container_guids'] = $request->getParam('container_guids');
		$options['guids'] = $request->getParam('guids');

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

		$exclude = array_merge($value, $exclude);

		if (!empty($exclude)) {
			$options['wheres'][] = function(QueryBuilder $qb) use ($exclude) {
				return $qb->compare('e.guid', 'NOT IN', $exclude, ELGG_VALUE_INTEGER);
			};
		}

		$entities = elgg_search($options);

		if (empty($entities)) {
			return elgg_ok_response(json_encode([]));
		}

		$data = array_map(function(\ElggEntity $e) {
			return [
				'id' => $e->guid,
				'text' => $e->getDisplayName(),
				'iconUrl' => $e->getIconURL(['size' => 'small']),
			];
		}, $entities);

		return elgg_ok_response(json_encode($data));
	}
}