<?php

namespace App\Ajax;

use App\Api\Axora;

class Favorites extends Axora implements IAjaxRequest
{
    public function boot()
    {
        $productId = $this->request->get('product_id', 'integer');
        $favoritesCount = 0;

        if (!$productId) {
            return $favoritesCount;
        }

        if (empty($_COOKIE['favorites'])) {
            $this->addToCookie([$productId]);
            $favoritesCount++;
        } else {
            $favorites = explode(',', $_COOKIE['favorites']);

            switch ($_GET['action']) {
                case 'add':
                    $favorites[] = $productId;
                    $this->addToCookie($favorites);

                    break;
                case 'remove':
                    unset($favorites[array_search($productId, $favorites)]);
                    $this->addToCookie($favorites);

                    break;
            }
            $favoritesCount = count($favorites);
        }

        return $favoritesCount;
    }

    private function addToCookie(array $favorites): void
    {
        $expire = time() + 60 * 60 * 24 * 30;
        setcookie('favorites', implode(',', $favorites), $expire, '/');
    }
}
