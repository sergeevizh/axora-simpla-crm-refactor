function formDataToObj($form) {
	let arr = $form.serializeArray();
	let obj = {};

	$.map(arr, function (n, i) {
		obj[n['name']] = n['value'];
	})

	return obj;
}

function validateForm($form) {
	$form.each(function () {
		$(this).validate({
			errorPlacement: function (error, element) {
				if ($(element).is(':checkbox') || $(element).is(':radio')) {
					error.insertAfter($(element).closest('label'));
				} else {
					error.insertAfter(element);
				}
			}
		});
	});
}
