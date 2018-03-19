<?php

namespace hypeJunction\Autocomplete;

use Elgg\Hook;

class PrepareAutocomplete {

	/**
	 * Prepare select vars
	 *
	 * @elgg_plugin_hook view_vars input/select
	 *
	 * @param Hook $hook Hook
	 *
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$vars = $hook->getValue();

		if (elgg_extract('no_js', $vars)) {
			$vars['class'] = elgg_extract_class($vars, 'elgg-no-js');
			return $vars;
		}

		$config = (array) elgg_extract('config', $vars, []);
		unset($vars['config']);

		if (!isset($config['placeholder'])) {
			$placeholder = elgg_extract('placeholder', $vars);
			$config['placeholder'] = $placeholder ? : elgg_echo('autocomplete:select:placeholder');
		}

		if (!isset($config['minimumResultsForSearch'])) {
			$config['minimumResultsForSearch'] = 20;
		}

		if (!isset($config['containerCssClass'])) {
			$config['containerCssClass'] = 'elgg-autocomplete-select';
		}

		if (isset($vars['data-source'])) {
			$config['multiple'] = true;
		}

		$vars['data-select-opts'] = json_encode($config);

		return $vars;
	}
}