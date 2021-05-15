<?php

namespace App\Ajax;

use App\Api\Axora;

class AddRating extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $rating = $this->request->post('rating');
        $userId = $this->request->post('user_id');
        $productId = $this->request->post('product_id');
        $avgRating = 0;

        if ( $rating && $productId && $userId ) {

            if ( $this->rating->getRating($productId,$userId)) {
                $this->rating->updateRating($rating,$productId,$userId);
                $msg = ['status' => 1, 'text' => 'Рейтинг обновлен'];
            } else {
                $this->rating->createRating($rating,$productId,$userId);
                $msg = ['status' => 2, 'text' => 'Рейтинг добавлен'];
            }

            $avgRating = $this->rating->calculateRating($productId);

        } else {

            if (!$userId) {
                $msg = ['status' => 3, 'text' => 'Для голосования необходимо зарегистрироваться'];
            } else {
                $msg = ['status' => 4, 'text' => 'Не все данные получены, необходимо отправить rating,product_id,user_id'];
            }
        }

        return ['msg' => $msg, 'avg_rating' => $avgRating];
    }
}
