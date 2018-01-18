// fix required for IE < 11
if ($("<input />").prop("required") === undefined) {
	$(document).on("submit", function(e) {
	$(this)
		.find("input, select, textarea")
		.filter("[required]")
		.filter(function() { return this.value == ''; })
		.each(function() {
			e.preventDefault();
			$(this).css({ "border-color":"red" });
			alert( $(this).prev('label').html() + " est un champ obligatoire.");
		});
	});
}