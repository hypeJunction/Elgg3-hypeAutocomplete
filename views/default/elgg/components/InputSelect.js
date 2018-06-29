define(function (require) {

	var $ = require('jquery');

	var autocomplete = require('autocomplete/select');

	var Vue = require('elgg/Vue');

	var Input = require('elgg/components/Input');

	var template = require('text!elgg/components/InputSelect.html');

	Vue.component('elgg-input-select', {
		template: template,
		extends: Input,
		props: {
			options: {
				type: Array,
				required: true
			},
			source: {},
			config: {
				type: Object,
				default: function() {
					return {};
				}
			},
			multiple: {
				type: Boolean,
				default: function() {
					return false;
				}
			}
		},
		computed: {
			filteredOptions: function () {
				var self = this;
				var options = this.options;
				options = options.map(function (option) {
					if (typeof option === 'string') {
						return {
							value: option,
							label: option,
							iconUrl: null,
							iconName: null
						};
					}
					return option;
				});
				if (this.placeholder) {
					options.unshift({
						disabled: true,
						label: this.placeholder,
						placeholder: true,
						value: null,
						iconUrl: null,
						iconName: null
					});
				}

				options.forEach(function (option) {
					if (typeof self.inputValue === 'undefined') {
						option.selected = option.placeholder === true;
					} else {
						option.selected = self.inputValue === option.value;
					}
				});

				return options;
			},
			normalizedConfig: function() {
				var config = $.extend({}, this.config);

				if (!config.minimumResultsForSearch) {
					config.minimumResultsForSearch = 20;
				}

				if (!config.containerCssClass) {
					config.containerCssClass = 'elgg-autocomplete-select';
				}

				return config;
			}
		},
		mounted: function() {
			var self = this;
			autocomplete.bind($(this.$refs.select), this.normalizedConfig);

			$(this.$refs.select).on('change', function(e) {
				var $elem = $(this);
				self.$emit('input', $elem.val());
			});
		}
	});

});
