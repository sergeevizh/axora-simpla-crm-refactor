<?php

namespace App\Api;

use stdClass;

class Cart extends Axora
{
    /**
     * Функция возвращает корзину
     *
     * @return stdClass
     */
    public function get_cart()
    {
        $cart = new stdClass();
        $cart->purchases = [];
        $cart->total_price = 0;
        $cart->total_products = 0;
        $cart->coupon = null;
        $cart->discount = 0;
        $cart->coupon_discount = 0;

        // Берем из сессии список variant_id=>amount
        if (!empty($_SESSION['shopping_cart'])) {
            $session_items = $_SESSION['shopping_cart'];

            $variants = $this->variants->get_variants(['id'=>array_keys($session_items)]);
            if (!empty($variants)) {
                $items = [];
                $products_ids = [];
                foreach ($variants as $variant) {
                    $items[$variant->id] = new stdClass();
                    $items[$variant->id]->variant = $variant;
                    $items[$variant->id]->amount = $session_items[$variant->id];
                    $products_ids[] = $variant->product_id;
                }

                $products = $this->products->get_products_compile(['id'=>$products_ids, 'limit' => count($products_ids)]);

                foreach ($items as $variant_id=>$item) {
                    $purchase = null;
                    if (!empty($products[$item->variant->product_id])) {
                        $purchase = new stdClass();
                        $purchase->product = $products[$item->variant->product_id];
                        $purchase->variant = $item->variant;
                        $purchase->amount = $item->amount;

                        $cart->purchases[] = $purchase;
                        $cart->total_price += $item->variant->price*$item->amount;
                        $cart->total_products += $item->amount;
                    }
                }

                // Пользовательская скидка
                $cart->discount = 0;
                if (isset($_SESSION['user_id']) && $user = $this->users->get_user(intval($_SESSION['user_id']))) {
                    $cart->discount = $user->discount;
                }

                $cart->total_price *= (100-$cart->discount)/100;

                // Скидка по купону
                if (isset($_SESSION['coupon_code'])) {
                    $cart->coupon = $this->coupons->get_coupon($_SESSION['coupon_code']);
                    if ($cart->coupon && $cart->coupon->valid && $cart->total_price>=$cart->coupon->min_order_price) {
                        if ($cart->coupon->type=='absolute') {
                            // Абсолютная скидка не более суммы заказа
                            $cart->coupon_discount = $cart->total_price>$cart->coupon->value ? $cart->coupon->value : $cart->total_price;
                            $cart->total_price = max(0, $cart->total_price-$cart->coupon->value);
                        } else {
                            $cart->coupon_discount = $cart->total_price * ($cart->coupon->value)/100;
                            $cart->total_price = $cart->total_price-$cart->coupon_discount;
                        }
                    } else {
                        unset($_SESSION['coupon_code']);
                    }
                }
            }
        }

        // Способы доставки
        $deliveries = $this->delivery->get_deliveries(['enabled' => 1]);
        //  получаем выбранную доставку или берем первую
        if (isset($_COOKIE['delivery_id'])) {
            $cart->сurrent_delivery =  $this->delivery->get_delivery($_COOKIE['delivery_id']);
        } else {
            $cart->сurrent_delivery = $deliveries[0];
            $expire = time() + 60 * 60 * 24;
            setcookie('delivery_id', $deliveries[0]->id, $expire, '/');
        }

        $cart = $this->calculateCartTotalPriceIncludingDelivery($cart);

        return $cart;
    }

    /**
     *   просчитываем общую сумму с учетом доствки
     *
     * @param $cart
     * @param $delivery
     *
     * @return mixed
     */
    public function calculateCartTotalPriceIncludingDelivery($cart)
    {
        //  просчитываем общую сумму с учетом доствки
        if ($cart->сurrent_delivery) {
            if ($cart->total_price <  $cart->сurrent_delivery->free_from) {
                $cart->total_price_without_delivery = $cart->total_price;
                $cart->total_price +=  $cart->сurrent_delivery->price;
                $cart->delivery_price =  $cart->сurrent_delivery->price;
            }
        }

        return $cart;
    }

    /**
     * Добавление варианта товара в корзину
     *
     * @param $variant_id
     * @param int $amount
     */
    public function add_item($variant_id, $amount = 1)
    {
        $amount = max(1, $amount);

        if (isset($_SESSION['shopping_cart'][$variant_id])) {
            $amount = max(1, $amount+$_SESSION['shopping_cart'][$variant_id]);
        }

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = $this->variants->get_variant($variant_id);

        // Если товар существует, добавим его в корзину
        if (!empty($variant) && ($variant->stock>0)) {
            // Не дадим больше чем на складе
            $amount = min($amount, $variant->stock);

            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
        }
    }

    /**
     * Обновление количества товара
     *
     * @param $variant_id
     * @param int $amount
     */
    public function update_item($variant_id, $amount = 1)
    {
        $amount = max(1, $amount);

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = $this->variants->get_variant($variant_id);

        // Если товар существует, добавим его в корзину
        if (!empty($variant) && $variant->stock>0) {
            // Не дадим больше чем на складе
            $amount = min($amount, $variant->stock);

            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
        }
    }

    /**
     * Удаление товара из корзины
     *
     * @param $variant_id
     */
    public function delete_item($variant_id)
    {
        unset($_SESSION['shopping_cart'][$variant_id]);
    }

    /**
     * Очистка корзины
     */
    public function empty_cart()
    {
        unset($_SESSION['shopping_cart']);
        unset($_SESSION['coupon_code']);
    }

    /**
     * Применить купон
     *
     * @param $coupon_code
     */
    public function apply_coupon($coupon_code)
    {
        $coupon = $this->coupons->get_coupon((string)$coupon_code);
        if ($coupon && $coupon->valid) {
            $_SESSION['coupon_code'] = $coupon->code;
        } else {
            unset($_SESSION['coupon_code']);
        }
    }
}
