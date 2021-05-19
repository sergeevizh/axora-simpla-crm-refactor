<?php

namespace App\Api;

class Users extends Axora
{
    private $user;

    /**
     * TODO надо вынести в config/config.php
     * осторожно, при изменении соли испортятся текущие пароли пользователей
     *
     * @var string
     */
    private $salt = '8e86a279d6e182b3c811c559e6b15484';

    /**
     * @param array $filter
     *
     * @return array
     */
    public function get_users($filter = [])
    {
        $limit = 1000;
        $page = 1;
        $group_id_filter = '';
        $keyword_filter = '';
        $order = 'u.name';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        if (isset($filter['group_id'])) {
            $group_id_filter = $this->db->placehold('AND u.group_id IN( ?@ )', (array)$filter['group_id']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND ( u.name LIKE '%$kw%'
                                                                    OR u.email LIKE '%$kw%'
                                                                    OR u.last_ip LIKE '%$kw%'
                                                            )");
                }
            }
        }

        if (!empty($filter['sort'])) {
            switch ($filter['sort']) {
                case 'date':
                    $order = 'u.created DESC';

                    break;

                case 'name':
                    $order = 'u.name';

                    break;
            }
        }

        // Выбираем пользователей
        $query = $this->db->placehold("SELECT u.id,
                                              u.email,
                                              u.password,
                                              u.name,
                                              u.group_id,
                                              u.address,
                                              u.phone,
                                              u.enabled,
                                              u.last_ip,
                                              u.created,
                                              g.discount,
                                              g.name AS group_name
										FROM __users u
										LEFT JOIN __groups g ON u.group_id = g.id
										WHERE 1
											$group_id_filter
											$keyword_filter
										ORDER BY $order
										$sql_limit");
        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param array $filter
     *
     * @return false|int
     */
    public function count_users($filter = [])
    {
        $group_id_filter = '';
        $keyword_filter = '';

        if (isset($filter['group_id'])) {
            $group_id_filter = $this->db->placehold('AND u.group_id IN( ?@ )', (array)$filter['group_id']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND ( u.name LIKE '%$kw%'
                                                                    OR u.email LIKE '%$kw%'
                                                                    OR u.last_ip LIKE '%$kw%'
                                                            )");
                }
            }
        }

        // Выбираем пользователей
        $query = $this->db->placehold("SELECT COUNT(*) as count
										FROM __users u
										LEFT JOIN __groups g ON u.group_id=g.id
										WHERE 1
											$group_id_filter
											$keyword_filter");
        $this->db->query($query);

        return $this->db->result('count');
    }

    /**
     * @param int $id
     *
     * @return object|false
     */
    public function get_user($id)
    {
        if ($this->user) {
            return $this->user;
        }

        if (gettype($id) == 'string') {
            $where = $this->db->placehold(' WHERE u.email=? ', $id);
        } else {
            $where = $this->db->placehold(' WHERE u.id=? ', intval($id));
        }

        // Выбираем пользователя
        $query = $this->db->placehold("SELECT u.id,
                                              u.email,
                                              u.password,
                                              u.name,
                                              u.group_id,
                                              u.enabled,
                                              u.address,
                                              u.phone,
                                              u.last_ip,
                                              u.created,
                                              g.discount,
                                              g.name AS group_name
										FROM __users u
										LEFT JOIN __groups g ON u.group_id = g.id
											$where
										LIMIT 1", $id);
        $this->db->query($query);
        $user = $this->db->result();
        if (empty($user)) {
            return false;
        }
        $user->discount *= 1; // Убираем лишние нули, чтобы было 5 вместо 5.00

        $this->user = $user;

        return $this->user;
    }

    /**
     * @param object|array $user
     *
     * @return int|false
     */
    public function add_user($user)
    {
        $user = (array)$user;
        if (isset($user['password'])) {
            $user['password'] = md5($this->salt . $user['password'] . md5($user['password']));
        }

        $query = $this->db->placehold('SELECT COUNT(*) AS count
										FROM __users
										WHERE email=?', $user['email']);
        $this->db->query($query);

        if ($this->db->result('count') > 0) {
            return false;
        }

        $query = $this->db->placehold('INSERT INTO __users SET ?%', $user);
        $this->db->query($query);

        return $this->db->insert_id();
    }

    /**
     * @param int $id
     * @param object|array $user
     *
     * @return int
     */
    public function update_user($id, $user)
    {
        $user = (array)$user;

        if (isset($user['password'])) {
            $user['password'] = md5($this->salt . $user['password'] . md5($user['password']));
        }

        $query = $this->db->placehold('UPDATE __users SET ?% WHERE id=? LIMIT 1', $user, intval($id));
        $this->db->query($query);

        return $id;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete_user($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold('UPDATE __orders SET user_id=NULL WHERE user_id=?', intval($id));
            $this->db->query($query);

            $query = $this->db->placehold('DELETE FROM __users WHERE id=? LIMIT 1', intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|false
     */
    public function get_groups()
    {
        // Выбираем группы
        $query = $this->db->placehold('SELECT g.id,
                                              g.name,
                                              g.discount
										FROM __groups AS g
										ORDER BY g.discount');
        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * @param int $id
     *
     * @return object
     */
    public function get_group($id)
    {
        // Выбираем группу
        $query = $this->db->placehold('SELECT g.*
										FROM __groups AS g
										WHERE g.id = ?
										LIMIT 1', $id);
        $this->db->query($query);
        $group = $this->db->result();

        return $group;
    }

    /**
     * @param array|object $group
     *
     * @return int
     */
    public function add_group($group)
    {
        $query = $this->db->placehold('INSERT INTO __groups SET ?%', $group);
        $this->db->query($query);

        return $this->db->insert_id();
    }

    /**
     * @param int $id
     * @param array|object $group
     *
     * @return int
     */
    public function update_group($id, $group)
    {
        $query = $this->db->placehold('UPDATE __groups SET ?% WHERE id=? LIMIT 1', $group, intval($id));
        $this->db->query($query);

        return $id;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete_group($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold('UPDATE __users SET group_id = NULL WHERE group_id=? LIMIT 1', intval($id));
            $this->db->query($query);

            $query = $this->db->placehold('DELETE FROM __groups WHERE id=? LIMIT 1', intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return false|int
     */
    public function check_password($email, $password)
    {
        $enc_password = md5($this->salt . $password . md5($password));

        $query = $this->db->placehold('SELECT id FROM __users WHERE email=? AND password=? LIMIT 1', $email, $enc_password);
        $this->db->query($query);

        if ($id = $this->db->result('id')) {
            return $id;
        }

        return false;
    }

    public function exist(string $email, int $currentUserId = 0): bool
    {
        if ($currentUserId) {
            $this->db->query('SELECT count(*) as count FROM __users WHERE email=? AND id!=?', $email, $currentUserId);
        } else {
            $this->db->query('SELECT count(*) as count FROM __users WHERE email=?', $email);
        }

        return (bool)$this->db->result('count');
    }
}
