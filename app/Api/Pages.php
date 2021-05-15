<?php

namespace App\Api;

class Pages extends Axora
{

    /**
     * Функция возвращает страницу по ее id или url (в зависимости от типа)
     *
     * @param  int|string $id
     * @return false|object
     */
    public function get_page($id)
    {
        if (gettype($id) == 'string') {
            $where = $this->db->placehold(' WHERE url=? ', $id);
        } else {
            $where = $this->db->placehold(' WHERE id=? ', intval($id));
        }

        $this->db->query("SELECT id, 
                                 url, 
                                 header, 
                                 name, 
                                 meta_title, 
                                 meta_description, 
                                 meta_keywords, 
                                 body, 
                                 menu_id, 
                                 position, 
                                 visible
                            FROM __pages 
                                $where 
                            LIMIT 1");

        return $this->db->result();
    }

    /**
     * Функция возвращает массив страниц, удовлетворяющих фильтру
     *
     * @param  array $filter
     * @return array
     */
    public function get_pages($filter = array())
    {
        $menu_filter = '';
        $visible_filter = '';

        $pages = array();

        if (isset($filter['menu_id'])) {
            $menu_filter = $this->db->placehold('AND menu_id IN ( ?@ )', (array)$filter['menu_id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND visible = ?', intval($filter['visible']));
        }

        $this->db->query("SELECT id, 
                        url, 
                        header, 
                        name, 
                        meta_title, 
                        meta_description, 
                        meta_keywords, 
                        body, 
                        menu_id, 
                        position, 
                        visible
                      FROM __pages 
                      WHERE 1 
                        $menu_filter 
                        $visible_filter 
                        ORDER BY position");

        foreach ($this->db->results() as $page) {
            $pages[$page->id] = $page;
        }

        return $pages;
    }

    /**
     * Создание страницы
     *
     * @param  array|object $page
     * @return false|mixed
     */
    public function add_page($page)
    {
        $query = $this->db->placehold('INSERT INTO __pages SET ?%', $page);
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query('UPDATE __pages SET position=id WHERE id=?', $id);

        return $id;
    }

    /**
     * Обновить страницу
     *
     * @param  int|array $id
     * @param  array|object $page
     * @return false|mixed
     */
    public function update_page($id, $page)
    {
        $query = $this->db->placehold('UPDATE __pages SET ?% WHERE id IN( ?@ )', $page, (array)$id);
        if (!$this->db->query($query)) {
            return false;
        }

        return $id;
    }

    /**
     * Удалить страницу
     *
     * @param  int $id
     * @return bool
     */
    public function delete_page($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold('DELETE FROM __pages WHERE id=? LIMIT 1', intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Функция возвращает массив меню
     *
     * @return array
     */
    public function get_menus()
    {
        $menus = array();

        $this->db->query('SELECT * FROM __menu ORDER BY position');
        foreach ($this->db->results() as $menu) {
            $menus[$menu->id] = $menu;
        }
        return $menus;
    }

    /**
     * Функция возвращает меню по id
     *
     * @param  int $menu_id
     * @return false|object
     */
    public function get_menu($menu_id)
    {
        $query = $this->db->placehold('SELECT * FROM __menu WHERE id=? LIMIT 1', intval($menu_id));
        $this->db->query($query);
        return $this->db->result();
    }
}
