define(function (require) {

		var elgg = require('elgg');
		var Ajax = require('elgg/Ajax');
		var $ = require('jquery');
		require('select2');

		var autocomplete = {
			format: function(state, text) {
				var $elem = $(state.element);

				var img = state.iconUrl || $elem.data('iconUrl');
				var icon = state.iconName || $elem.data('iconName');

				var tmpl;

				text = text || state.text;

				if (img) {
					tmpl = $('<span><span class="select-img"><img src="' + img + '"/></span><span class="select-label">' + text + '</span></span>');
				} else if (icon) {
					tmpl = $('<span><i class="select-icon elgg-icon fa fa-' + icon + '"></i><span class="select-label">' + text + '</span></span>');
				} else {
					tmpl = $('<span class="select-label">' + text + '</span>');
				}

				return tmpl;
			},
			formatSelection: function (state) {
				var text = state.selection || state.text;
				return autocomplete.format(state, text);
			},
			formatResult: function (state) {
				var text = state.result || state.text;
				return autocomplete.format(state.text);
			},
			init: function () {
				$('.elgg-input-select:not(.select2-hidden-accessible):not(.elgg-no-js)').each(function () {

					var $elem = $(this);
					var opts = $elem.data('selectOpts') || {};

					opts.ajax = autocomplete.prepareAjaxParams($elem);

					opts = elgg.trigger_hook('options', 'select', {
						$elem: $elem
					}, opts);

					$elem.select2($.extend({}, autocomplete.getDefaults(), opts));
				});
			},
			prepareAjaxParams: function($elem) {
				var source = $elem.data('source');
				if (!source) {
					return null;
				}

				return {
					url: source,
					delay: 250,
					dataType: 'json',
					data: function(params) {
						return {
							q: params.term,
							format: 'select2',
							prop: $elem.data('prop') || 'guid',
							value: $elem.val()
						};
					},
					transport: function (params, success, failure) {
						var ajax = new Ajax(false);
						return ajax.path(params.url, params).done(success).fail(failure);
					},
					processResults: function (data) {
						return {
							results: data.map(function(i) {
								if (!i.id) {
									i.id = i.value;
								}
								if (!i.text) {
									i.text = i.name || i.title || i.label;
								}

								return i;
							})
						};
					}
				};
			},
			getDefaults: function () {
				return {
					templateResult: autocomplete.formatResult,
					templateSelection: autocomplete.formatSelection,
					language: function () {
						return {
							errorLoading: function () {
								return elgg.echo('autocomplete:errorLoad');
							},
							inputTooLong: function (e) {
								return elgg.echo('autocomplete:inputTooLong', [e.maximum]);
							},
							inputTooShort: function (e) {
								return elgg.echo('autocomplete:inputTooShort', [e.minimum]);
							},
							loadingMore: function () {
								return elgg.echo('autocomplete:loadingMore');
							},
							maximumSelected: function (e) {
								return elgg.echo('autocomplete:maximumSelected', [e.maximum]);
							},
							noResults: function () {
								return elgg.echo('autocomplete:noResults');
							},
							searching: function () {
								return elgg.echo('autocomplete:searching');
							}
						};
					}
				};
			}
		};

		return autocomplete;
	}
);