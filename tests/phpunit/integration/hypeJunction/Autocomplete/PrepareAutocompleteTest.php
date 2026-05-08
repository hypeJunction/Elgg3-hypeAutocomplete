<?php

namespace hypeJunction\Autocomplete;

use Elgg\Event;
use Elgg\IntegrationTestCase;

class PrepareAutocompleteTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypeautocomplete';
	}

	public function up(): void {}
	public function down(): void {}

	private function makeHook(array $vars): Event {
		$hook = $this->getMockBuilder(Event::class)
			->disableOriginalConstructor()
			->getMock();
		$hook->method('getValue')->willReturn($vars);
		return $hook;
	}

	public function testReturnsEarlyWithNoJsClassWhenNoJsFlagSet(): void {
		$vars = [
			'no_js' => true,
			'class' => ['existing-class'],
		];

		$result = (new PrepareAutocomplete())($this->makeHook($vars));

		$this->assertArrayNotHasKey('data-select-opts', $result);
		$classes = is_array($result['class']) ? $result['class'] : explode(' ', (string) $result['class']);
		$this->assertContains('elgg-no-js', $classes);
	}

	public function testEncodesSelect2ConfigAsJsonInDataSelectOpts(): void {
		$vars = [];

		$result = (new PrepareAutocomplete())($this->makeHook($vars));

		$this->assertArrayHasKey('data-select-opts', $result);
		$config = json_decode($result['data-select-opts'], true);
		$this->assertIsArray($config);
		$this->assertSame(20, $config['minimumResultsForSearch']);
		$this->assertSame('elgg-autocomplete-select', $config['containerCssClass']);
		$this->assertArrayHasKey('placeholder', $config);
	}

	public function testHonoursExplicitPlaceholderFromVars(): void {
		$vars = [
			'placeholder' => 'Pick something',
		];

		$result = (new PrepareAutocomplete())($this->makeHook($vars));
		$config = json_decode($result['data-select-opts'], true);

		$this->assertSame('Pick something', $config['placeholder']);
	}

	public function testCallerSuppliedConfigOverridesDefaults(): void {
		$vars = [
			'config' => [
				'placeholder' => 'Custom',
				'minimumResultsForSearch' => 5,
				'containerCssClass' => 'custom-class',
			],
		];

		$result = (new PrepareAutocomplete())($this->makeHook($vars));
		$config = json_decode($result['data-select-opts'], true);

		$this->assertSame('Custom', $config['placeholder']);
		$this->assertSame(5, $config['minimumResultsForSearch']);
		$this->assertSame('custom-class', $config['containerCssClass']);
		$this->assertArrayNotHasKey('config', $result, 'config key must be unset before render');
	}

	public function testForcesMultipleWhenDataSourceIsPresent(): void {
		$vars = [
			'data-source' => '/autocomplete/guids',
		];

		$result = (new PrepareAutocomplete())($this->makeHook($vars));
		$config = json_decode($result['data-select-opts'], true);

		$this->assertTrue($config['multiple']);
	}
}
