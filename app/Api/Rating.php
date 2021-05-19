<?php

namespace App\Api;

class Rating extends Axora
{
    public function calculateRating($product_id)
    {
        $query = $this->db->placehold(
            "
                SELECT AVG(rating)
                FROM s_rating
                WHERE product_id ={$product_id} 
            "
        );

        $this->db->query($query);

        return round($this->db->results()[0]->{'AVG(rating)'});
    }

    public function getRating($product_id, $user_id)
    {
        $query = $this->db->placehold(
            "
                SELECT * FROM __rating 
                WHERE product_id={$product_id}
                AND user_id={$user_id} 
            "
        );

        $this->db->query($query);

        return $this->db->results();
    }

    public function getRatingsByProduct($product_ids)
    {
        if (is_array($product_ids)) {
            $product_ids = implode(',', $product_ids);
        }
        $query = $this->db->placehold(
            "
                SELECT AVG(rating) as rating, product_id
                FROM s_rating
                WHERE product_id IN ({$product_ids})
                GROUP BY product_id
            "
        );

        $this->db->query($query);

        return $this->db->results();
    }

    public function updateRating($rating, $product_id, $user_id)
    {
        $query = $this->db->placehold(
            "
                UPDATE __rating SET rating={$rating}
                WHERE product_id={$product_id}
                AND user_id={$user_id}   
            "
        );

        $this->db->query($query);

        return $this->db->results();
    }

    public function createRating($rating, $product_id, $user_id)
    {
        $query = $this->db->placehold(
            "
                INSERT INTO __rating ( rating, product_id, user_id)
                VALUES ({$rating}, {$product_id}, {$user_id}); 
            "
        );
        $this->db->query($query);

        return $this->db->results();
    }
}
