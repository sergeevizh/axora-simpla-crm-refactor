<?php

namespace App\Controllers;


class SearchController extends Controller
{
    public function fetch()
    {
        $filter = array();
        $filter['visible'] = 1;

        // Если задано ключевое слово
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $this->design->assign('keyword', $keyword);
            $filter['keyword'] = $keyword;
        }



        if(!empty($filter['keyword'])){

            $kw = $this->db->escape($keyword);

            $this->db->query("SELECT c.id, c.name, c.url, 'category' AS type, CONCAT('catalog/', c.url) AS url
                                    FROM __categories AS c
                                    WHERE (c.name LIKE '%$kw%' OR c.meta_keywords LIKE '%$kw%')
                                    AND c.visible = 1");
            $search_categories = $this->db->results();

            $this->design->assign('search_categories', $search_categories);

            $this->db->query("SELECT b.id, b.name, 'brand' AS type, CONCAT('brands/', b.url) AS url
                                    FROM __brands AS b
                                    WHERE (b.name LIKE '%$kw%' OR b.meta_keywords LIKE '%$kw%')");
            $search_brands = $this->db->results();
            $this->design->assign('search_brands', $search_brands);


            $items_per_page = $this->settings->products_num;
            // Текущая страница в постраничном выводе
            $current_page = $this->request->get('page', 'integer');
            // Если не задана, то равна 1
            $current_page = max(1, $current_page);
            $this->design->assign('current_page_num', $current_page);
            // Вычисляем количество страниц
            $products_count = $this->products->count_products($filter);


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
            $this->design->assign('products', $products);

        }

        return $this->design->fetch('search.tpl');
    }
}



