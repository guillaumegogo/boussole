jQuery.fn.filterByText = function(textbox, selectSingleMatch, minimumTextValue)  {
	return this.each(function() {
		var select = this;
		var options = [];
		if(typeof selectSingleMatch === "undefined")  {   selectSingleMatch = true;   }
		if(typeof minimumTextValue === "undefined")  {   minimumTextValue = 1;  }

		$(select).find('option').each(function() {
			options.push({value: $(this).val(), text: $(this).text()});
		});
		$(select).data('options', options);
		$(textbox).bind('change keyup', function() {
			if(textbox.val().length > minimumTextValue) {
				var options = $(select).empty().scrollTop(0).data('options');
				var search = $.trim($(this).val());
				var regex = new RegExp("\\b" + search,"gi");
				$.each(options, function(i) {
					var option = options[i];
					if(option.text.match(regex) !== null) {
						$(select).append(
						   $('<option>').text(option.text).val(option.value)
						);
					}
				});
			}
			if (selectSingleMatch === true && $(select).children().length === 1) {
				$(select).children().get(0).selected = true;
			}
		});          
	});
};