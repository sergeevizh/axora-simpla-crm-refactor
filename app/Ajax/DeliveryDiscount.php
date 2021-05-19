<?php

namespace App\Ajax;

use App\Api\Axora;

class DeliveryDiscount extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $expire = time() + 60 * 60 * 24;

        $accessFields = [
            'delivery_id',
            'name',
            'email',
            'phone',
            'address',
            'comment',
        ];

        foreach ($accessFields as $field) {
            if ($value = $this->request->post($field)) {
                $this->addToCookie($field, $value, $expire);
            }
        }

        return $this->request->post('delivery_id');
    }

    private function addToCookie($key, $value, $expire): void
    {
        setcookie($key, $value, $expire, '/');
    }
}
