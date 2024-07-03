jQuery(document).ready(function () {
	if (jQuery(".wpp_date").length === 0) {
		console.log("custom_date not found");
		return;
	}

	jQuery(".wpp_date").datetimepicker();

	if (jQuery("#wpp_checkbox_2").is(":checked")) {
		jQuery(".wpp_date").show();
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
