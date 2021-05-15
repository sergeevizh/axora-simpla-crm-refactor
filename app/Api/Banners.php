<?php

namespace App\Api;

class Banners extends Axora
{
    public $types = array(
        'slider' => 1,
        'main' => 2,

/*        'category' => 2,
        'category_grid' => 7,
        'cart' => 5,
        'users' => 6,
        'events' => 3*/
    );
    public $typesNames = array(
        1 => 'На главной слайдер',
        2 => 'На главной',

/*        2 => 'Категория',
        7 => 'Категория Плитка',
        3 => 'События',
        6 => 'Личный кабинет',
        5 => 'В корзине'*/
    );

    public function get($id)
    {

        $query = $this->db->placehold("SELECT ba.*
										FROM __banners ba
										WHERE ba.id = ?
										LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    public function gets($filter = array())
    {

        // По умолчанию
        $limit = 100;
        $page = 1;
        $visible_filter = '';
        $type_filter = '';
        $id_filter = '';
        $group_by = '';

        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold('AND ba.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND ba.visible=?', intval($filter['visible']));
        }
        if (isset($filter['type'])) {
            if(is_int($filter['type'])) {
                $type_filter = $this->db->placehold('AND ba.type=?', intval($filter['type']));
            } elseif (is_string($filter['type'])) {
                $type_filter = $this->db->placehold('AND ba.type=?', $this->types[$filter['type']]);
            }

        }

        $query = $this->db->placehold("SELECT ba.*
										FROM __banners ba
										WHERE 1
											$id_filter
											$visible_filter
											$type_filter
										$group_by
										ORDER BY ba.position
										");
        $this->db->query($query);
        return $this->db->results();
    }

    public function count($filter = array())
    {
/*        $visible_filter = '';
        $group_by = '';

        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold('AND ba.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND ba.visible=?', intval($filter['visible']));
        }


        $this->db->query($query);
        return $this->db->result('count');*/
    }

    public function add($banner)
    {
        $banner = (array)$banner;
        $query = $this->db->placehold("INSERT INTO __banners SET ?%", $banner);
        $this->db->query($query);
        $id = $this->db->insert_id();

        $query = $this->db->placehold("UPDATE __banners SET position=id WHERE id=?", $id);
        $this->db->query($query);

        return $id;
    }

    public function update($id, $banners)
    {
        $query = $this->db->placehold("UPDATE __banners SET ?% WHERE id=? LIMIT 1", $banners, intval($id));
        $this->db->query($query);
        return $id;
    }


    public function delete($id)
    {

        if (!empty($id)) {
            $this->delete_image($id);
            $query = $this->db->placehold("DELETE FROM __banners WHERE id=? LIMIT 1", intval($id));
            $this->db->query($query);
        }
    }

    public function delete_image($banner_id)
    {
        $query = $this->db->placehold("SELECT image FROM __banners WHERE id=?", intval($banner_id));
        $this->db->query($query);
        $filename = $this->db->result('image');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __banners SET image=NULL WHERE id=?", $banner_id);
            $this->db->query($query);
            $query = $this->db->placehold("SELECT count(*) AS count FROM __banners WHERE image=? LIMIT 1", $filename);
            $this->db->query($query);
            $count = $this->db->result('count');
            if ($count == 0) {
                @unlink($this->config->root_dir . $this->config->banners_dir . $filename);
            }
        }
    }
}
