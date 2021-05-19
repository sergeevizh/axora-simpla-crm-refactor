<?php

namespace App\Ajax;

use App\Api\Axora;

class Compare extends Axora implements IAjaxRequest
{
    public function boot()
    {
        if ($this->request->method('post')) {
            $product_id = $this->request->post('product_id', 'int');

            if (!empty($product_id)) {
                $compares = $this->compares->gets();
                if (isset($compares) && in_array($product_id, $compares)) {
                    $this->compares->delete($product_id);
                } else {
                    $this->compares->add($product_id);
                }
            }

            $this->design->assign('compares', $this->compares->gets());

            return $this->design->fetch('compare_informer.tpl');
        }

        return 0;
    }
}
