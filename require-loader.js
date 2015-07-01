require (['../../js/jquery-1.6.2.min'], function ($) {
	require({
		baseUrl: '../../js/'
	}, [
		"navigation",
		"add",
		"edit"
	], function( 
		nav,
		add,
		edit
	) {
		var jq_selector = "input[name='dateOption']";

		jQuery (jq_selector).bind ("change", function () {
			toggle_options (jQuery (this).val());
		}).is(":checked") ? toggle_options (jQuery (jq_selector).val()) : null;

		function toggle_options (selection) {
			var jq_selector_date = "div.selectTime-date",
			    jq_selector_time = "div.selectTime-time";

			if (selection == "dateOption-selectTime") {
				jQuery (jq_selector_date).show();
				jQuery (jq_selector_time).show();
			} else {
				jQuery (jq_selector_date).hide();
				jQuery (jq_selector_time).hide();
			}
		}
	});
});