<?php

namespace App\Controllers;

class ComparesController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        // Удаление товара из корзины
        if ($delete_id = intval($this->request->get('delete'))) {
            $this->compares->delete($delete_id);
            header('location: ' . $this->config->root_url . '/compares');
            exit;
        }
    }

    public function fetch()
    {

        $filter = array();
        $filter['visible'] = 1;
        $filter['id'] = $this->compares->gets();

        if (!empty($filter['id'])) {


            $features = array();

            $this->db->query('SELECT DISTINCT feature_id FROM __options WHERE product_id IN (?@)', $filter['id']);
            $feature_ids = $this->db->results('feature_id');
            if (!empty($feature_ids)) {

                foreach ($this->features->get_features(array('id' => $feature_ids)) as $f) {
                    $features[$f->id] = $f;

                }
                $this->design->assign('features', $features);
            }

            $products = $this->products->get_products_compile($filter);
            foreach ($products as $product) {
                $product->features = array();
                foreach ($this->features->get_product_options(array('product_id' => $product->id)) as $feature) {
                    if (empty($product->features[$feature->feature_id])) {
                        $product->features[$feature->feature_id] = $feature;
                        $product->features[$feature->feature_id]->values = array();
                    }
                    $product->features[$feature->feature_id]->values[] = $feature->value;
                }
                foreach ($product->features as $f) {
                    if (!empty($f->values)) {
                        sort($f->values);
                        $product->features[$f->feature_id]->text = implode(', ', $f->values);
                    }
                }
            }

            $this->design->assign('products', $products);
        }


        if ($this->page) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
        }

        return $this->design->fetch('compares.tpl');
    }
}
