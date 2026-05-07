<?php

namespace hypeJunction\Autocomplete;

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\IntegrationTestCase;
use Elgg\Request;

class SearchTagsTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypeautocomplete';
	}

	public function up(): void {}
	public function down(): void {}

	public function testThrowsBadRequestWhenQueryParamMissing(): void {
		$request = $this->getMockBuilder(Request::class)
			->disableOriginalConstructor()
			->getMock();
		$request->method('getParam')->willReturn(null);

		$this->expectException(BadRequestException::class);
		(new SearchTags())($request);
	}

	public function testReturnsEmptyJsonArrayWhenNoTagsMatch(): void {
		$request = $this->getMockBuilder(Request::class)
			->disableOriginalConstructor()
			->getMock();
		$request->method('getParam')
			->willReturnCallback(fn($k) => $k === 'q' ? 'definitely-not-a-tag-' . uniqid() : null);
		$request->method('elgg')->willReturn(_elgg_services());

		$response = (new SearchTags())($request);

		$this->assertSame('[]', $response->getContent());
	}

	public function testReturnsMatchingTagsAsSelect2Payload(): void {
		$tag = 'autocomplete-tag-' . uniqid();
		$user = $this->createUser();
		$obj = $this->createObject([
			'subtype' => 'page',
			'owner_guid' => $user->guid,
			'access_id' => ACCESS_PUBLIC,
			'tags' => [$tag],
		]);

		$request = $this->getMockBuilder(Request::class)
			->disableOriginalConstructor()
			->getMock();
		$request->method('getParam')
			->willReturnCallback(fn($k) => $k === 'q' ? $tag : null);
		$request->method('elgg')->willReturn(_elgg_services());

		try {
			$response = (new SearchTags())($request);
			$payload = json_decode($response->getContent(), true);

			$this->assertIsArray($payload);
			$this->assertNotEmpty($payload);
			$this->assertSame($tag, $payload[0]['id']);
			$this->assertSame($tag, $payload[0]['text']);
		} finally {
			$obj->delete();
		}
	}
}
