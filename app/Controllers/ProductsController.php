<?php

namespace App\Controllers;

class ProductsController extends Controller
{

    public function fetch()
    {

        //=--- сео теги
        $tag = $this->design->get_var('tag');

        // GET-Параметры
        $category_url    = $this->request->get('category', 'string');
        $brand_url       = $this->request->get('brand');
        $brand_ids       = $this->request->get('b');

        $filter = array();
        $filter['visible'] = 1;

        // Если задан бренд, выберем его из базы
        if (!empty($brand_url) && !is_array($brand_url)) {

            $brand = $this->brands->get_brand((string)$brand_url);

            if (empty($brand)) {
                return false;
            }
            $this->design->assign('brand', $brand);
            $filter['brand_id'] = $brand->id;
        } elseif (is_array($brand_url)) {
            $filter['brand_id'] = $brand_url;
        }


        if(!empty($brand_ids)){
            $filter['brand_id'] = $brand_ids;
        }
        if(!empty($this->request->get('discounted', 'int'))){
            $filter['discounted'] = 1;
        }
        // Выберем текущую категорию
        if (!empty($category_url)) {
            $category = $this->categories->get_category((string)$category_url);
            if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
                return false;
            }
            $this->design->assign('category', $category);
            $filter['category_id'] = $category->children;
        }

