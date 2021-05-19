<?php

namespace App\Api;

class Compares extends Axora
{
    protected $ids = [];
    protected $keySession = 'compares_ids';

    public function __construct()
    {
        if (!empty($_SESSION[$this->keySession])) {
            $this->ids = $_SESSION[$this->keySession];
        }
    }

    public function __destruct()
    {
        $_SESSION[$this->keySession] = $this->ids;
    }

    public function gets()
    {
        return $this->ids;
    }

    public function add($product_id)
    {
        // Выберем товар из базы, заодно убедившись в его существовании
        $product = $this->products->get_product($product_id);

        // Если товар существует, добавим его в корзину
        if (!empty($product)) {
            array_unshift($this->ids, $product->id);
            $this->ids = array_unique($this->ids);

            // товар существует, добавим его в корзину
            $_SESSION[$this->keySession] = $this->ids;
        }

        return false;
    }

    public function delete($product_id)
    {
        if (in_array((int)$product_id, $this->ids, true)) {
            foreach ($this->ids as $k=>$id) {
                if ($id === $product_id) {
                    unset($this->ids[$k]);
                }
            }
        }
        $_SESSION[$this->keySession] = $this->ids;
    }
}
