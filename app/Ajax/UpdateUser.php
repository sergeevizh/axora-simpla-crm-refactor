<?php

namespace App\Ajax;

use App\Api\Axora;

class UpdateUser extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $userId = $this->request->post('user_id');

        if ($_SESSION['user_id'] !== (int)$userId) {
            return;
        }

        $accessFields = [
            'name', 'email', 'phone', 'address'
        ];

        $user = [];

        foreach ($accessFields as $field) {
            if ($value = $this->request->post($field)) {
                $user[$field] = $value;
            }
        }

        $this->users->update_user($userId, $user);
    }
}
