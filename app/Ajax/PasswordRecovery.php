<?php

namespace App\Ajax;

use App\Api\Axora;

class PasswordRecovery extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $email = $this->request->post('email');

        if (!$email) {
            return validateError('E-mail отсутствует');
        }

        $user = $this->users->get_user($email);

        if (empty($user)) {
            return validateError('Пользователь не найден');
        }

        // Генерируем секретный код и сохраняем в сессии
        $code = md5(uniqid($this->config->salt, true));
        $_SESSION['password_remind_code'] = $code;
        $_SESSION['password_remind_user_id'] = $user->id;

        // Отправляем письмо пользователю для восстановления пароля
        $this->notify->email_password_remind($user->id, $code);

        return ['success' => 'Письмо успешно отправлено, проверьте почту'];
    }
}
