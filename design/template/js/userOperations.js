$(document).ready(function () {

	/**
	 * изменения пароля пользователя
	 */
	$(document).on('click', '.js-popup-open-psw-change', function(e){
		e.preventDefault();
		$.fancybox.close();
		$.fancybox.open({
			src  : $(this).attr('href') || $(this).data('src'),
			type : $(this).data('type') || 'inline',
			opts : {
				afterLoad : function(instance, current) {
					$('[name=user_id]').val($('[name=id]').val());
				},
				afterShow : function(instance, current) {
					$(current.$slide).find('.js-validation-form-user-password-edit').each(function() {
						$(this).validate({
							errorPlacement: function(error, element) {
								if ($(element).is(':checkbox') || $(element).is(':radio')) {
									error.insertAfter($(element).closest('label'));
								} else {
									error.insertAfter(element);
								}
							},
							submitHandler: function (form) {
								$.ajax({
									url: "ajax/user_update.php",
									method : 'post',
									data: {
										user_id       : $.trim($('[name=user_id]').val()),
										password     :  $.trim($('[name=password]').val()),

									},
									dataType: 'json',
									success: function(){
										location.reload();
									},
									error: function (request, status, error) {
										console.log(error);
									}
								});
							}
						});
					});
				}
			}
		});
	});

	/**
	 * Добавить отзыв
	 */
	$('.js-feedback_form').each(function() {
		var $form = $(this);
		$form.validate({
			errorPlacement: function(error, element) {
				if ($(element).is(':checkbox') || $(element).is(':radio')) {
					error.insertAfter($(element).closest('label'));
				} else {
					error.insertAfter(element);
				}
			}
		});
	});

	function addAjaxFeedback(data, $btn) {

		$.ajax({
			url: "ajax/user/add-feedback",
			method : 'post',
			data: data,
			dataType: 'json',
			success: function(result){
				$btn.attr("disabled", true);

				if (result.success === true) {
					$btn.text('Сообщение отправлено');
				} else {
					$btn.text('Произошла ошибка отправки');
				}

			},
			error: function (request, status, error) {
				console.log(error);
			}
		});

	}

});
