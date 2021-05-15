<?php

namespace App\Ajax;

use App\Api\Axora;
use Exception;

class SearchProducts extends Axora implements IAjaxRequest
{
    private $limit = 10;

    private $result;

    public function boot()
    {
        $this->result = new \stdClass();
        $this->result->suggestions = array();
        $this->result->query = $this->request->get('query', 'string');

        if (!empty($this->result->query)) {

            $kw = $this->db->escape($this->result->query);

            $this->fetchCategories($kw);
            $this->fetchBrands($kw);
            $this->fetchProducts($kw);
        }

        return $this->result;
    }

    private function fetchProducts(string $kw): void
    {
        $this->db->query("SELECT p.id, p.name, i.filename as image, 'product' AS type, v.price, CONCAT('products/', p.url) AS url
                                    FROM __products p
									LEFT JOIN __images i ON i.product_id=p.id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
									LEFT JOIN __variants v ON v.product_id=p.id AND v.position=(SELECT MIN(position) FROM __variants WHERE product_id=p.id LIMIT 1)
									WHERE (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))
									AND p.visible=1
									GROUP BY p.id
									ORDER BY p.name
									LIMIT ?", $this->limit);

        foreach($this->db->results() as $p){
            $products[$p->id] = $p;
        }

        if(!empty($products)){

            $currencies = $this->money->get_currencies(array('enabled'=>1));

            $currency = isset($_SESSION['currency_id'])
                ? $this->money->get_currency($_SESSION['currency_id'])
                : reset($currencies);

            $this->db->query("SELECT IF(c.name_in_type <> '', c.name_in_type, c.name) AS name, IF(c.image <> '', CONCAT(? , c.image), '') AS image, CONCAT('catalog/', c.url) as url, pc.product_id
                                FROM __categories AS c
                                INNER JOIN __products_categories pc ON c.id = pc.category_id AND pc.position=( SELECT MIN(pc2.position)
											                    FROM __products_categories pc2
											                    WHERE pc.product_id = pc2.product_id)
											                    AND pc.product_id IN( ?@ )
											                    GROUP BY pc.product_id", $this->config->categories_images_dir, array_keys($products));

            try {
                foreach ($this->db->results() as $row) {
                    $products[$row->product_id]->category = $row;
                }
            } catch (Exception $e) {
                echo  $e;
            }

            foreach ($products as $product) {
                $suggestion = new \stdClass();

                if (!empty($product->image)) {
                    $product->image = $this->image->resize_image($product->image, 200, 150);
                }
                if ( $product->price > 0) {
                    $product->price = $this->money->convert($product->price) . ' ' .$currency->sign;
                } else {
                    $product->price = '';
                }

                $suggestion->value = $product->name;
                $suggestion->type = $product->type;
                $suggestion->data = $product;
                $this->result->suggestions[] = $suggestion;
            }
        }
    }

    private function fetchBrands(string $kw): void
    {
        $this->db->query("SELECT b.id, b.name, 'brand' AS type, CONCAT('brands/', b.url) AS url
                                    FROM __brands AS b
                                    WHERE (b.name LIKE '%$kw%' OR b.meta_keywords LIKE '%$kw%')");
        $brands = $this->db->results();

        foreach ($brands as $brand) {
            $suggestion = new \stdClass();
            $suggestion->value = $brand->name;
            $suggestion->type = $brand->type;
            $suggestion->data = $brand;
            $this->result->suggestions[] = $suggestion;
        }
    }

    private function fetchCategories(string $kw): void
    {
        $this->db->query("SELECT c.id, c.name, c.url, 'category' AS type, CONCAT('catalog/', c.url) AS url
                                    FROM __categories AS c
                                    WHERE (c.name LIKE '%$kw%' OR c.meta_keywords LIKE '%$kw%')
                                    AND c.visible = 1");
        $categories = $this->db->results();

        foreach ($categories as $category) {
            $suggestion = new \stdClass();
            $suggestion->value = $category->name;
            $suggestion->type = $category->type;
            $suggestion->data = $category;
            $this->result->suggestions[] = $suggestion;
        }
    }
}
