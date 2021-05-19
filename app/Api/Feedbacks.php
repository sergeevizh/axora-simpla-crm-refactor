<?php

namespace App\Api;

class Feedbacks extends Axora
{
    /**
     * @param  int $id
     *
     * @return object|false
     */
    public function get_feedback($id)
    {
        $query = $this->db->placehold('SELECT f.id, 
                                              f.name, 
                                              f.email, 
                                              f.ip, 
                                              f.message, 
                                              f.date 
                                        FROM __feedbacks f 
                                        WHERE id=? 
                                        LIMIT 1', intval($id));

        if ($this->db->query($query)) {
            return $this->db->result();
        }

        return false;
    }

    /**
     * @param  array $filter
     * @param  bool $new_on_top
     *
     * @return array|bool
     */
    public function get_feedbacks($filter = [], $new_on_top = false)
    {
        // По умолчанию
        $limit = 0;
        $page = 1;
        $keyword_filter = '';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw!=='') {
                    $keyword_filter .= $this->db->placehold("AND f.name LIKE '%$kw%' OR f.message LIKE '%$kw%' OR f.email LIKE '%$kw%' ");
                }
            }
        }

        if ($new_on_top) {
            $sort='DESC';
        } else {
            $sort='ASC';
        }

        $query = $this->db->placehold("SELECT f.id, 
                                              f.name, 
                                              f.email, 
                                              f.ip, 
                                              f.message, 
                                              f.date
										FROM __feedbacks f 
										WHERE 1 
										    $keyword_filter 
                                        ORDER BY f.id $sort $sql_limit");

        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param  array $filter
     *
     * @return bool|object|string
     */
    public function count_feedbacks($filter = [])
    {
        $keyword_filter = '';

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw!=='') {
                    $keyword_filter .= $this->db->placehold("AND f.name LIKE '%$kw%' OR f.message LIKE '%$kw%' OR f.email LIKE '%$kw%' ");
                }
            }
        }

        if (!empty($filter['read'])) {
            $keyword_filter = "AND is_read=". $filter['read'];
        }

        $query = $this->db->placehold("SELECT COUNT(DISTINCT f.id) AS count
										FROM __feedbacks f 
										WHERE 1 
										$keyword_filter");
        $this->db->query($query);

        return $this->db->result('count');
    }

    public function count_not_read()
    {
        $query = $this->db->placehold("SELECT COUNT(DISTINCT f.id) AS count
										FROM __feedbacks f 
										WHERE is_read=0");
        $this->db->query($query);

        return $this->db->result('count');
    }

    /**
     * @param  array|object $feedback
     *
     * @return bool|mixed
     */
    public function add_feedback($feedback)
    {
        $query = $this->db->placehold(
            'INSERT INTO __feedbacks
		    SET ?%,
		    date = NOW()',
            $feedback
        );

        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();

        return $id;
    }

    /**
     * @param  int $id
     * @param  object $feedback
     *
     * @return mixed
     */
    public function update_feedback($id, $feedback)
    {
        $date_query = '';
        if (isset($feedback->date)) {
            $date = $feedback->date;
            unset($feedback->date);
            $date_query = $this->db->placehold(', date=STR_TO_DATE(?, ?)', $date, $this->settings->date_format);
        }
        $query = $this->db->placehold("UPDATE __feedbacks SET ?% $date_query WHERE id IN( ?@ ) LIMIT 1", $feedback, (array)$id);
        $this->db->query($query);

        return $id;
    }

    public function markAsRead($ids)
    {
        $query = $this->db->placehold("UPDATE __feedbacks SET is_read=1  WHERE id IN( ?@ ) LIMIT 1", (array)$ids);
        $this->db->query($query);
    }

    /**
     * @param  int $id
     *
     * @return void
     */
    public function delete_feedback($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold('DELETE FROM __feedbacks WHERE id=? LIMIT 1', intval($id));
            $this->db->query($query);
        }
    }
}
