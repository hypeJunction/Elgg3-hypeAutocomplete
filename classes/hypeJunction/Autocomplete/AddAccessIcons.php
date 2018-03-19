<?php

namespace hypeJunction\Autocomplete;

use Elgg\Hook;

class AddAccessIcons {

	/**
	 * Add access icons
	 *
	 * @elgg_plugin_hook view_vars input/select
	 *
	 * @param Hook $hook Hook
	 *
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$vars = $hook->getValue();

		$classes = elgg_extract_class($vars);
		if (!in_array('elgg-input-access', $classes)) {
			return $vars;
		}

		$options = elgg_extract('options_values', $vars);
		$vars['options_values'] = [];

		foreach ($options as $value => $option) {
			if (is_array($option)) {
				$value = elgg_extract('value', $vars);
			} else {
				$option = [
					'text' => $option,
					'value' => $value,
					'title' => $option,
				];
			}

			switch ($value) {
				case ACCESS_PUBLIC :
				case ACCESS_LOGGED_IN :
					$icon_name = 'globe';
					break;
				case ACCESS_PRIVATE :
					$icon_name = 'lock';
					break;
				default:
					$icon_name = 'cog';
					$collection = get_access_collection($value);
					if ($collection) {
						switch ($collection->getSubtype()) {
							case 'friends' :
								$icon_name = 'user';
								break;

							case 'group_acl' :
								$icon_name = 'users';
								break;
						}
					}

					break;
			}

			$option['data-icon-name'] = $icon_name;

			$vars['options_values'][] = $option;
		}

		return $vars;
	}
}