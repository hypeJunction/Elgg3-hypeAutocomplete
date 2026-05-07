<?php

namespace hypeJunction\Autocomplete;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

class AddAccessIconsTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypeautocomplete';
	}

	public function up(): void {}
	public function down(): void {}

	private function makeHook(array $vars): Hook {
		$hook = $this->getMockBuilder(Hook::class)
			->disableOriginalConstructor()
			->getMock();
		$hook->method('getValue')->willReturn($vars);
		return $hook;
	}

	public function testReturnsVarsUntouchedWhenNotAccessSelect(): void {
		$vars = [
			'class' => ['some-other-class'],
			'options_values' => [1 => 'A', 2 => 'B'],
		];

		$result = (new AddAccessIcons())($this->makeHook($vars));

		$this->assertSame($vars, $result);
	}

	public function testAddsGlobeIconForPublicAccess(): void {
		$vars = [
			'class' => ['elgg-input-access'],
			'options_values' => [
				ACCESS_PUBLIC => 'Public',
			],
		];

		$result = (new AddAccessIcons())($this->makeHook($vars));

		$this->assertSame('globe', $result['options_values'][0]['data-icon-name']);
	}

	public function testAddsLockIconForPrivateAccess(): void {
		$vars = [
			'class' => ['elgg-input-access'],
			'options_values' => [
				ACCESS_PRIVATE => 'Private',
			],
		];

		$result = (new AddAccessIcons())($this->makeHook($vars));

		$this->assertSame('lock', $result['options_values'][0]['data-icon-name']);
	}

	public function testWrapsScalarOptionAsAssociativeArray(): void {
		$vars = [
			'class' => ['elgg-input-access'],
			'options_values' => [
				ACCESS_PUBLIC => 'Public',
			],
		];

		$result = (new AddAccessIcons())($this->makeHook($vars));

		$option = $result['options_values'][0];
		$this->assertSame('Public', $option['text']);
		$this->assertSame(ACCESS_PUBLIC, $option['value']);
		$this->assertSame('Public', $option['title']);
	}

	public function testFallsBackToCogIconForUnknownAccess(): void {
		$vars = [
			'class' => ['elgg-input-access'],
			'options_values' => [
				9999 => 'Custom',
			],
		];

		$result = (new AddAccessIcons())($this->makeHook($vars));

		$this->assertSame('cog', $result['options_values'][0]['data-icon-name']);
	}
}
