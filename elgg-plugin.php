<?php

$plugin_root = __DIR__;
$root = dirname(dirname($plugin_root));
$alt_root = dirname(dirname(dirname($root)));
if (file_exists("$plugin_root/vendor/autoload.php")) {
	$path = $plugin_root;
} else if (file_exists("$root/vendor/autoload.php")) {
	$path = $root;
} else {
	$path = $alt_root;
}

return [
	'plugin' => [
		'name' => 'hypeAutocomplete',
		'version' => '6.0.0',
		'dependencies' => [
			'hypeajax' => [
				'must_be_active' => true,
			],
		],
	],

	'bootstrap' => \hypeJunction\Autocomplete\Bootstrap::class,

	'views' => [
		'default' => [
			// NOT $path: that heuristic points at the Elgg root when composer installs
			// this plugin (no plugin-local vendor/autoload.php), which resolved the alias
			// to /var/www/html/vendors/select2/ and made Elgg log a RecursiveDirectoryIterator
			// failure at boot. The vendored bundle always lives beside this file.
			'select2/' => "$plugin_root/vendors/select2/",
		],
	],
	'routes' => [
		'autocomplete:tags' => [
			'path' => '/autocomplete/tags',
			'controller' => \hypeJunction\Autocomplete\SearchTags::class,
			'middleware' => [
				\Elgg\Router\Middleware\AjaxGatekeeper::class,
			]
		],
		'autocomplete:guids' => [
			'path' => '/autocomplete/guids',
			'controller' => \hypeJunction\Autocomplete\SearchEntities::class,
			'middleware' => [
				\Elgg\Router\Middleware\AjaxGatekeeper::class,
			]
		],
	]
];
