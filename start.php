<?php

require_once __DIR__ . '/autoloader.php';

return function () {

	elgg_register_event_handler('init', 'system', function () {

		elgg_extend_view('elements/forms.css', 'autocomplete/stylesheet.css');

		elgg_define_js('select2', [
			'src' => elgg_get_simplecache_url('select2/js/select2.full.min.js'),
			'deps' => ['jquery'],
		]);

		elgg_extend_view('input/select', 'autocomplete/select');

		elgg_register_plugin_hook_handler('view_vars', 'input/select', \hypeJunction\Autocomplete\AddAccessIcons::class, 900);
		elgg_register_plugin_hook_handler('view_vars', 'input/select', \hypeJunction\Autocomplete\PrepareAutocomplete::class, 900);

		elgg_extend_view('theme_sandbox/forms', 'theme_sandbox/forms/guids');
	});
};
