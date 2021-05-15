<?php

namespace App\Ajax;

use App\Api\Axora;

class Cart extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $this->cart->add_item($this->request->get('variant', 'integer'), $this->request->get('amount', 'integer'));

        $this->design->assign('cart', $this->cart->get_cart());

        return $this->design->fetch('cart_informer.tpl');
    }
}
