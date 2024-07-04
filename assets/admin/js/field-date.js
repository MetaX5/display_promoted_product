jQuery(document).ready(function () {
	if (jQuery(".wpp_date").length === 0) {
		return;
	}

	jQuery(".wpp_date").datetimepicker().attr("autocomplete", "off");

	if (jQuery("#wpp_checkbox_2").is(":checked")) {
		jQuery(".wpp_date").show();
		jQuery(".wpp_date");
	} else {
		jQuery(".wpp_date").hide();
	}

	jQuery("#wpp_checkbox_2").on("change", function () {
		if (jQuery(this).is(":checked")) {
			jQuery(".wpp_date").show();
		} else {
			jQuery(".wpp_date").hide();
		}
	});
});
