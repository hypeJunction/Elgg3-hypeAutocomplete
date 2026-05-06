<?php

namespace hypeJunction\Autocomplete;

use Elgg\DefaultPluginBootstrap;

/**
 * Bootstrap class.
 */
class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		elgg_extend_view('elements/forms.css', 'autocomplete/stylesheet.css');

		elgg_define_js('select2', [
			'src' => elgg_get_simplecache_url('select2/js/select2.full.min.js'),
			'deps' => ['jquery'],
		]);

		elgg_extend_view('input/select', 'autocomplete/select');

		elgg_register_plugin_hook_handler('view_vars', 'input/select', AddAccessIcons::class, 900);
		elgg_register_plugin_hook_handler('view_vars', 'input/select', PrepareAutocomplete::class, 900);

		elgg_extend_view('theme_sandbox/forms', 'theme_sandbox/forms/guids');
	}
}
