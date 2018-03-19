<hr />
<form class="elgg-form">
    <fieldset>
        <?php
		echo elgg_view_field([
			'#label' => 'GUIDs (.elgg-input-guids) with default data source',
			'#type' => 'guids',
			'value' => elgg_get_entities([
				'types' => 'user',
				'order_by' => new \Elgg\Database\Clauses\OrderByClause('RAND()'),
				'limit' => 5,
			]),
		]);

		echo elgg_view_field([
			'#label' => 'GUIDs (.elgg-input-guids) with default data source and options',
			'#type' => 'guids',
			'options' => [
				'type' => 'user',
				'metadata' => [
					'validated' => '1',
				],
			],
		]);

		echo elgg_view_field([
			'#label' => 'GUIDs (.elgg-input-guids) with preset items',
			'#type' => 'guids',
			'items' => elgg_get_entities([
				'types' => 'group',
				'limit' => 0,
				'callback' => function($row) {
					return $row->guid;
				},
				'order_by_metadata' => [
					'name' => 'name',
					'direction' => 'ASC',
					'as' => 'string',
				],
			]),
		]);

		?>
	</fieldset>
</form>
