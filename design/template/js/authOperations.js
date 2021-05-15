$(document).ready(function () {

    let routeSuffix = window.location.origin + '/ajax/auth/'

    let routes = {
        login: routeSuffix + 'login',
        register: routeSuffix + 'register',
        passwordRecovery: routeSuffix + 'password-recovery',
    };

    /**
     * Авторизация
     */
    $(document).on('click', '.js-popup-open-login', function (e) {
        e.preventDefault();
        $.fancybox.close();
        $.fancybox.open({
            src: $(this).attr('href') || $(this).data('src'),
            type: $(this).data('type') || 'inline',
            opts: {
                afterShow: function (instance, current) {
                    $(current.$slide).find('.js-validation-form-login').each(function () {
                        $(this).validate({
                            errorPlacement: function (error, element) {
                                if ($(element).is(':checkbox') || $(element).is(':radio')) {
                                    error.insertAfter($(element).closest('label'));
                                } else {
                                    error.insertAfter(element);
                                }
                            },
                            submitHandler: function (form) {
                                $.ajax({
                                    url: routes.login,
                                    method: 'post',
                                    data: formDataToObj($(form)),
                                    dataType: 'json',
                                    success: function (data) {
                                        window.location.href = data.redirectUrl;
                                    },
                                    error: function (request, status, error) {
                                        $('#login-error').html(request.responseJSON.errors);
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
     * Восстановления пароля пользователя
     */
    $(document).on('click', '.js-popup-open-recovery', function (e) {
        e.preventDefault();
        $.fancybox.close();
        $.fancybox.open({
            src: $(this).attr('href') || $(this).data('src'),
            type: $(this).data('type') || 'inline',
            opts: {
                afterShow: function (instance, current) {
                    $(current.$slide).find('.js-validation-recovery-form').each(function () {
                        $(this).validate({
                            errorPlacement: function (error, element) {
                                if ($(element).is(':checkbox') || $(element).is(':radio')) {
                                    error.insertAfter($(element).closest('label'));
                                } else {
                                    error.insertAfter(element);
                                }
                            },
                            submitHandler: function (form) {
                                $.ajax({
                                    url: routes.passwordRecovery,
                                    method: 'post',
                                    data: formDataToObj($(form)),
                                    dataType: 'json',
                                    success: function (data) {
                                            $('#recovery-error').text('');
                                            $('#recovery-success').html(data.response.success);
                                            $('.js-validation-recovery-form').find('[name=email]').prop('disabled', true);
                                    },
                                    error: function (request, status, error) {
                                        $('#recovery-error').html(request.responseJSON.response.error);                                    }
                                });
                            }
                        });
                    });
                }
            }
        });
    });

    /**
     * Регистрация
     */
    $(document).on('click', '.js-popup-open-register', function (e) {
        e.preventDefault();
        $.fancybox.close();
        $.fancybox.open({
            src: $(this).attr('href') || $(this).data('src'),
            type: $(this).data('type') || 'inline',
            opts: {
                afterShow: function (instance, current) {
                    $(current.$slide).find('.js-validation-form-register').each(function () {
                        $(this).validate({
                            errorPlacement: function (error, element) {
                                if ($(element).is(':checkbox') || $(element).is(':radio')) {
                                    error.insertAfter($(element).closest('label'));
                                } else {
                                    error.insertAfter(element);
                                }
                            },
                            submitHandler: function (form) {
                                $.ajax({
                                    url: routes.register,
                                    method: 'post',
                                    data: formDataToObj($(form)),
                                    dataType: 'json',
                                    success: function (data) {
                                        window.location.href = data.redirectUrl;
                                    },
                                    error: function (request, status, error) {
                                        $('#register-error').html(request.responseJSON.errors);
                                    }
                                });
                            }
                        });
                    });
                }
            }
        });
    });
});
