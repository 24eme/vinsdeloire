(function($)
{

	var normalize = function(term) {
		var accentMap = { "à": "a", "á": "a", "ã": "a", "ä": "a", "å": "a", "â":"a", "é": "e", "è": "e", "ê": "e", "ë": "e", "ì":"i", "í":"i", "î":"i", "ï":"i", "ð": "o", "ò": "o", "ó": "o", "ô": "o", "õ": "o", "ö":"o", "ù": "u", "ú": "u", "û": "u", "ü": "u", "ý": "y", "ÿ":"y", "ç": "c", "À": "A", "Á": "A", "Â": "A", "Ã": "A", "Ä": "A", "Å": "A", "Ç": "C", "È": "E", "É": "E", "Ê": "E", "Ë": "E", "Ì" : "I", "Í": "I", "Î": "I", "Ï": "I", "Ò":"O", "Ó":"O", "Ô":"O", "Õ":"O", "Ö":"O", "Ù":"U", "Ú":"U", "Û":"U", "Ü":"U", "Ý":"Y" };
		var ret = "";
		for ( var i = 0; i < term.length; i++ ) {
			ret += accentMap[ term.charAt(i) ] || term.charAt(i);
		}
		return ret;
	};

	var search = function(text, term, hightlight) {
		if (!hightlight) {
			hightlight = "<strong>%term%</strong>";
		}
		var reg = new RegExp("[ ]+", "g");
		text = normalize(text);
		term = normalize(term);
		var words_text = text.split(reg);
		var words_term = term.split(reg);
		var text_final = text;
		for(wterm in words_term) {
			var matcher = new RegExp(normalize(words_term[wterm]), "i" );
			var find = false;
			for(wtext in words_text) {
				text_current = words_text[wtext];
				text_final = text_final.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" +
					words_term[wterm] +
					")(?![^<>]*>)(?![^&;]+;)", "i"
				), hightlight.replace("%term%", "$1"));
				if(matcher.test(text_current)) {
					find = true;
					delete words_text[wtext];
					break;
				}


				//delete words_text[wtext];
			}
			if(!find) {
				return false;
			}
		}

		for(wtext in words_text) {
			//text_final = text_final + " " + words_text[wtext];
		}

		return text_final;
	};

	$.widget("ui.combobox", {
		_create: function() {
			var self = this,
			select = this.element.hide(),
			selected = select.find("option:selected");
			
			value = selected.text() ? selected.text() : "";
			
			var newValueAllowed  = select.hasClass('permissif');

			if(newValueAllowed) {
				var newValueOption = $('<option class="new_value" value=""></option>');
				select.append(newValueOption);
			}

			var url_ajax = select.attr('data-ajax');
			var limit = 100;
			//var prev_term = "";
			var minLength = 0;
			var delay = (url_ajax) ? 500 : 200;
			
			var input = this.input = $("<input type='text'>")
			.insertAfter(select)
			.val(value)
			.autocomplete({
				delay: delay,
				minLength: minLength,
				source: function(request, response) {
					// prev_term_matcher = new RegExp("^"+prev_term);
					var new_url_ajax = select.attr('data-ajax');
					if (new_url_ajax != url_ajax) {
						url_ajax = new_url_ajax;
						// prev_term = "";
					}

					if(url_ajax) {
						
						//prev_term = request.term;
						$.getJSON(url_ajax, {q:request.term,limit:limit}, function(data) {
							/*if (prev_term != request.term) {
								return ;
							}*/
							var inner_select = '';
							if(newValueAllowed) {
								inner_select += '<option value="'+request.term+'"></option>';
							}
							for(hash in data) {
								inner_select += '<option value="'+hash+'">'+data[hash]+'</option>';
							}
							select.html(inner_select);
							response(select.find("option").map(function() {
								var text = $(this).text();
								var text_highlight = search(text, request.term);
								if(this.value && (!request.term || text_highlight != false))
									return {
										label: text_highlight,
										value: text,
										option: this
									};
							}));

							$(input).parent().find('button').button("option", "disabled", select.children("option").length > limit);
						});

						return;
					}


					response(select.find("option").map(function() {
						var text = $(this).text();
						var text_highlight = search(text, request.term);
						if(this.value && (!request.term || text_highlight != false))
							return {
								label: text_highlight,
								value: text,
								option: this
							};
						}));
				},
				select: function(event, ui) {
					ui.item.option.selected = true;
					self._trigger("selected", event, {
						item: ui.item.option
					});
					$(this).val(ui.item.value.replace(new RegExp("[ ]*\\(.+\\)[ ]*"), " "));
					return false;
				},
				change: function(event, ui) {
					if(!ui.item) {
						var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
						valid = false;
						select.children("option").each(function() {
							if ($(this).text().match(matcher)) {
								this.selected = valid = true;
								return false;
							}
						});
						if(!valid) {
							
							select.val('');
							
							// remove invalid value, as it didn't match anything
							if(newValueAllowed)
							{
								var newValue = $(this).val();
								select.find('option').removeAttr('selected');
								newValueOption.attr('selected','selected').val(newValue).text(newValue);
							}
							else
							{
								$(this).val("");
								input.data("autocomplete").term = "";
							}
							return false;
						}
					}
				}
			});

			//.addClass( "ui-widget ui-widget-content ui-corner-left" );

			input.data("autocomplete")._renderItem = function(ul, item) {
				optgroup_class = $(item.option).parent('optgroup').attr('class');
				return $("<li></li>")
				.addClass(optgroup_class)
				.data("item.autocomplete", item)
				.append("<a>" + item.label.replace('(', '<span style="font-size: 10px; color: #aaa" class="code">').replace(')', '</span>') + "</a>")
				.appendTo(ul);
			};

			// Surcharge de la réponse pour pouvoir afficher quand même le bouton d'ajout même quand il n'y a pas de résultats
			input.data('autocomplete').__response = function(content) {

				if(!this.options.disabled && content && content.length) {
					content = this._normalize(content);
					this._suggest(content);
				}else {
					this._suggest('');
				}

				this._trigger("open");
			};

			// Si le select a la classe combobox, on ajoute le bouton pour afficher toute la liste
			if(select.hasClass('combobox'))
			{
				this.button = $("<button type='button'></button>")
				.attr("tabIndex", -1)
				.attr("title", "Voir toute la liste")
				.insertAfter(input)
				.button({
	            	icons: {
	              		primary: "ui-icon-triangle-1-s"
	            	},
	            	text: false
	          	})
	          	.removeClass("ui-corner-all")
	          	.addClass("custom-combobox-toggle ui-corner-right")
				.click(function() 
				{
					// close if already visible
					if(input.autocomplete("widget").is(":visible")) {
						input.autocomplete("close");
						return;
					}

					// work around a bug (likely same cause as #5265)
					$(this).blur();

					// pass empty string as value to search for, displaying all results
					input.autocomplete("search", "");
					input.focus();
				});

				input.on('autocompleteopen', function()
				{	
                                        console.log($(this).data('url')); 
					var autocomplete_courant = $(this).data("autocomplete").menu.element;

					if(!autocomplete_courant.find('.btn_ajout_autocomplete').length) {

						autocomplete_courant.append(
							'<li class="btn_ajout_autocomplete"><a href="'+select.data('url')+'">' 
							+ select.data('btn-ajout-txt') + 
							'</a></li>'
						);
					}

				});
			}
			
			$(input).parent().find('button').button("option", "disabled", url_ajax && (select.children("option").length == 1 || select.children("option").length > limit));
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call(this);
		}
	});

})(jQuery);