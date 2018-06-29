define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');

	var autocomplete = require('autocomplete/select');

	var Vue = require('elgg/Vue');

	var Input = require('elgg/components/Input');

	var template = require('text!elgg/components/InputSelect.html');

	Vue.component('elgg-input-guids', {
		template: template,
		extends: Input,
		props: {
			options: {
				type: Array,
				default: function () {
					return [];
				}
			},
			source: {
				type: String
			},
			type: {
				type: String
			},
			subtype: {
				type: String
			},
			metadata: {
				type: Object
			},
			exclude: {
				type: Array
			},
			config: {
				type: Object,
				default: function () {
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
		data: function () {
			return {
				valueOptions: [],
				select2: null
			};
		},
		computed: {
			filteredOptions: function () {
				var self = this;
				var options = this.options;

				options = options.concat(this.valueOptions);
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
						if (typeof self.value === 'array') {
							option.selected = self.inputValue.indexOf(option.value) !== false;
						} else {
							option.selected = self.inputValue === option.value;
						}
					}
				});

				return options;
			},
			normalizedConfig: function () {
				var config = $.extend({}, this.config);

				if (!config.containerCssClass) {
					config.containerCssClass = 'elgg-autocomplete-guids';
				}

				if (!config.minimumInputLength) {
					config.minimumInputLength = 2;
				}

				var source = this.source;
				if (!source) {
					source = elgg.normalize_url('autocomplete/guids');
				}

				var queryData = {
					type: this.type,
					subtype: this.subtype,
					metadata: this.metadata,
					exclude: this.exclude
				};

				var parts = elgg.parse_url(source),
					args = {}, base = '';

				if (typeof parts['host'] === 'undefined') {
					if (source.indexOf('?') === 0) {
						base = '?';
						args = elgg.parse_str(parts['query']);
					}
				} else {
					if (typeof parts['query'] !== 'undefined') {
						args = elgg.parse_str(parts['query']);
					}
					var split = source.split('?');
					base = split[0] + '?';
				}

				$.extend(true, args, queryData);
				source = base + $.param(args);

				config.source = source;
				return config;
			}
		},
		mounted: function () {
			var self = this;

			if (self.value) {
				var ajax = new Ajax(false);
				ajax.path('autocomplete/guids', {
					data: {
						guids: self.value
					}
				}).done(function (options) {
					options = options.map(function (e) {
						return {
							value: e.id,
							label: e.text,
							iconUrl: e.iconUrl
						};
					});

					self.valueOptions = options;
				});
			}

			self.select2 = autocomplete.bind($(this.$refs.select), this.normalizedConfig);

			this.select2.on('change', function() {
				var $elem = $(this);

				self.$emit('input', $elem.val());
			});
		}
	});

});
