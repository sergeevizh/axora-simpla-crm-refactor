<?php

namespace App\Api;

/**
 * Class Axora
 *
 * @property Request $request
 * @property Database $db
 * @property Settings $settings
 * @property Products $products
 * @property Variants $variants
 * @property Categories $categories
 * @property Brands $brands
 * @property Features $features
 * @property Money $money
 * @property Pages $pages
 * @property Blog $blog
 * @property Cart $cart
 * @property Banners $banners
 * @property Image $image
 * @property Delivery $delivery
 * @property Payment $payment
 * @property Orders $orders
 * @property Users $users
 * @property Coupons $coupons
 * @property Comments $comments
 * @property Feedbacks $feedbacks
 * @property Notify $notify
 * @property Managers $managers
 * @property Rating $rating
 * @property Document $document
 * @property Compares $compares
 * @property Tags $tags
 */
class Axora
{
    private $modelsMap = [
        'config' => Config::class,
        'request' => Request::class,
        'db' => Database::class,
        'settings' => Settings::class,
        'products' => Products::class,
        'variants' => Variants::class,
        'categories' => Categories::class,
        'brands' => Brands::class,
        'features' => Features::class,
        'money' => Money::class,
        'pages' => Pages::class,
        'banners' => Banners::class,
        'blog' => Blog::class,
        'cart' => Cart::class,
        'image' => Image::class,
        'delivery' => Delivery::class,
        'payment' => Payment::class,
        'orders' => Orders::class,
        'users' => Users::class,
        'coupons' => Coupons::class,
        'comments' => Comments::class,
        'feedbacks' => Feedbacks::class,
        'notify' => Notify::class,
        'managers' => Managers::class,
        'compares' => Compares::class,
        'rating' => Rating::class,
        'document' => Document::class,
        'tags' => Tags::class,
    ];

    private static $objects = [];

    public function __construct()
    {
    }

    /**
     * Магический метод, создает нужный объект API
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        // Если такой объект уже существует, возвращаем его
        if (isset(self::$objects[$name])) {
            return (self::$objects[$name]);
        }

        // Если запрошенного API не существует - ошибка
        if (!array_key_exists($name, $this->modelsMap)) {
            return null;
        }

        // Определяем имя нужного класса
        $class = $this->modelsMap[$name];

        // Сохраняем для будущих обращений к нему
        self::$objects[$name] = new $class();

        // Возвращаем созданный объект
        return self::$objects[$name];
    }
}
