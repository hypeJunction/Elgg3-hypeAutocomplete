<?php

namespace hypeJunction\Autocomplete;

use Elgg\BadRequestException;
use Elgg\Database\QueryBuilder;
use Elgg\EntityNotFoundException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class SearchTags {

	/**
	 * Autocomplete tags
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws BadRequestException
	 */
	public function __invoke(Request $request) {

		$query = $request->getParam('q');

		if (!$query) {
			throw new BadRequestException();
		}

		elgg_set_http_header('Content-Type: application/json');

		$qb = \Elgg\Database\Select::fromTable('metadata', 'md');
		$qb->select('md.value AS tag')
			->addSelect('COUNT(md.id) AS total')
			->where($qb->compare('md.name', 'IN', elgg_get_registered_tag_metadata_names(), ELGG_VALUE_STRING))
			->andWhere($qb->compare('md.value', 'LIKE', "%$query%", ELGG_VALUE_STRING))
			->groupBy('md.value')
			->orderBy('total', 'desc');

		$tags = $request->elgg()->db->getData($qb);

		if (empty($tags)) {
			return elgg_ok_response(json_encode([]));
		}

		$data = array_map(function($e) {
			return [
				'id' => $e->tag,
				'text' => $e->tag,
			];
		}, $tags);

		return elgg_ok_response(json_encode($data));
	}
}