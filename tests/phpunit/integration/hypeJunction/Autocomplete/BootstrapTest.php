<?php

namespace hypeJunction\Autocomplete;

use Elgg\IntegrationTestCase;

/**
 * Characterization suite for hypeautocomplete on Elgg 4.x.
 *
 * 4-class plugin with 2 declarative controller routes and 2 view_vars
 * hook handlers lifted from the 3.x start.php into a Bootstrap::init.
 */
class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypeautocomplete';
	}

	public function up() {}
	public function down() {}

	// --- plugin lifecycle ---

	public function testPluginIsRegistered() {
		$this->assertInstanceOf(\ElggPlugin::class, elgg_get_plugin_from_id('hypeautocomplete'));
	}

	public function testPluginIsActive() {
		$this->assertTrue(elgg_get_plugin_from_id('hypeautocomplete')->isActive());
	}

	public function testHypeajaxDepIsActive() {
		$p = elgg_get_plugin_from_id('hypeajax');
		$this->assertNotNull($p);
		$this->assertTrue($p->isActive());
	}

	// --- migration invariants ---

	public function testNoStartPhpPresent() {
		$pluginPath = elgg_get_plugin_from_id('hypeautocomplete')->getPath();
		$this->assertFileDoesNotExist($pluginPath . 'start.php');
	}

	public function testBootstrapRegisteredInPluginManifest() {
		$plugin = elgg_get_plugin_from_id('hypeautocomplete');
		$data = include $plugin->getPath() . 'elgg-plugin.php';
		$this->assertArrayHasKey('bootstrap', $data);
		$this->assertSame(Bootstrap::class, $data['bootstrap']);
	}

	// --- class autoloading ---

	public function testBootstrapClassLoads() {
		$this->assertTrue(class_exists(Bootstrap::class));
	}

	public function testBootstrapExtendsDefaultPluginBootstrap() {
		$r = new \ReflectionClass(Bootstrap::class);
		$this->assertTrue($r->isSubclassOf(\Elgg\DefaultPluginBootstrap::class));
	}

	public function testAddAccessIconsClassLoads() {
		$this->assertTrue(class_exists(AddAccessIcons::class));
	}

	public function testPrepareAutocompleteClassLoads() {
		$this->assertTrue(class_exists(PrepareAutocomplete::class));
	}

	public function testSearchTagsControllerLoads() {
		$this->assertTrue(class_exists(SearchTags::class));
	}

	public function testSearchEntitiesControllerLoads() {
		$this->assertTrue(class_exists(SearchEntities::class));
	}

	// --- Bootstrap::init hook wiring ---

	public function testViewVarsInputSelectHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('view_vars', $handlers);
		$this->assertArrayHasKey('input/select', $handlers['view_vars']);
	}
}
