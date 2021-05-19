<?php

namespace App\Ajax;

use App\Api\Axora;

class UserLogin extends Axora implements IAjaxRequest
{
    public function boot()
    {
        if ($this->request->method('post')) {
            $email = $this->request->post('email');
            $password = $this->request->post('password');

            $userId = $this->users->check_password($email, $password);

            if (!$userId) {
                return validateError('Неправильный логин или пароль');
            }

            $user = $this->users->get_user($email);

            if (!$user->enabled) {
                return validateError('Пользователь отключен');
            }

            $_SESSION['user_id'] = $userId;

            $this->users->update_user($userId, ['last_ip' => $_SERVER['REMOTE_ADDR']]);

            $redirect_url = $this->config->root_url;

            if (!empty($_SESSION['last_visited_page'])) {
                $redirect_url = $_SESSION['last_visited_page'];
            }

            return ['redirect_url' => $redirect_url];
        }

        return ['error' => 'Incorrect http method'];
    }
}
