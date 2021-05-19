<?php

namespace App\Api;

class Features extends Axora
{
    /**
     * @param array $filter
     *
     * @return array|false
     */
    public function get_features($filter = [])
    {
        $category_id_filter = '';
        $in_filter_filter = '';
        $id_filter = '';

        if (isset($filter['category_id'])) {
            $show_in_product_filter = '';

            if (isset($filter['show_in_product'])) {
                $show_in_product_filter = ' AND cf.in_product=1';
            }
            if (isset($filter['in_filter'])) {
                $show_in_product_filter = ' AND cf.in_filter=1';
            }

            $category_id_filter = $this->db->placehold("AND id IN ( SELECT feature_id 
                                                                    FROM __categories_features cf 
                                                                    WHERE cf.category_id IN(?@)
                                                                    $show_in_product_filter 
                                                                    )", (array)$filter['category_id']);
        }

//        if (isset($filter['in_filter'])) {
//            $in_filter_filter = $this->db->placehold('AND f.in_filter=?', intval($filter['in_filter']));
//        }

        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold('AND f.id IN(?@)', (array)$filter['id']);
        }

        // Выбираем свойства
        $query = $this->db->placehold("SELECT f.id, 
                                              f.name, 
                                              f.position,
                                              f.is_select, 
                                              f.in_filter
                                        FROM __features AS f
                                        WHERE 1
                                            $category_id_filter
                                            $in_filter_filter
                                            $id_filter
                                        ORDER BY f.position");
        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param int $id
     *
     * @return false|object
     */
    public function get_feature($id)
    {
        // Выбираем свойство
        $query = $this->db->placehold('SELECT f.id, 
                                              f.name, 
                                              f.position, 
                                              f.in_filter,
                                              f.is_select
										FROM __features AS f
										WHERE f.id=?
										LIMIT 1', $id);
        $this->db->query($query);

        return $this->db->result();
    }

    /**
     * @param int $id
     *
     * @return array|false
     */
    public function get_feature_categories($id)
    {
        $query = $this->db->placehold("SELECT cf.category_id
										FROM __categories_features cf
										WHERE cf.feature_id = ?										
										", $id);
        $this->db->query($query);

        return $this->db->results('category_id');
    }

    public function get_full_fields_feature_categories($id)
    {
        $query = $this->db->placehold("SELECT *
										FROM __categories_features cf
										WHERE cf.feature_id = ?										
										", $id);
        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param array|object $feature
     *
     * @return mixed
     */
    public function add_feature($feature)
    {
        $query = $this->db->placehold("INSERT INTO __features SET ?%", $feature);
        $this->db->query($query);
        $id = $this->db->insert_id();

        $query = $this->db->placehold("UPDATE __features SET position=id WHERE id=? LIMIT 1", $id);
        $this->db->query($query);

        return $id;
    }

    /**
     * @param  $id
     * @param  $feature
     *
     * @return mixed
     */
    public function update_feature($id, $feature)
    {
        $query = $this->db->placehold("UPDATE __features SET ?% WHERE id IN(?@) LIMIT ?", (array)$feature, (array)$id, count((array)$id));
        $this->db->query($query);

        return $id;
    }

    /**
     * @param int $id
     */
    public function delete_feature($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __features WHERE id=? LIMIT 1", intval($id));
            $this->db->query($query);
            $query = $this->db->placehold("DELETE FROM __options WHERE feature_id=?", intval($id));
            $this->db->query($query);
            $query = $this->db->placehold("DELETE FROM __categories_features WHERE feature_id=?", intval($id));
            $this->db->query($query);
        }
    }

    /**
     * @param int $product_id
     * @param int $feature_id
     */
    public function delete_option($product_id, $feature_id)
    {
        $query = $this->db->placehold('DELETE FROM __options WHERE product_id=? AND feature_id=? LIMIT 1', intval($product_id), intval($feature_id));
        $this->db->query($query);
    }

    /**
     * @param int $product_id
     * @param int $feature_id
     * @param string $value
     *
     * @return mixed
     */
    public function update_option($product_id, $feature_id, $value, $position = 0)
    {
        if ($value != '') {
            $query = $this->db->placehold('REPLACE INTO __options SET value=?, product_id=?, feature_id=?, position=?', $value, intval($product_id), intval($feature_id), $position);
        } else {
            $query = $this->db->placehold('DELETE FROM __options WHERE feature_id=? AND product_id=?', intval($feature_id), intval($product_id));
        }

        return $this->db->query($query);
    }

    /**
     * @param int $id
     * @param int $category_id
     */
    public function add_feature_category($id, $category_id)
    {
        $query = $this->db->placehold('INSERT IGNORE INTO __categories_features SET feature_id=?, category_id=?', $id, $category_id);
        $this->db->query($query);
    }

    public function update_feature_categories($id, $categories, $data)
    {
        $id = intval($id);

//        $query = $this->db->placehold('DELETE FROM __categories_features WHERE feature_id=?', $id);
//        $this->db->query($query);

        if (is_array($categories)) {
            foreach ($categories as $category) {
                $query = $this->db->placehold("SELECT * FROM __categories_features WHERE feature_id=$id AND category_id=$category");
                $this->db->query($query);
                $featureCategory = $this->db->result();

                if ($featureCategory) {
                    $query = $this->db->placehold("UPDATE __categories_features SET ?% WHERE feature_id=$id AND category_id=$category", (array)$data);
//                    var_dump($query);die;
                    $this->db->query($query);
                } else {
                    $data['feature_id'] = $id;
                    $data['category_id'] = $category;
                    $query = $this->db->placehold("INSERT INTO __categories_features SET ?%", $data);
                    $this->db->query($query);
                }
            }
        }
    }

    /**
     * @param array $filter
     *
     * @return array|false
     */
    public function get_options($filter = [])
    {
        $feature_id_filter = '';
        $product_id_filter = '';
        $category_id_filter = '';
        $visible_filter = '';
        $in_stock_filter = '';
        $brand_id_filter = '';
        $features_filter = '';

        if (empty($filter['feature_id']) && empty($filter['product_id'])) {
            return [];
        }

        if (isset($filter['feature_id'])) {
            $feature_id_filter = $this->db->placehold('AND po.feature_id IN(?@)', (array)$filter['feature_id']);
        }

        if (isset($filter['product_id'])) {
            $product_id_filter = $this->db->placehold('AND po.product_id IN(?@)', (array)$filter['product_id']);
        }

        if (isset($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id=po.product_id AND pc.category_id in(?@)', (array)$filter['category_id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('INNER JOIN __products p ON p.id=po.product_id AND p.visible=?', intval($filter['visible']));
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT COUNT(*)>0 FROM __variants pv WHERE pv.product_id=po.product_id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['in_stock']));
        }

        /*        if (isset($filter['brand_id'])) {
                    $brand_id_filter = $this->db->placehold('AND po.product_id IN(SELECT id FROM __products WHERE brand_id IN(?@))', (array)$filter['brand_id']);
                }*/

        /*        if (isset($filter['features'])) {
                    foreach ($filter['features'] as $feature_id=>$value) {
                        $features_filter .= $this->db->placehold('AND (po.feature_id=? OR po.product_id IN (SELECT product_id FROM __options WHERE feature_id=? AND value=? )) ', $feature_id, $feature_id, $value);
                    }
                }*/

        $query = $this->db->placehold("SELECT po.feature_id, po.value
										FROM __options po
										$visible_filter
										$category_id_filter
										WHERE 1
											$feature_id_filter
											$product_id_filter
											$in_stock_filter
											$brand_id_filter
											$features_filter
										GROUP BY po.feature_id, po.value
										ORDER BY po.value=0, -po.value DESC, po.value");

        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param array|int $product_id
     *
     * @return array|false
     */
    public function get_product_options($product_id)
    {
        $query = $this->db->placehold("SELECT f.id AS feature_id, 
                                              f.name, 
                                              f.is_select, 
                                              po.value, 
                                              po.product_id, 
                                              po.position
										FROM __options po
										LEFT JOIN __features f ON f.id=po.feature_id
										WHERE po.product_id IN( ?@ )
										ORDER BY po.position, f.position", (array)$product_id);

        $this->db->query($query);

        return $this->db->results();
    }
}
