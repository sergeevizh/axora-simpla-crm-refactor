<?php

namespace App\Ajax;

use App\Api\Axora;

class Register extends Axora implements IAjaxRequest
{
    public function boot()
    {
        if ($this->request->method('post')) {

            $name = $this->request->post('name');
            $email = $this->request->post('email');
            $password = $this->request->post('password');

            if ($this->users->exist($email)) {
                return validateError('Пользователь с таким E-mail существует');
            }

            if (empty($name)) {
                return validateError('Поле Имя не должно быть пустым');
            }

            if (empty($email)) {
                return validateError('Поле E-mail не должно быть пустым');
            }

            if (empty($password)) {
                return validateError('Поле Пароль не должно быть пустым');
            }

            $userId = $this->users->add_user([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'enabled' => 1,
                'last_ip' => $_SERVER['REMOTE_ADDR'],
            ]);

            if (!$userId) {
                return validateError('При регистрации произошла ошибка');
            }

            $_SESSION['user_id'] = $userId;
            $redirectUrl = $_SESSION['last_visited_page'] ?? $this->config->root_url;

            return ['redirect_url' => $redirectUrl];
        }

        return validateError('Недопустимый http метод');
    }
}
