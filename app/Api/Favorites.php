<?php

namespace App\Api;

class Favorites extends Axora
{
    public function get_awaiting($filter = [])
    {
        $filter_user_id = '';
        $filter_product_id = '';

        if (!empty($filter['user_id'])) {
            $filter_user_id = $this->db->placehold('AND user_id in(?@)', (array)$filter['user_id']);
        }

        if (!empty($filter['product_id'])) {
            $filter_product_id = $this->db->placehold('AND product_id in(?@)', (array)$filter['product_id']);
        }

        if (!$filter_user_id && $filter_product_id) {
            return [];
        }

        $this->db->query("SELECT * 
                            FROM __favorites
                            WHERE 1
                            $filter_user_id
                            $filter_product_id
                            ORDER BY date");

        return $this->db->results();
    }

    public function add_awaiting($awaiting)
    {
        $awaiting = (array)$awaiting;
        if (empty($awaiting['user_id']) || empty($awaiting['product_id'])) {
            return false;
        }

        $this->db->query("INSERT INTO _favorites SET ?%", $awaiting);

        return $this->db->insert_id();
    }

    public function delete_awaiting($id)
    {
        if (!empty($id)) {
            $this->db->query("DELETE FROM __users_favorites WHERE id = ? LIMIT 1", intval($id));
        }
    }
}
