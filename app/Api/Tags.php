<?php

namespace App\Api;

class Tags extends Axora
{

    public function gets_tags($filter = array())
    {

        // По умолчанию
        $limit = 100;
        $page = 1;

        $tag_id_filter = '';
        $visible_filter = '';
        $in_category_filter = '';
        $category_id_filter = '';
        $features_filter = '';
        $brands_filter = '';
//        $colors_filter = '';
        $group_by = '';

        $order = 't.position DESC';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        if (!empty($filter['id'])) {
            $tag_id_filter = $this->db->placehold('AND t.id IN(?@)', (array)$filter['id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND t.visible=?', intval($filter['visible']));
        }


        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('AND t.category_id IN(?@)', (array)$filter['category_id']);
        }

        if (!empty($filter['features']) && !empty($filter['features'])) {
            $kw = $this->db->escape(trim($filter['features']));
            $features_filter .= $this->db->placehold("AND t.features LIKE '%$kw%'");
        }

        if (!empty($filter['brand_id'])) {
            //$brand_id_filter = $this->db->placehold('AND t.brand_id IN(?@)', (array)$filter['brand_id']);
        }

//        if (!empty($filter['colors'])) {
//            $kw = $this->db->escape(trim($filter['colors']));
//            $colors_filter .= $this->db->placehold("AND t.colors LIKE '%$kw%'");
//        }
        // echo $colors_filter;
        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND t.visible=?', intval($filter['visible']));
        }
        // Выбираем все
        $query = $this->db->placehold("SELECT t.id,
                                              t.name,
                                              t.h1,
                                              t.url,
                                              t.visible,
                                              t.meta_title,
                                              t.meta_keywords,
                                              t.meta_description,
                                              t.description,
                                              t.category_id,
                                              t.features,
                                              t.colors,
                                              t.brands,
                                              t.position
								 		FROM __tags AS t
								 		WHERE 1
								 		$tag_id_filter
								 		$visible_filter
								 		$in_category_filter
								 		$category_id_filter
								 		$features_filter
								 		$brands_filter
								 		
                                        $group_by
                                        ORDER BY $order
                                            $sql_limit");

        $this->db->query($query);
        return $this->db->results();
    }

    public function count_tags($filter = array())
    {
        $tag_id_filter = '';
        $visible_filter = '';
        $in_category_filter = '';
        $category_id_filter = '';
        $features_filter = '';
        $brands_filter = '';
//        $colors_filter = '';


        if (isset($filter['in_category'])) {
            $in_category_filter = $this->db->placehold('AND t.in_category=?', intval($filter['in_category']));
        }

        $this->db->query("SELECT COUNT(DISTINCT t.id) AS count
                            FROM __tags AS t
                            WHERE 1
                                $tag_id_filter
                                $visible_filter
                                $in_category_filter
                                $category_id_filter
                                $features_filter
                                $brands_filter
                            ");
        return $this->db->result('count');
    }

    public function get_tag($id)
    {
        if (is_int($id)) {
            $filter = $this->db->placehold('t.id = ?', intval($id));
        } else {
            $filter = $this->db->placehold('t.url = ?', $id);
        }

        $query = $this->db->placehold("SELECT t.id,
                                              t.name,
                                              t.h1,
                                              t.url,
                                              t.visible,
                                              t.views,
                                              t.in_category,
                                              t.in_category_name,
                                              t.meta_title,
                                              t.meta_keywords,
                                              t.meta_description,
                                              t.description,
                                              t.category_id,
                                              t.features,
                                              t.colors,
                                              t.brands,
                                              t.position
                                        FROM __tags AS t
                                        WHERE $filter
                                        LIMIT 1");

        $this->db->query($query);

        return $this->db->result();
    }


    public function add_tag($tag)
    {
        $tag = (array)$tag;
        if (empty($tag['url'])) {
            $tag['url'] = preg_replace("/[\s]+/ui", '_', $tag['name']);
            $tag['url'] = strtolower(preg_replace("/[^0-9a-zа-я_]+/ui", '', $tag['url']));
        }

        $query = $this->db->placehold("INSERT INTO __tags SET ?%", $tag);

        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query("UPDATE __tags SET position=id WHERE id=?", intval($id));

        return $id;
    }

    public function update_tag($id, $tag)
    {
        $query = $this->db->placehold("UPDATE __tags SET ?% WHERE id=? LIMIT 1", $tag, intval($id));
        $this->db->query($query);
        return $id;
    }


    public function delete_tag($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __tags WHERE id=? LIMIT 1", $id);
            $this->db->query($query);
        }
    }


}
