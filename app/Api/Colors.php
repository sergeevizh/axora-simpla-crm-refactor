<?php

namespace App\Api;

class Colors extends Axora
{
    public function get_colors($filter = [])
    {
        $category_id_filter = '';
        $visible_filter = '';
        $in_stock_filter = '';
        $group_by = '';
        $order = 'co.position';

        /*        $id_filter = '';
                $category_id_filter = '';

                if(!empty($filter['id']))
                    $id_filter = $this->db->placehold('AND id co.in(?@)', (array)$filter['id']);*/

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('LEFT JOIN __products_colors pco ON pco.color_id=co.id
				LEFT JOIN __products_categories pc ON pc.product_id = pco.product_id WHERE pc.category_id in(?@)', (array)$filter['category_id']);
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold(
                'AND (SELECT COUNT(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?',
                intval($filter['in_stock'])
            );
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold("LEFT JOIN __products_colors pco ON pco.color_id=co.id
                                                        LEFT JOIN __products p ON p.id=pco.product_id 
                                                        LEFT JOIN __products_categories pc ON p.id = pc.product_id 
                                                        WHERE 1
                                                        AND pc.category_id IN( ?@ ) 
                                                        $visible_filter 
                                                        $in_stock_filter", (array)$filter['category_id']);
            $group_by = 'GROUP BY co.id';
        } elseif (isset($filter['visible']) || isset($filter['in_stock'])) {
            $category_id_filter = $this->db->placehold("
                                    LEFT JOIN __products_colors pco ON pco.color_id=co.id
                                    LEFT JOIN __products p ON p.id=pco.product_id
                                    WHERE 1
                                    $visible_filter 
                                    $in_stock_filter");
            $group_by = 'GROUP BY co.id';
        }

        $query = $this->db->placehold("SELECT co.*
											FROM __colors co
											$category_id_filter
				                        $group_by
				                        ORDER BY $order");

        $this->db->query($query);

        return $this->db->results();
    }

    public function get_color($id)
    {
        $this->db->query("SELECT * FROM __colors WHERE id = ? LIMIT 1", intval($id));

        return $this->db->result();
    }

    public function add_color($color)
    {
        $color = (array)$color;

        $this->db->query("SELECT MAX(position) AS max_position FROM __colors");
        $color['position'] = $this->db->result('max_position') + 1;

        $this->db->query("INSERT INTO __colors SET ?%", $color);

        return $this->db->insert_id();
    }

    public function update_color($id, $color)
    {
        $query = $this->db->placehold("UPDATE __colors SET ?% WHERE id=? LIMIT 1", $color, intval($id));
        $this->db->query($query);

        return $id;
    }

    public function delete_color($id)
    {
        if (!empty($id)) {
            // удаляем рисунок
            //$this->delete_image($id);

            // удаляем с бД
            $this->db->query("DELETE FROM __colors WHERE id=? LIMIT 1", intval($id));

            $this->db->query("DELETE FROM __products_colors WHERE color_id=?", intval($id));
        }
    }

    /*
        public function delete_image($color_id)
        {
            $query = $this->db->placehold("SELECT image FROM __colors WHERE id=?", intval($color_id));
            $this->db->query($query);
            $filename = $this->db->result('image');

            if(!empty($filename))
            {

                $this->db->query("UPDATE __colors SET image=NULL WHERE id=?", $color_id);

                $this->db->query("SELECT count(*) as count FROM __colors WHERE image=? LIMIT 1", $filename);
                $count = $this->db->result('count');

                if($count == 0)
                {
                    @unlink($this->config->root_dir.$this->config->colors_images_dir.$filename);
                }
            }
        }*/

    public function update_colors_product($product_id, $colors_products_ids, $clear = true)
    {
        if ($clear) {
            $query = $this->db->placehold("DELETE FROM __products_colors WHERE product_id=?", intval($product_id));
            $this->db->query($query);
        }

        if (is_array($colors_products_ids)) {
            foreach ($colors_products_ids as $c_id) {
                $this->db->query("INSERT INTO __products_colors SET product_id=?, color_id=?", $product_id, $c_id);
            }
        }
    }

    public function get_colors_products($product_id)
    {
        $query = $this->db->placehold("SELECT color_id FROM __products_colors WHERE product_id=?", intval($product_id));
        $this->db->query($query);

        return $this->db->results('color_id');
    }
}
