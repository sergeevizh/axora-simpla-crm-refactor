<?php


namespace App\Api;


class Document extends Axora
{

    public function get($pid)
    {

        $query = $this->db->placehold("SELECT *
										FROM s_product_documents 
										WHERE product_id = ?
										", intval($pid));
        $this->db->query($query);
        return $this->db->results();
    }

    public function getById($id)
    {

        $query = $this->db->placehold("SELECT *
										FROM s_product_documents 
										WHERE id = ?
										", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    public function add($pid, $document, $dbName )
    {

        $this->db->query("INSERT INTO s_product_documents (product_id, document, name) VALUES ({$pid}, '{$document}', '{$dbName}')");
        $id = $this->db->insert_id();

        return $id;
    }

    public function updateName($id, $document)
    {
        $query = $this->db->placehold("UPDATE s_product_documents SET ?% WHERE id=? LIMIT 1", $document, intval($id));
        $this->db->query($query);
        return $id;
    }


    public function  delete($id)
    {
        $query = $this->db->placehold('DELETE FROM s_product_documents WHERE id=? LIMIT 1', $id);
        $this->db->query($query);
    }
}
