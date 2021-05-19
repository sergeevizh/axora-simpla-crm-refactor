<?php

namespace App\Controllers;

use App\Api\Delivery;
use stdClass;

class CartController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Если передан id варианта, добавим его в корзину
        if ($variant_id = $this->request->get('variant', 'integer')) {
            $this->cart->add_item($variant_id, $this->request->get('amount', 'integer'));
            header('Location: ' . $this->config->root_url . '/cart', true, 302);
            exit();
        }

        // очищаем корзину полностью
        if (isset($_GET['delete_variant']) &&  $_GET['delete_variant'] == 'all') {
            $this->cart->empty_cart();
            if (!isset($_POST['submit_order']) || $_POST['submit_order'] != 1) {
                header('Location: ' . $this->config->root_url . '/cart', true, 302);
                exit();
            }
        }

        // Удаление товара из корзины
        if ($delete_variant_id = intval($this->request->get('delete_variant'))) {
            $this->cart->delete_item($delete_variant_id);
            if (!isset($_POST['submit_order']) || $_POST['submit_order'] != 1) {
                header('Location: ' . $this->config->root_url . '/cart', true, 302);
                exit();
            }
        }

        // Если нажали оформить заказ
        if (isset($_POST['checkout'])) {
            $order = new stdClass();
            $order->delivery_id = $this->request->post('delivery_id', 'integer');
            $order->name = $this->request->post('name');
            $order->email = $this->request->post('email');
            $order->address = $this->request->post('address');
            $order->phone = $this->request->post('phone');
            $order->comment = $this->request->post('comment');
            $order->payment_method_id = $this->request->post('payment_method_id');
            $order->ip = $_SERVER['REMOTE_ADDR'];
            $this->design->assign('delivery_id', $order->delivery_id);
            $this->design->assign('name', $order->name);
            $this->design->assign('email', $order->email);
            $this->design->assign('phone', $order->phone);
            $this->design->assign('address', $order->address);
            $this->design->assign('payment_method_id', $order->payment_method_id);

//            $captcha_code = $this->request->post('captcha_code', 'string');

            // Скидка
            $cart = $this->cart->get_cart();

            $order->discount = $cart->discount;
            if ($cart->coupon) {
                $order->coupon_discount = $cart->coupon_discount;
                $order->coupon_code = $cart->coupon->code;
            }

            if (!empty($this->user->id)) {
                $order->user_id = $this->user->id;
            }

            if (empty($order->name)) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($order->email)) {
                $this->design->assign('error', 'empty_email');
            } else {
                // Добавляем заказ в базу

                $order_id = $this->orders->add_order($order);

                $_SESSION['order_id'] = $order_id;

                // Если использовали купон, увеличим количество его использований
                if ($cart->coupon) {
                    $this->coupons->update_coupon($cart->coupon->id, ['usages' => $cart->coupon->usages + 1]);
                }

                // Добавляем товары к заказу
                foreach ($this->request->post('amounts') as $variant_id => $amount) {
                    $this->orders->add_purchase(['order_id' => $order_id, 'variant_id' => intval($variant_id), 'amount' => intval($amount)]);
                }

                $order = $this->orders->get_order($order_id);

                // Стоимость доставки
                $delivery = $this->delivery->get_delivery($order->delivery_id);

                if (!empty($delivery) && $delivery->free_from > $order->total_price) {
                    $this->orders->update_order($order->id, [
                        'delivery_price' => $delivery->price,
                        'separate_delivery' => $cart->total_price > $delivery->free_from ? Delivery::FREE_DELIVERY : Delivery::PAID_DELIVERY,
                    ]);
                }

                // Отправляем письмо пользователю
                $this->notify->email_order_user($order->id);

                // Отправляем письмо администратору
                $this->notify->email_order_admin($order->id);

                // Очищаем корзину (сессию)
                $this->cart->empty_cart();

                // Перенаправляем на страницу заказа
                header('Location: ' . $this->config->root_url . '/order/' . $order->url, true, 302);
                exit();
            }
        } else {

            // Если нам запостили amounts, обновляем их
            if ($amounts = $this->request->post('amounts')) {
                foreach ($amounts as $variant_id => $amount) {
                    $this->cart->update_item($variant_id, $amount);
                }

                $coupon_code = trim($this->request->post('coupon_code', 'string'));
                if (empty($coupon_code)) {
                    $this->cart->apply_coupon('');
                    header('Location: ' . $this->config->root_url . '/cart', true, 302);
                    exit();
                }
                $coupon = $this->coupons->get_coupon((string)$coupon_code);

                if (empty($coupon) || !$coupon->valid) {
                    $this->cart->apply_coupon($coupon_code);
                    $this->design->assign('coupon_error', 'invalid');
                } else {
                    $this->cart->apply_coupon($coupon_code);
                    header('Location: ' . $this->config->root_url . '/cart', true, 302);
                    exit();
                }
            }
        }
    }

    public function fetch()
    {
        // Варианты оплаты
        $payment_methods = $this->payment->get_payment_methods(['enabled' => 1]);
        $this->design->assign('payment_methods', $payment_methods);
        // Данные пользователя
        if ($this->user) {
            $last_order = $this->orders->get_orders(['user_id' => $this->user->id, 'limit' => 1]);
            $last_order = reset($last_order);
            if ($last_order) {
                $this->design->assign('name', $last_order->name);
                $this->design->assign('email', $last_order->email);
                $this->design->assign('phone', $last_order->phone);
                $this->design->assign('address', $last_order->address);
            } else {
                $this->design->assign('name', $this->user->name);
                $this->design->assign('email', $this->user->email);
                $this->design->assign('phone', $this->user->phone);
                $this->design->assign('address', $this->user->address);
            }
        }

        if (isset($_COOKIE['order_name'])) {
            $this->design->assign('name', $_COOKIE['order_name']);
        }
        if (isset($_COOKIE['order_email'])) {
            $this->design->assign('email', $_COOKIE['order_email']);
        }
        if (isset($_COOKIE['order_phone'])) {
            $this->design->assign('phone', $_COOKIE['order_phone']);
        }
        if (isset($_COOKIE['order_address'])) {
            $this->design->assign('address', $_COOKIE['order_address']);
        }
        if (isset($_COOKIE['order_comment'])) {
            $this->design->assign('comment', $_COOKIE['order_comment']);
        }

        // Если существуют валидные купоны, нужно вывести инпут для купона
        if ($this->coupons->count_coupons(['valid' => 1]) > 0) {
            $this->design->assign('coupon_request', true);
        }

        // Выводим корзину
        return $this->design->fetch('cart.tpl');
    }
}
