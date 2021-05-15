<?php

namespace Axora;

use App\Api\Axora;

class FeatureAdmin extends Axora
{

    public function fetch()
    {
        $feature = new \stdClass();
        if ($this->request->method('post')) {
            $feature->id = $this->request->post('id', 'integer');
            $feature->name = $this->request->post('name');
            $feature->in_filter = intval($this->request->post('in_filter'));


            if (empty($feature->id)) {
                $feature->id = $this->features->add_feature($feature);
                $feature = $this->features->get_feature($feature->id);
                $this->design->assign('message_success', 'added');
            } else {
                $this->features->update_feature($feature->id, $feature);
                $feature = $this->features->get_feature($feature->id);
                $this->design->assign('message_success', 'updated');
            }

            $feature_categories = $this->request->post('check');
            if (is_array($feature_categories)) {
                switch ($this->request->post('action')) {
                    case 'use-in-category':
                    {
                        $this->features->update_feature_categories($feature->id, $feature_categories, ['in_filter' => 1]);
                        break;
                    }
                    case 'use-not-in-category':
                    {
                        $this->features->update_feature_categories($feature->id, $feature_categories, ['in_filter' => 0]);
                        break;
                    }
                    case 'use-in-filter':
                    {
                        $this->features->update_feature_categories($feature->id, $feature_categories, ['in_product' => 1]);
                        break;
                    }
                    case 'use-not-in-filter':
                    {
                        $this->features->update_feature_categories($feature->id, $feature_categories, ['in_product' => 0]);
                        break;
                    }
                }
            }


        } else {
            $feature->id = $this->request->get('id', 'integer');
            $feature = $this->features->get_feature($feature->id);
        }

        $feature_categories = array();
        $in_product_filtered_categories = array();
        if ($feature) {
            $feature_categories = $this->features->get_full_fields_feature_categories($feature->id);
            $in_product_filtered_categories = array_filter($feature_categories, function ($cat) {
                return $cat->in_product == 1;
            });
            $in_product_filtered_categories = array_map(function ($cat) {
                return $cat->category_id;
            }, $in_product_filtered_categories);


            $in_filter_filtered_categories = array_filter($feature_categories, function ($cat) {
                return $cat->in_filter == 1;
            });

            $in_filter_filtered_categories = array_map(function ($cat) {
                return $cat->category_id;
            }, $in_filter_filtered_categories);
                    $this->design->assign('in_filter_filtered_categories', $in_filter_filtered_categories);

        }
        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);
        $this->design->assign('feature', $feature);
        $this->design->assign('feature_categories', array_map(function ($cat) {
            return $cat->category_id;
        }, $feature_categories));
        $this->design->assign('in_product_filtered_categories', $in_product_filtered_categories);

        return $this->design->fetch('feature.tpl');
    }
}