        // Если задано ключевое слово
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $this->design->assign('keyword', $keyword);
            $filter['keyword'] = $keyword;
        }

        if ($type = $this->request->get('type', 'string')) {
            if ($type == 'featured') {
                $filter['featured'] = 1;
            } elseif ($type == 'new') {
                $filter['new'] = 1;
            } elseif ($type == 'actions' || $type == 'discounted') {
                $filter['discounted'] = 1;
            }
            $this->design->assign('type', $type);
        }

        // Сортировка товаров, сохраняем в сесси, чтобы текущая сортировка оставалась для всего сайта
        if ($sort = $this->request->get('sort', 'string')) {
            $_SESSION['sort'] = $sort;
        }
        if (!empty($_SESSION['sort'])) {
            $filter['sort'] = $_SESSION['sort'];
        } else {
            $filter['sort'] = 'position';
        }
        $this->design->assign('sort', $filter['sort']);

        if ($this->request->get('min_price', 'integer')) {
            $filter['min_price'] = $this->request->get('min_price', 'integer') * $this->currency->rate_to / $this->currency->rate_from;
            $this->design->assign('min_price', true);
        }

        if ($this->request->get('max_price')) {
            $filter['max_price'] = $this->request->get('max_price', 'integer') * $this->currency->rate_to / $this->currency->rate_from;
            $this->design->assign('max_price', true);
        }

        // Свойства товаров
        if (!empty($category)) {
            $features = array();
            foreach ($this->features->get_features(array(
                'category_id' => $category->id,
                'in_filter' => 1
            )) as $feature) {
                $features[$feature->id] = $feature;
                if (($val = $this->request->get($feature->id)) != '') {
                    if ($val[0] != '') {
                        $filter['features'][$feature->id] = (array)$val;
                    }

                    $features[$feature->id]->active = true;
                } else {
                    $features[$feature->id]->active = false;
                }
            }

            $options_filter['visible'] = 1;

            $features_ids = array_keys($features);
            if (!empty($features_ids)) {
                $options_filter['feature_id'] = $features_ids;
            }
            $options_filter['category_id'] = $category->children;
            if (isset($filter['features'])) {
                $options_filter['features'] = $filter['features'];
            }
            if (!empty($brand)) {
                $options_filter['brand_id'] = $brand->id;
            }

            $options = $this->features->get_options($options_filter);
            foreach ($options as $option) {

                // в этой групе есть чекнутый фильтер
                if ($features[$option->feature_id]->active) {

                    if (in_array($option->value, $filter['features'][$option->feature_id])) {
                        $option->checked = true;
                        $option->disabled = false;
                        $option->count = '';
                    } else {
                        $temp_filter = $filter;
                        $temp_filter['features'][$option->feature_id] = (array)$option->value;
                        $option->count = '+'.$this->products->count_products($temp_filter);
                        if((int)$option->count > 0){
                            $option->disabled = false;
                        }else{
                            $option->disabled = true;
                            $option->count = '';
                        }

                        unset($temp_filter);
                    }
                } else {
                    $temp_filter = $filter;
                    $temp_filter['features'][$option->feature_id] = (array)$option->value;
                    $option->count = $this->products->count_products($temp_filter);
                    if((int)$option->count > 0)
                        $option->disabled = false;
                    else
                        $option->disabled = true;
                    unset($temp_filter);
                    $option->checked = false;
                }

                $features[$option->feature_id]->options[] = $option;
            }

            foreach ($features as $i => &$feature) {
                if (empty($feature->options)) {
                    unset($features[$i]);
                }
            }

            $this->design->assign('features', $features);


            // $this->design->assign('prices', $this->products->prices_products(array('visible' => 1, 'category_id' => $filter['category_id'])));
        }
        $this->design->assign('filter', $filter);
        // Постраничная навигация
        $items_per_page = $this->request->get('limit') ?: $this->settings->products_num;
        // Текущая страница в постраничном выводе
        $current_page = $this->request->get('page', 'integer');
        // Если не задана, то равна 1
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);
        // Вычисляем количество страниц
        $products_count = $this->products->count_products($filter);

        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $items_per_page = $products_count;
        }

        $pages_num = ceil($products_count/$items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_products_num', $products_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        ///////////////////////////////////////////////
        // Постраничная навигация END
        ///////////////////////////////////////////////

        // Товары
        $products = $this->products->get_products_compile($filter);

        // Если искали товар и найден ровно один - перенаправляем на него
        if (!empty($keyword) && !empty($products) && $products_count == 1) {
            $p = reset($products);
            header('Location: '.$this->config->root_url.'/products/'.$p->url);
            exit();
        }

        $this->design->assign('products', $products);


        // Выбираем бренды, они нужны нам в шаблоне
        if (!empty($category)) {
            $brands = array();
            foreach ($this->brands->get_brands(array('category_id' => $category->children, 'visible' => 1)) as $b) {
                if (!empty($filter['brand_id']) && in_array($b->id, (array)$filter['brand_id'])) {
                    $b->checked = true;
                } else {
                    $b->checked = false;
                }
                $brands[$b->id] = $b;
            }
            $category->brands = $brands;
        } elseif (!empty($type) && !isset($brand)) {

            $results_categories = $this->categories->getCategoriesByProductType($type);
            $this->design->assign('results_categories', $results_categories);
            $this->design->assign('type', $type);

        }



        // Устанавливаем мета-теги в зависимости от запроса
        if (!empty($tag)) {
            $this->design->assign('meta_title', $tag->meta_title);
            $this->design->assign('meta_keywords', $tag->meta_keywords);
            $this->design->assign('meta_description', $tag->meta_description);


        } else if ($this->page) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
        } elseif (isset($category)) {
            $this->design->assign('meta_title', $category->meta_title);
            $this->design->assign('meta_keywords', $category->meta_keywords);
            $this->design->assign('meta_description', $category->meta_description);
        } elseif (isset($brand)) {

            $results_categories = $this->brands->getCategoriesByBrand($brand->id);
            foreach ($results_categories as &$u) {
                $u->url .= '?b[]=' . $u->brand_id;
            }

            $this->design->assign('results_categories', $results_categories);
            $this->design->assign('meta_title', $brand->meta_title);
            $this->design->assign('meta_keywords', $brand->meta_keywords);
            $this->design->assign('meta_description', $brand->meta_description);
        } elseif (isset($keyword)) {
            $this->design->assign('meta_title', $keyword);
        }

        return $this->design->fetch('products.tpl');
    }
}
