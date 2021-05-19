<?php

namespace App\Api;

class Comments extends Axora
{
    /**
     * Возвращает комментарий по id
     *
     * @param $id
     *
     * @return bool|object|string
     */
    public function get_comment($id)
    {
        $query = $this->db->placehold("SELECT c.id, 
                                              c.object_id, 
                                              c.name, 
                                              c.ip, 
                                              c.type, 
                                              c.text, 
                                              c.date, 
                                              c.approved 
                                        FROM __comments c 
                                        WHERE c.id=? 
                                        LIMIT 1", intval($id));

        if ($this->db->query($query)) {
            return $this->db->result();
        }

        return false;
    }

    /**
     * Возвращает комментарии, удовлетворяющие фильтру
     *
     * @param array $filter
     *
     * @return array|bool
     */
    public function get_comments($filter = [])
    {
        // По умолчанию
        $limit = 100;
        $page = 1;
        $object_id_filter = '';
        $type_filter = '';
        $keyword_filter = '';
        $approved_filter = '';
        $ip = '';
        $sort = 'DESC';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        if (isset($filter['ip'])) {
            $ip = $this->db->placehold("OR c.ip=?", $filter['ip']);
        }
        if (isset($filter['approved'])) {
            $approved_filter = $this->db->placehold("AND (c.approved=? $ip)", intval($filter['approved']));
        }

        if (!empty($filter['object_id'])) {
            $object_id_filter = $this->db->placehold('AND c.object_id IN( ?@ )', (array)$filter['object_id']);
        }

        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold('AND c.type=?', $filter['type']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND c.name LIKE '%$kw%' OR c.text LIKE '%$kw%' ");
                }
            }
        }

        $query = $this->db->placehold("SELECT c.id, 
                                              c.object_id, 
                                              c.ip, 
                                              c.name, 
                                              c.text, 
                                              c.type, 
                                              c.date, 
                                              c.approved
										FROM __comments c
										WHERE 1
											$object_id_filter
											$type_filter
											$keyword_filter
											$approved_filter
										ORDER BY id $sort
										$sql_limit");

        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * Количество комментариев, удовлетворяющих фильтру
     *
     * @param array $filter
     *
     * @return bool|object|string
     */
    public function count_comments($filter = [])
    {
        $object_id_filter = '';
        $type_filter = '';
        $approved_filter = '';
        $keyword_filter = '';

        if (!empty($filter['object_id'])) {
            $object_id_filter = $this->db->placehold('AND c.object_id IN( ?@ )', (array)$filter['object_id']);
        }

        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold('AND c.type=?', $filter['type']);
        }

        if (isset($filter['approved'])) {
            $approved_filter = $this->db->placehold('AND c.approved=?', intval($filter['approved']));
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND c.name LIKE '%$kw%' OR c.text LIKE '%$kw%' ");
                }
            }
        }

        $query = $this->db->placehold("SELECT COUNT(DISTINCT c.id) AS count
										FROM __comments c
										WHERE 1
										    $object_id_filter
										    $type_filter
										    $keyword_filter
										    $approved_filter", $this->settings->date_format);

        $this->db->query($query);

        return $this->db->result('count');
    }

    /**
     * Добавление комментария
     *
     * @param $comment
     *
     * @return bool|mixed
     */
    public function add_comment($comment)
    {
        $comment = (array)$comment;

        $query = $this->db->placehold('INSERT INTO __comments SET ?%, DATE = NOW()', $comment);

        if (!$this->db->query($query)) {
            return false;
        }

        return $this->db->insert_id();
    }

    /**
     * Изменение комментария
     *
     * @param  int $id
     * @param  array|object $comment
     *
     * @return mixed
     */
    public function update_comment($id, $comment)
    {
        $query = $this->db->placehold("UPDATE __comments SET ?% WHERE id IN( ?@ ) LIMIT 1", $comment, (array)$id);
        $this->db->query($query);

        return $id;
    }

    /**
     * Удаление комментария
     *
     * @param int $id
     */
    public function delete_comment($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __comments WHERE id=? LIMIT 1", intval($id));
            $this->db->query($query);
        }
    }
}
