<?php

namespace App\Controllers;

class ProductController extends Controller
{
    public function fetch()
    {
        $product_url = $this->request->get('product_url', 'string');

        if (empty($product_url)) {
            return false;
        }




        // Выбираем товар из базы
        $product = $this->products->get_product((string)$product_url);
        if (empty($product) || (!$product->visible && empty($_SESSION['admin']))) {
            return false;
        }
        
          $documents = $this->document->get($product->id);
        $this->design->assign('documents',$documents);

        $rating = $this->rating->calculateRating($product->id);
        $this->design->assign('in_product_page_rating',$rating);

        $product->images = $this->products->get_images(array('product_id'=>$product->id));
        $product->image = reset($product->images);

        $variants = array();
        foreach ($this->variants->get_variants(array('product_id'=>$product->id)) as $v) {
            $variants[$v->id] = $v;

        }


        $product->variants = $variants;

        // Вариант по умолчанию
        if (($v_id = $this->request->get('variant', 'integer'))>0 && isset($variants[$v_id])) {
            $product->variant = $variants[$v_id];
        } else {
            $product->variant = reset($variants);
        }

        $product->features = $this->features->get_product_options(array('product_id'=>$product->id));

        // Автозаполнение имени для формы комментария
        if (!empty($this->user)) {
            $this->design->assign('comment_name', $this->user->name);
        }

        // Принимаем комментарий
        if (isset($_POST['comment'])) {
            $comment = new \stdClass();
            $comment->name = $this->request->post('name');
            $comment->text = $this->request->post('text');
            $captcha_code =  $this->request->post('captcha_code', 'string');

            // Передадим комментарий обратно в шаблон - при ошибке нужно будет заполнить форму
            $this->design->assign('comment_text', $comment->text);
            $this->design->assign('comment_name', $comment->name);

            // Проверяем капчу и заполнение формы
            if ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code)) {
                $this->design->assign('error', 'captcha');
            } elseif (empty($comment->name)) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($comment->text)) {
                $this->design->assign('error', 'empty_comment');
            } else {
                // Создаем комментарий
                $comment->object_id = $product->id;
                $comment->type      = 'product';
                $comment->ip        = $_SERVER['REMOTE_ADDR'];

                // Если были одобренные комментарии от текущего ip, одобряем сразу
                $this->db->query("SELECT 1 FROM __comments WHERE approved=1 AND ip=? LIMIT 1", $comment->ip);
                if ($this->db->num_rows()>0) {
                    $comment->approved = 1;
                }

                // Добавляем комментарий в базу
                $comment_id = $this->comments->add_comment($comment);

                // Отправляем email
                $this->notify->email_comment_admin($comment_id);

                // Приберем сохраненную капчу, иначе можно отключить загрузку рисунков и постить старую
                unset($_SESSION['captcha_code']);
                header('location: '.$_SERVER['REQUEST_URI'].'#reviews', true, 302);
                exit();
            }
        }

        // Связанные товары
        $related_ids = array();
        foreach ($this->products->get_related_products($product->id) as $p) {
            $related_ids[] = $p->related_id;
        }
        if (!empty($related_ids)) {
            $products = $this->products->get_products_compile(array('id'=>$related_ids, 'limit' => count($related_ids), 'in_stock'=>1, 'visible'=>1));

            $this->design->assign('related_products', $products);
        }

        // Отзывы о товаре
        $comments = $this->comments->get_comments(array('type'=>'product', 'object_id'=>$product->id, 'approved'=>1, 'ip'=>$_SERVER['REMOTE_ADDR']));

        // Соседние товары
        $this->design->assign('next_product', $this->products->get_next_product($product->id));
        $this->design->assign('prev_product', $this->products->get_prev_product($product->id));

        // И передаем его в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('comments', $comments);

        // Категория и бренд товара
        $product->categories = $this->categories->get_categories(array('product_id'=>$product->id));
        $this->design->assign('brand', $this->brands->get_brand(intval($product->brand_id)));
        $this->design->assign('category', reset($product->categories));

        // Добавление в историю просмотров товаров
        $max_visited_products = 100; // Максимальное число хранимых товаров в истории
        $expire = time()+60*60*24*30; // Время жизни - 30 дней
        if (!empty($_COOKIE['browsed_products'])) {
            $browsed_products = explode(',', $_COOKIE['browsed_products']);
            // Удалим текущий товар, если он был
            if (($exists = array_search($product->id, $browsed_products)) !== false) {
                unset($browsed_products[$exists]);
            }
        }
        // Добавим текущий товар
        $browsed_products[] = $product->id;
        $cookie_val = implode(',', array_slice($browsed_products, -$max_visited_products, $max_visited_products));
        setcookie("browsed_products", $cookie_val, $expire, "/");
        $this->design->assign('meta_title', $product->meta_title);
        $this->design->assign('meta_keywords', $product->meta_keywords);
        $this->design->assign('meta_description', $product->meta_description);
        return $this->design->fetch('product.tpl');
    }
}
