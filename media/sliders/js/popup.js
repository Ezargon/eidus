/**
 * @package         Sliders
 * @version         5.1.10
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	"use strict";

	$(document).ready(function() {
		setTimeout(function() {
			nnSlidersPopup.init();
		}, 1000);
	});

	var nnSlidersPopup = {
		params  : {
		},
		booleans: ['icon'],

		init: function() {
			var self = this;

			this.fillFromSelection();

			$('input[name$="[default]"][value="0"]').click(function(e) {
				self.unsetDefault(this);
			});

			$('input[name$="[default]"][value="1"]').click(function(e) {
				self.setDefault(this);
			});

			$('input[name$="[default]"][value="1"]').each(function($i, el) {
				if ($(el).attr('checked')) {
					$('.' + $(el).parent().attr('id') + '_icon').show();
				}
			});

			$('.nn_overlay').css('cursor', '').fadeOut();
		},

		setDefault: function(el) {
			$('.' + $(el).parent().attr('id') + '_icon').show();
			this.closeOtherDefaults(el);
		},

		unsetDefault: function(el) {
			$('.' + $(el).parent().attr('id') + '_icon').hide();
		},

		closeOtherDefaults: function(el) {
			var self = this;
			var $el = $(el);

			$('input[name$="[default]"][value="1"]').each(function($i, input) {
				if ($(input).attr('name') == $el.attr('name')) {
					return;
				}

				$('.' + $(input).parent().attr('id') + '_icon').hide();
				self.setRadioOption($(input).attr('name'), 0);
			});
		},

		fillFromSelection: function() {
			var self = this;
			var selection = this.getSelection();

			if (!selection) {
				return;
			}

			var form = document.getElementById('slidersForm');

			var params_sets = this.getParamsFromSelection(selection);

			if (!params_sets) {
				return;
			}

			$.each(params_sets, function(i, params) {
				var id = 'slider_' + (i + 1);

				$('#' + id).find('input[type="text"],textarea,input[type="hidden"]').each(function(i, input) {
					var param_type = $(input).attr('id').substr(id.length + 1);

					if (typeof params[param_type] == "undefined") {
						return;
					}

					form[$(input).attr('id')].value = params[param_type];
				});

				if (params.content) {
					$('#' + id + '_content').html(params.content).show();
				}

				if (typeof params.default != "undefined") {
					self.setRadioOption(id + '[default]', (params.default ? 1 : 0));
				}

				if (params.nested_id) {
					self.setRadioOption(id + '[nested]', 1);
					form[id + '[nested_id]'].value = params.nested_id;
				}

				$.each(self.booleans, function(b, boolean) {
					if (typeof params[boolean] == "undefined") {
						return;
					}

					self.setRadioOption(id + '[' + boolean + ']', (params[boolean] ? 1 : 0));
				});

				$.each(self.params, function(param_type, param_set) {
					$.each(param_set, function(p, param) {
						if (typeof params[param] == "undefined") {
							return;
						}

						self.setRadioOption(id + '[' + param_type + ']', param);
					});
				});

			});
		},

		getSelection: function() {
			var editor_textarea = window.parent.document.getElementById(sliders_editorname);
			if (!editor_textarea) {
				return false;
			}

			var iframes = editor_textarea.parentNode.getElementsByTagName('iframe');
			if (!iframes.length) {
				return false;
			}

			var editor_frame = iframes[0];
			var contentWindow = editor_frame.contentWindow;
			var selection = '';

			if (typeof contentWindow.getSelection != "undefined") {
				var sel = contentWindow.getSelection();
				if (sel.rangeCount) {
					var container = contentWindow.document.createElement("div");
					var len = sel.rangeCount
					for (i = 0; i < len; ++i) {
						container.appendChild(sel.getRangeAt(i).cloneContents());
					}
					selection = container.innerHTML;
				}
			} else if (typeof contentWindow.document.selection != "undefined") {
				if (contentWindow.document.selection.type == "Text") {
					selection = contentWindow.document.selection.createRange().htmlText;
				}
			}

			return selection;
		},

		getParamsFromSelection: function(selection) {
			if (selection.indexOf(sliders_tag_characters[0] + sliders_tag_open) == -1) {
				return false;
			}

			var tag_open = sliders_tag_characters[0] + sliders_tag_open;
			var tag_close = sliders_tag_characters[0] + '/' + sliders_tag_close;
			var nested_id = '';

			if (selection.indexOf(tag_open + '-') == selection.indexOf(tag_open)) {
				// First slider has a nested id
				var regex = new RegExp('^.*?' + this.preg_quote(tag_open) + '-(.*?)' + this.preg_quote(sliders_tag_delimiter) + '.*$', 'gi');
				nested_id = selection.replace(regex, '$1');

				tag_open += '-' + nested_id;
				tag_close += '-' + nested_id;
			}

			tag_open += '-' + sliders_tag_delimiter;
			tag_close += '-' + sliders_tag_characters[1];

			selection = selection.split(tag_open);

			var param_sets = [];
			var count = selection.length;

			if (count > (sliders_max_count + 1)) {
				var surplus = selection.slice(sliders_max_count, count);
				selection = selection.slice(0, sliders_max_count);
				selection[sliders_max_count] = surplus.join(tag_open);
				count = selection.length;
			}

			for (i = 1; i < count; i++) {
				if (selection[i].indexOf('}') == -1) {
					continue;
				}

				params = {
					'title'  : '',
					'content': '',
					'alias'  : '',
					'class'  : ''
				}

				var title = selection[i].substr(0, selection[i].indexOf('}'));
				var content = selection[i].substr(selection[i].indexOf('}') + 1);

				title = title.replace(/\s*\|\s*/g, '|');

				var regex = new RegExp('\\|alias=(.*?)(\\||$)');
				if (title.match(regex)) {
					var match = title.match(regex);
					params['alias'] = match[1];
					title = title.replace(regex, '$2');
				}

				var regex = new RegExp('\\|(?:closed|close|inactive)(\\||$)');
				if (title.match(regex)) {
					params['default'] = false;
					title = title.replace(regex, '$1');
				}

				var regex = new RegExp('\\|(?:default|opened|open|active)(\\||$)');
				if (title.match(regex)) {
					params['default'] = true;
					title = title.replace(regex, '$1');
				}

				$.each(this.booleans, function(b, boolean) {
					var regex = new RegExp('\\|' + boolean + '(\\||$)');
					if (!title.match(regex)) {
						return;
					}

					var regex = new RegExp('\\|' + boolean + '=0(\\||$)');
					if (title.match(regex)) {
						params[boolean] = false;
						title = title.replace(regex, '$1');
						return;
					}

					params[boolean] = true;
					var regex = new RegExp('\\|' + boolean + '(?:=1)?(\\||$)');
					title = title.replace(regex, '$1');
				});

				$.each(this.params, function(param_type, param_set) {
					$.each(param_set, function(p, param) {
						var regex = new RegExp('\\|' + param + '(\\||$)');
						if (!title.match(regex)) {
							return;
						}

						params[param] = true;
						title = title.replace(regex, '$1');
					});
				});


				if (title.indexOf('|') != -1) {
					var classes = title.split('|');
					title = classes.shift();
					params.class = classes.join(' ');
				}

				params.title = title;

				var regex = new RegExp('^(?:</p>)?(.*?)(?:(?:<p>)?' + this.preg_quote(tag_close) + '(?:</p>)?|<p>)?$', 'gi');
				params.content = content.replace(regex, '$1');

				if (i == 1 && nested_id) {
					params.nested_id = nested_id;
				}

				param_sets.push(params);
			}

			return param_sets;
		},

		insertText: function() {
			var self = this;
			var form = document.getElementById('slidersForm');

			var html = '';
			var nested_id = $('input[name="slider_1[nested]"][value="1"]').attr('checked') ? '-' + form['slider_1[nested_id]'].value.trim() : '';

			if (form['slider_1[title]'].value.trim() == '') {
				alert(window['sliders_error_empty_title']);

				return false;
			}

			var first = true;
			$('.tab-pane').each(function($i, el) {
				var title = form[el.id + '[title]'].value.trim();

				if (title == '') {
					return;
				}

				if (form[el.id + '[alias]'].value) {
					title += '|alias=' + form[el.id + '[alias]'].value;
				}

				if (form[el.id + '[class]'].value) {
					title += '|' + form[el.id + '[class]'].value;
				}

				var default_no = $('input[name="' + el.id + '[default]"][value="0"]');
				var default_yes = $('input[name="' + el.id + '[default]"][value="1"]');

				var has_default_yes = false;
				$('input[name$="[default]"][value="1"]').each(function($i, field) {
					if ($(field).attr('checked')) {
						has_default_yes = true;
					}
				});

				if (first && default_no.attr('checked') && !has_default_yes) {
					title += '|closed';
				}
				if (!first && default_yes.attr('checked')) {
					title += '|default';
				}

				$.each(self.booleans, function(b, boolean) {
					var field_name = el.id + '[' + boolean + ']';

					var input_default = $('input[name="' + field_name + '"][value=""]');
					var input_no = $('input[name="' + field_name + '"][value="0"]');
					var input_yes = $('input[name="' + field_name + '"][value="1"]');

					if (input_default.attr('checked')) {
						return;
					}

					if (input_default.length && input_no.attr('checked')) {
						title += '|' + boolean + '=0';
						return;
					}

					if (input_yes.attr('checked')) {
						title += '|' + boolean;
					}
				});

				$.each(self.params, function(param_type, param_set) {
					var field_name = el.id + '[' + param_type + ']';

					var input_default = $('input[name="' + field_name + '"][value=""]');

					if (input_default.attr('checked')) {
						return;
					}

					$('input[name="' + field_name + '"]').each(function($i, el) {
						if (!$(el).attr('checked')) {
							return;
						}

						title += '|' + $(el).attr('value');
					});
				});


				var content = $('#' + el.id + '_content').html().trim();

				if (content == '') {
					content = sliders_content_placeholder;
				}

				html += '<p>' + sliders_tag_characters[0] + sliders_tag_open + nested_id + sliders_tag_delimiter + title + sliders_tag_characters[1] + '</p>';
				html += content ? content : '<p></p>';

				first = false;
			});

			if (html == '') {
				alert(window['sliders_error_empty_title']);

				return false;
			}

			html += '<p>' + sliders_tag_characters[0] + '/' + sliders_tag_close + nested_id + sliders_tag_characters[1] + '</p>';

			window.parent.jInsertEditorText(html, sliders_editorname);

			return true;
		},

		setRadioOption: function(name, value) {
			var inputs = $('input[name="' + name + '"]');
			var input = $('input[name="' + name + '"][value="' + value + '"]');

			$('label[for="' + input.attr('id') + '"]').click();
			inputs.attr('checked', false);
			input.attr('checked', true).click();
		},

		setSelectOption: function(name, value) {
			var select = $('select[name="' + name + '"]');
			var option = $('select[name="' + name + '"] option[value="' + value + '"]');

			select.attr('value', value).click();
			select.attr('selected', true).click();
		},

		preg_quote: function(str) {
			return (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!<>\|\:])/g, '\\$1');
		}
	}
})
(jQuery);
