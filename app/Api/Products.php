<?php

namespace App\Api;

class Products extends Axora
{

    /**
     * Функция возвращает товары
     * Возможные значения фильтра:
     * id - id товара или их массив
     * category_id - id категории или их массив
     * brand_id - id бренда или их массив
     * page - текущая страница, integer
     * limit - количество товаров на странице, integer
     * sort - порядок товаров, возможные значения: position(по умолчанию), name, price
     * keyword - ключевое слово для поиска
     * features - фильтр по свойствам товара, массив (id свойства => значение свойства)
     *
     * @param array $filter
     * @return array|bool
     */
    public function get_products($filter = array())
    {

        // По умолчанию
        $limit = 100;
        $page = 1;
        $category_id_filter = '';
        $brand_id_filter = '';
        $product_id_filter = '';
        $features_filter = '';
        $keyword_filter = '';
        $visible_filter = '';
        $is_featured_filter = '';
        $is_new_filter = '';
        $discounted_filter = '';
        $in_stock_filter = '';
        $min_price = '';
        $max_price = '';
        $group_by = '';
        $order = 'p.position DESC';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

        if (!empty($filter['id'])) {
            $product_id_filter = $this->db->placehold('AND p.id IN(?@)', (array)$filter['id']);
        }

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id IN(?@)', (array)$filter['category_id']);
            // выборка по глявной категории
            if (!empty($filter['is_main_category'])) {
                $category_id_filter .='AND pc.position=( SELECT MIN(pc2.position) 
											                    FROM __products_categories pc2 
											                    WHERE pc.product_id = pc2.product_id)';
            }
            $group_by = "GROUP BY p.id";
        }
        if (!empty($filter['brand_id'])) {
            $brand_id_filter = $this->db->placehold('AND p.brand_id IN(?@)', (array)$filter['brand_id']);
        }

        if (isset($filter['featured'])) {
            $is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));
        }

        if (isset($filter['new'])) {
            $is_new_filter = $this->db->placehold('AND p.new=?', intval($filter['new']));
        }

        //if (isset($filter['discounted'])) {
          //  $discounted_filter = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = ?', intval($filter['discounted']));
        //}
         if (isset($filter['discounted'])) {
            $is_new_filter = $this->db->placehold('AND p.discounted=?', intval($filter['discounted']));
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT COUNT(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['in_stock']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (!empty($filter['sort'])) {
            switch ($filter['sort']) {
                case 'position':
                    $order = 'p.position DESC';
                    break;
                case 'name':
                    $order = 'p.name';
                    break;
                case 'created':
                    $order = 'p.created DESC';
                    break;
                case 'new':
                    $order = 'p.new DESC, p.position DESC';
                    break;
                case 'rand':
                    $order = 'RAND()';
                    break;
                case 'price':
                    //$order = 'pv.price IS NULL, pv.price=0, pv.price';
                    //$order = '(SELECT -pv.price FROM __variants pv WHERE  p.id = pv.product_id  LIMIT 1) DESC';
                    $order = '(SELECT -pv.price FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id AND pv.position=(SELECT MIN(position) FROM __variants WHERE (stock>0 OR stock IS NULL) AND product_id=p.id LIMIT 1) LIMIT 1) DESC';
                    break;
                case 'price_asc':
                    //$order = 'pv.price IS NULL, pv.price=0, pv.price';
                    $order = '(SELECT -pv.price FROM __variants pv  WHERE p.id = pv.product_id LIMIT 1) ASC';
//                     $order = '(SELECT -pv.price FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id AND pv.position=(SELECT MIN(position) FROM __variants WHERE (stock>0 OR stock IS NULL) AND product_id=p.id LIMIT 1) LIMIT 1)';
                    break;
            }
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw!=='') {
                    $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                }
            }
        }

        if (!empty($filter['min_price'])) {
            $min_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>=? LIMIT 1)', intval($filter['min_price']));
        }

        if (!empty($filter['max_price'])) {
            $max_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price<=? LIMIT 1)', intval($filter['max_price']));
        }

        if (!empty($filter['features']) && !empty($filter['features'])) {
            foreach ($filter['features'] as $feature => $value) {
                if (count($value) == 1 && current($value) == 'empty') {
                    $features_filter .= $this->db->placehold('AND ( SELECT count(*)=0 FROM __options o WHERE o.product_id=p.id AND o.feature_id=? LIMIT 1) = 1',
                        $feature);
                } else {
                    $features_filter .= $this->db->placehold('AND p.id in (SELECT product_id FROM __options WHERE feature_id=? AND value IN (?@) ) ',
                        $feature, (array)$value);
                }
            }
        }

        $query = "SELECT
					p.id,
					p.url,
					p.brand_id,
					p.name,
					p.annotation,
					p.body,
					p.position,
                    p.youtube_link,
                    p.discounted,	
					p.created AS created,
					p.visible,
					p.featured,
					p.new,					
					p.meta_title,
					p.meta_keywords,
					p.meta_description,
					b.name AS brand,
					b.url AS brand_url
				FROM __products p
				LEFT JOIN __brands b ON p.brand_id = b.id
				$category_id_filter
				WHERE
					1
					$product_id_filter
					$brand_id_filter
					$features_filter
					$keyword_filter
					$is_featured_filter
					$is_new_filter
					$discounted_filter
					$in_stock_filter        
					$min_price
                    $max_price
					$visible_filter
				$group_by
				ORDER BY $order
					$sql_limit";

        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция возвращает количество товаров
     * Возможные значения фильтра:
     * category_id - id категории или их массив
     * brand_id - id бренда или их массив
     * keyword - ключевое слово для поиска
     * features - фильтр по свойствам товара, массив (id свойства => значение свойства)
     *
     * @param array $filter
     * @return bool|object|string
     */
    public function count_products($filter = array())
    {
        $category_id_filter = '';
        $brand_id_filter = '';
        $product_id_filter = '';
        $keyword_filter = '';
        $visible_filter = '';
        $is_featured_filter = '';
        $is_new_filter = '';
        $in_stock_filter = '';
        $discounted_filter = '';
        $min_price = '';
        $max_price = '';
        $features_filter = '';

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id IN(?@)', (array)$filter['category_id']);
            // выборка по глявной категории
            if (!empty($filter['is_main_category'])) {
                $category_id_filter .='AND pc.position=( SELECT MIN(pc2.position) 
											                    FROM __products_categories pc2 
											                    WHERE pc.product_id = pc2.product_id)';
            }
        }


        if (!empty($filter['brand_id'])) {
            $brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);
        }

        if (!empty($filter['id'])) {
            $product_id_filter = $this->db->placehold('AND p.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw!=='') {
                    $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                }
            }
        }

        if (isset($filter['featured'])) {
            $is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));
        }

        if (isset($filter['new'])) {
            $is_new_filter = $this->db->placehold('AND p.new=?', intval($filter['new']));
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['in_stock']));
        }

        if (isset($filter['discounted'])) {
            $discounted_filter = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = ?', intval($filter['discounted']));
        }
        
        if (isset($filter['discounted'])) {
            $is_new_filter = $this->db->placehold('AND p.discounted=?', intval($filter['discounted']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (!empty($filter['min_price'])) {
            $min_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>=? LIMIT 1)', intval($filter['min_price']));
        }

        if (!empty($filter['max_price'])) {
            $max_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price<=? LIMIT 1)', intval($filter['max_price']));
        }

        if (!empty($filter['features']) && !empty($filter['features'])) {
            foreach ($filter['features'] as $feature => $value) {
                if (count($value) == 1 && current($value) == 'empty') {
                    $features_filter .= $this->db->placehold('AND ( SELECT count(*)=0 FROM __options o WHERE o.product_id=p.id AND o.feature_id=? LIMIT 1) = 1',
                        $feature);
                } else {
                    $features_filter .= $this->db->placehold('AND p.id in (SELECT product_id FROM __options WHERE feature_id=? AND value IN (?@) ) ',
                        $feature, (array)$value);
                }
            }
        }

        $this->db->query("SELECT COUNT(DISTINCT p.id) as count
                            FROM __products AS p
                            $category_id_filter
                            WHERE 1
                                $brand_id_filter
                                $product_id_filter
                                $keyword_filter
                                $is_featured_filter
                                $is_new_filter
                                $in_stock_filter
                                $discounted_filter
                                $min_price
                                $max_price
                                $visible_filter
                                $features_filter");
        return $this->db->result('count');
    }
    /**
     * Функция возвращает товар по id
     *
     * @param  int|string $id
     * @return bool|object
     */
    public function get_product($id)
    {
        if (is_int($id)) {
            $filter = $this->db->placehold('p.id = ?', $id);
        } else {
            $filter = $this->db->placehold('p.url = ?', $id);
        }

        $this->db->query("SELECT p.id,
                                 p.url,
                                 p.brand_id,
                                 p.name,
                                 p.annotation,
                                 p.body,
                                 p.youtube_link,
                                 p.discounted,	
                                 p.position,
                                 p.created as created,
                                 p.visible,
                                 p.new,
                                 p.featured,
                                 p.meta_title,
                                 p.meta_keywords,
                                 p.meta_description
                            FROM __products AS p
                            WHERE $filter
                            LIMIT 1");
        return $this->db->result();
    }

    /**
     * @param  array $filter
     * @return array
     */
    public function get_products_compile($filter = array())
    {
        $products = array();

        foreach ($this->get_products($filter) as $p) {
            $products[$p->id] = $p;
        }
        if (!empty($products)) {
            $products_ids = array_keys($products);
            foreach ($products as &$product) {
                $product->variants = array();
                $product->images = array();
                $product->properties = array();
                $product->type = false;
            }
            $this->db->query("SELECT IF(c.name_in_type <> '', c.name_in_type, c.name) AS name, c.image, c.url, pc.product_id 
                                FROM __categories AS c
                                INNER JOIN __products_categories pc ON c.id = pc.category_id AND pc.position=( SELECT MIN(pc2.position) 
											                    FROM __products_categories pc2 
											                    WHERE pc.product_id = pc2.product_id)
											                    AND pc.product_id IN( ?@ ) 
											                    GROUP BY pc.product_id", $products_ids);
            foreach ($this->db->results() as $row) {
                $products[$row->product_id]->type = $row;
            }

            $variants = $this->variants->get_variants(array('product_id'=>$products_ids));
            foreach ($variants as $variant) {
                $products[$variant->product_id]->variants[$variant->id] = $variant;
            }

            $images = $this->get_images(array('product_id'=>$products_ids));
            foreach ($images as $image) {
                $products[$image->product_id]->images[] = $image;
            }


            $ratings = $this->rating->getRatingsByProduct($products_ids);

            foreach ($ratings as $rating) {

                if (in_array($rating->product_id, $products_ids)) {
                    $products[$rating->product_id]->rating = round($rating->rating);
                }
            }

            foreach ($products as &$product) {
                $product->variant = reset($product->variants);

                if (isset($product->images[0])) {
                    $product->image = $product->images[0];
                }



            }

        }


        return $products;
    }

    /**
     * @param  int $id
     * @param  array|object $product
     * @return int|false
     */
    public function update_product($id, $product)
    {

        $query = $this->db->placehold("UPDATE __products SET ?% WHERE id IN ( ?@ ) LIMIT ?", $product, (array)$id, count((array)$id));
        if ($this->db->query($query)) {
            return $id;
        } else {
            return false;
        }
    }

    public function setNew($id, $value)
    {
        $query = $this->db->placehold("UPDATE __products SET new={$value} WHERE id={$id}");
        if ($this->db->query($query)) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * @param  array|object $product
     * @return int|false
     */
    public function add_product($product)
    {
        $product = (array) $product;

        if (empty($product['url'])) {
            $product['url'] = preg_replace("/[\s]+/ui", '-', $product['name']);
            $product['url'] = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $product['url']));
        }

        // Если есть товар с таким URL, добавляем к нему число
        while ($this->get_product((string)$product['url'])) {
            if (preg_match('/(.+)_([0-9]+)$/', $product['url'], $parts)) {
                $product['url'] = $parts[1] . '_' . ($parts[2] + 1);
                $product['name'] .= '_' . ($parts[2] + 1);
            } else {
                $product['url'] .= '_2';
                $product['name'] .= '_2';
            }
        }
        
         if (empty($product['discounted'])){
            unset($product['discounted']);
        }


        if ($this->db->query("INSERT INTO __products SET ?%", $product)) {
            $id = $this->db->insert_id();
            $this->db->query("UPDATE __products SET position=id WHERE id=?", $id);
            return $id;
        } else {
            return false;
        }
    }


    /**
     * Удалить товар
     *
     * @param  int $id
     * @return bool
     */
    public function delete_product($id)
    {
        if (!empty($id)) {
            // Удаляем варианты
            $variants = $this->variants->get_variants(array('product_id'=>$id));
            foreach ($variants as $v) {
                $this->variants->delete_variant($v->id);
            }

            // Удаляем изображения
            $images = $this->get_images(array('product_id'=>$id));
            foreach ($images as $i) {
                $this->delete_image($i->id);
            }

            // Удаляем категории
            $categories = $this->categories->get_categories(array('product_id'=>$id));
            foreach ($categories as $c) {
                $this->categories->delete_product_category($id, $c->id);
            }

            // Удаляем свойства
            $options = $this->features->get_options(array('product_id'=>$id));
            foreach ($options as $o) {
                $this->features->delete_option($id, $o->feature_id);
            }

            // Удаляем связанные товары
            $related = $this->get_related_products($id);
            foreach ($related as $r) {
                $this->delete_related_product($id, $r->related_id);
            }

            // Удаляем связанные товары
            $related = $this->get_related_products2($id);
            foreach ($related as $r) {
                $this->delete_related_product2($id, $r->related_id);
            }

            // Удаляем товар из связанных с другими
            $query = $this->db->placehold("DELETE FROM __related_products WHERE related_id=?", intval($id));
            $this->db->query($query);

            // Удаляем товар из связанных с другими
            $query = $this->db->placehold("DELETE FROM __related_products2 WHERE related_id=?", intval($id));
            $this->db->query($query);

            // Удаляем отзывы
            $comments = $this->comments->get_comments(array('object_id'=>$id, 'type'=>'product'));
            foreach ($comments as $c) {
                $this->comments->delete_comment($c->id);
            }

            // Удаляем из покупок
            $this->db->query('UPDATE __purchases SET product_id=NULL WHERE product_id=?', intval($id));

            // Удаляем товар
            $query = $this->db->placehold("DELETE FROM __products WHERE id=? LIMIT 1", intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param  int $id
     * @return bool|mixed
     */
    public function duplicate_product($id)
    {
        $product = $this->get_product($id);
        $product->id = null;
        $product->external_id = '';
        $product->visible = 0;
        unset($product->created);


        // Сдвигаем товары вперед и вставляем копию на соседнюю позицию
        $this->db->query('UPDATE __products SET position=position+1 WHERE position>?', $product->position);
        $new_id = $this->products->add_product($product);
        $this->db->query('UPDATE __products SET position=? WHERE id=?', $product->position+1, $new_id);

        // Дублируем категории
        $categories = $this->categories->get_product_categories($id);
        foreach ($categories as $c) {
            $this->categories->add_product_category($new_id, $c->category_id);
        }

        // Дублируем изображения
        $images = $this->get_images(array('product_id'=>$id));
        foreach ($images as $image) {
            $this->add_image($new_id, $image->filename);
        }

        // Дублируем варианты
        $variants = $this->variants->get_variants(array('product_id'=>$id));
        foreach ($variants as $variant) {
            $variant->product_id = $new_id;
            unset($variant->id);
            if ($variant->infinity) {
                $variant->stock = null;
            }
            unset($variant->infinity);
            $variant->external_id = '';
            $this->variants->add_variant($variant);
        }

        // Дублируем свойства
        $options = $this->features->get_options(array('product_id'=>$id));
        foreach ($options as $o) {
            $this->features->update_option($new_id, $o->feature_id, $o->value);
        }

        // Дублируем связанные товары
        $related = $this->get_related_products($id);
        foreach ($related as $r) {
            $this->add_related_product($new_id, $r->related_id);
        }

        // Дублируем связанные товары
        $related2 = $this->get_related_products2($id);
        foreach ($related as $r) {
            $this->add_related_product2($new_id, $r->related_id);
        }


        return $new_id;
    }

    /**
     * @param  array $product_id
     * @return array|bool
     */
    public function get_related_products($product_id = array())
    {
        if (empty($product_id)) {
            return array();
        }

        $product_id_filter = $this->db->placehold('AND product_id in(?@)', (array)$product_id);

        $query = $this->db->placehold("SELECT product_id, 
                                              related_id, 
                                              position
                                        FROM __related_products
                                        WHERE 1
                                            $product_id_filter
                                        ORDER BY position");

        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * @param  array $product_id
     * @return array|bool
     */
    public function get_related_products2($product_id = array())
    {
        if (empty($product_id)) {
            return array();
        }

        $product_id_filter = $this->db->placehold('AND product_id in(?@)', (array)$product_id);

        $query = $this->db->placehold("SELECT product_id, 
                                              related_id, 
                                              position
                                        FROM __related_products2
                                        WHERE 1
                                            $product_id_filter
                                        ORDER BY position");

        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция возвращает связанные товары
     *
     * @param int $product_id
     * @param int $related_id
     * @param int $position
     * @return mixed
     */
    public function add_related_product($product_id, $related_id, $position=0)
    {
        $query = $this->db->placehold("INSERT IGNORE INTO __related_products 
                                            SET product_id=?, 
                                            related_id=?, 
                                            position=?", $product_id, $related_id, $position);
        $this->db->query($query);
        return $related_id;
    }

    /**
     * Функция возвращает связанные товары
     *
     * @param int $product_id
     * @param int $related_id
     * @param int $position
     * @return mixed
     */
    public function add_related_product2($product_id, $related_id, $position=0)
    {
        $query = $this->db->placehold("INSERT IGNORE INTO __related_products2 
                                            SET product_id=?, 
                                            related_id=?, 
                                            position=?", $product_id, $related_id, $position);
        $this->db->query($query);
        return $related_id;
    }

    /**
     * Удаление связанного товара
     *
     * @param int $product_id
     * @param int $related_id
     */
    public function delete_related_product2($product_id, $related_id)
    {
        $query = $this->db->placehold('DELETE FROM __related_products2
                                        WHERE product_id=? 
                                        AND related_id=? 
                                        LIMIT 1', intval($product_id), intval($related_id));
        $this->db->query($query);
    }

    /**
     * Удаление связанного товара
     *
     * @param int $product_id
     * @param int $related_id
     */
    public function delete_related_product($product_id, $related_id)
    {
        $query = $this->db->placehold('DELETE FROM __related_products 
                                        WHERE product_id=? 
                                        AND related_id=? 
                                        LIMIT 1', intval($product_id), intval($related_id));
        $this->db->query($query);
    }

    /**
     * @param  array $filter
     * @return array|bool
     */
    public function get_images($filter = array())
    {
        $product_id_filter = '';
        $group_by = '';

        if (!empty($filter['product_id'])) {
            $product_id_filter = $this->db->placehold('AND i.product_id in(?@)', (array)$filter['product_id']);
        }

        // images
       $this->db->query("SELECT i.id, 
                                i.product_id, 
                                i.name, 
                                i.filename, 
                                i.position
                        FROM __images AS i
                        WHERE 1 
                            $product_id_filter 
                        $group_by 
                        ORDER BY i.product_id, i.position");
        return $this->db->results();
    }

    /**
     * @param  $product_id
     * @param  $filename
     * @return bool|mixed|object|string
     */
    public function add_image($product_id, $filename)
    {
        $query = $this->db->placehold('SELECT id FROM __images WHERE product_id=? AND filename=?', $product_id, $filename);
        $this->db->query($query);
        $id = $this->db->result('id');
        if (empty($id)) {
            $query = $this->db->placehold('INSERT INTO __images SET product_id=?, filename=?', $product_id, $filename);
            $this->db->query($query);
            $id = $this->db->insert_id();
            $query = $this->db->placehold('UPDATE __images SET position=id WHERE id=?', $id);
            $this->db->query($query);
        }
        return($id);
    }

    /**
     * @param $id
     * @param $image
     * @return mixed
     */
    public function update_image($id, $image)
    {
        $query = $this->db->placehold('UPDATE __images SET ?% WHERE id=?', $image, $id);
        $this->db->query($query);

        return $id;
    }

    /**
     * @param $id
     */
    public function delete_image($id)
    {
        $query = $this->db->placehold('SELECT filename FROM __images WHERE id=?', $id);
        $this->db->query($query);
        $filename = $this->db->result('filename');

        $query = $this->db->placehold('DELETE FROM __images WHERE id=? LIMIT 1', $id);
        $this->db->query($query);
        $query = $this->db->placehold('SELECT COUNT(*) as count FROM __images WHERE filename=? LIMIT 1', $filename);
        $this->db->query($query);
        $count = $this->db->result('count');
        if ($count == 0) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            // Удалить все ресайзы
            $rezised_images = glob($this->config->root_dir.$this->config->resized_images_dir.$file.".*x*.".$ext);
            if (is_array($rezised_images)) {
                foreach (glob($this->config->root_dir.$this->config->resized_images_dir.$file.".*x*.".$ext) as $f) {
                    @unlink($f);
                }
            }

            @unlink($this->config->root_dir.$this->config->original_images_dir.$filename);
        }
    }

    /**
     * Следующий товар
     *
     * @param  int $id
     * @return bool|object|string
     */
    public function get_next_product($id)
    {
        $this->db->query('SELECT position 
                            FROM __products 
                            WHERE id = ? 
                            LIMIT 1', $id);
        $position = $this->db->result('position');

        $this->db->query('SELECT pc.category_id 
                            FROM __products_categories pc 
                            WHERE product_id = ? 
                            ORDER BY position 
                            LIMIT 1', $id);
        $category_id = $this->db->result('category_id');

        $query = $this->db->placehold('SELECT id 
                                        FROM __products p, __products_categories pc
										WHERE pc.product_id = p.id 
										AND p.position > ?
										AND pc.position=( SELECT MIN(pc2.position) 
										                    FROM __products_categories pc2 
										                    WHERE pc.product_id = pc2.product_id )
										AND pc.category_id = ?
										AND p.visible 
										ORDER BY p.position 
										LIMIT 1', $position, $category_id);
        $this->db->query($query);

        return $this->get_product((integer)$this->db->result('id'));
    }

    /**
     * Предыдущий товар
     *
     * @param  int $id
     * @return bool|object|string
     */
    public function get_prev_product($id)
    {
        $this->db->query("SELECT position 
                            FROM __products 
                            WHERE id = ? 
                            LIMIT 1", $id);
        $position = $this->db->result('position');

        $this->db->query('SELECT pc.category_id 
                            FROM __products_categories pc 
                            WHERE product_id = ? 
                            ORDER BY position 
                            LIMIT 1', $id);
        $category_id = $this->db->result('category_id');

        $query = $this->db->placehold('SELECT p.id 
                                        FROM __products p, __products_categories pc
										WHERE pc.product_id = p.id 
										AND p.position < ?
										AND pc.position = ( SELECT MIN(pc2.position) 
                                                            FROM __products_categories pc2 
                                                            WHERE pc.product_id = pc2.product_id )
										AND pc.category_id = ?
										AND p.visible 
										ORDER BY p.position DESC 
										LIMIT 1', $position, $category_id);
        $this->db->query($query);
        return $this->get_product((int)$this->db->result('id'));
    }



    public function renders($filter = array())
    {
        $filter['visible'] = 1;

        $products = array();
        foreach ($this->get_products($filter) as $p) {
            $products[$p->id] = $p;
        }
        if (!empty($products)) {
            $products_ids = array_keys($products);
            foreach ($products as &$product) {
                $product->comments_count = 0;
                $product->price = 0;
                $product->compare_price = 0;
                $product->variants = array();
                $product->images = array();
                $product->properties = array();
            }

            $this->db->query("
				SELECT COUNT(id) AS comments_count, object_id
				FROM __comments 
				WHERE type='product' 
				AND object_id IN(?@)
				AND approved=1
				GROUP BY object_id
			", $products_ids);

            $comments_counts = $this->db->results();

            foreach ($comments_counts as $comment) {
                $products[$comment->object_id]->comments_count = $comment->comments_count;
            }

            $variants = $this->variants->get_variants(array('product_id' => $products_ids));

            foreach ($variants as &$variant) {
                if (empty($products[$variant->product_id]->price)) {
                    $products[$variant->product_id]->price = $variant->price;
                }
                if (empty($products[$variant->product_id]->compare_price)) {
                    $products[$variant->product_id]->compare_price = $variant->compare_price;
                }
                if ($variant->stock > 0) {
                    $products[$variant->product_id]->variants[] = $variant;
                }
            }

            $images = $this->products->get_images(array('product_id' => $products_ids));
            foreach ($images as $image) {
                $products[$image->product_id]->images[] = $image;
            }

            foreach ($products as &$product) {
                if (isset($product->variants[0])) {
                    $product->variant = $product->variants[0];
                    $product->price = $product->variant->price;
                    $product->compare_price = $product->variant->compare_price;
                }
                if (isset($product->images[0])) {
                    $product->image = $product->images[0];
                }
            }
        }


        return $products;
    }





}
