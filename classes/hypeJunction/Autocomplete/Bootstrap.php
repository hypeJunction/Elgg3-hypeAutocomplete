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
	public function init(): void {
		elgg_extend_view('elements/forms.css', 'autocomplete/stylesheet.css');

		elgg_register_esm('select2', elgg_get_simplecache_url('select2/js/select2.full.min.js'));

		elgg_extend_view('input/select', 'autocomplete/select');

		elgg_register_event_handler('view_vars', 'input/select', AddAccessIcons::class, 900);
		elgg_register_event_handler('view_vars', 'input/select', PrepareAutocomplete::class, 900);

		elgg_extend_view('theme_sandbox/forms', 'theme_sandbox/forms/guids');
	}
}
