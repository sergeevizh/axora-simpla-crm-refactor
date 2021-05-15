<?php

namespace Axora;

use App\Api\Axora;
use stdClass;

class ProductAdmin extends Axora
{
    public function fetch()
    {
        $options = array();
        $product_categories = array();
        $variants = array();
        $images = array();
        $related_products = array();
        $related_products2 = array();

        if ($this->request->method('post') && !empty($_POST)) {
            $product = new \stdClass();
            $product->id = $this->request->post('id', 'integer');
            $product->name = $this->request->post('name');
            $product->visible = $this->request->post('visible', 'boolean');
            $product->featured = $this->request->post('featured');
            $product->brand_id = $this->request->post('brand_id', 'integer');
            $product->new = $this->request->post('new', 'boolean');
              $product->discounted = $this->request->post('discounted');

            $product->url = trim($this->request->post('url', 'string'));
            $product->meta_title = $this->request->post('meta_title');
            $product->meta_keywords = $this->request->post('meta_keywords');
            $product->meta_description = $this->request->post('meta_description');
            $product->youtube_link = $this->request->post('youtube_link');
            $product->annotation = $this->request->post('annotation');
            $product->body = $this->request->post('body');

            // Варианты товара
            if ($this->request->post('variants')) {
                foreach ($this->request->post('variants') as $n=>$va) {
                    foreach ($va as $i=>$v) {
                        if (empty($variants[$i])) {
                            $variants[$i] = new \stdClass();
                        }
                        $variants[$i]->$n = $v;
                    }
                }
            }

            // Категории товара
            $product_categories = $this->request->post('categories');
            if (is_array($product_categories)) {
                $pc = array();
                foreach ($product_categories as $position => $c) {
                    $x = new \stdClass();
                    $x->category_id = $c;
                    $x->position = $position;
                    $pc[] = $x;
                }
                $product_categories = $pc;
            }


            // Свойства товара
            $options = $this->request->post('options');
            if (is_array($options)) {
                $po = array();
                foreach ($options as $f_id => $val) {
                    $po[$f_id] = new \stdClass();
                    $po[$f_id]->feature_id = $f_id;
                    $po[$f_id]->values = array_unique($val);
                }
                $options = $po;
            }

            // Связанные товары
            if (is_array($this->request->post('related_products'))) {
                $rp = array();
                foreach ($this->request->post('related_products') as $p) {
                    $rp[$p] = new \stdClass();
                    $rp[$p]->product_id = $product->id;
                    $rp[$p]->related_id = $p;
                }
                $related_products = $rp;
            }

            // Связанные товары
            if (is_array($this->request->post('related_products2'))) {
                $rp = array();
                foreach ($this->request->post('related_products2') as $p) {
                    $rp[$p] = new \stdClass();
                    $rp[$p]->product_id = $product->id;
                    $rp[$p]->related_id = $p;
                }
                $related_products2 = $rp;
            }

            // Не допустить пустое название товара.
            if (empty($product->name)) {
                $this->design->assign('message_error', 'empty_name');
                if (!empty($product->id)) {
                    $images = $this->products->get_images(array('product_id'=>$product->id));
                }
            }
            // Не допустить одинаковые URL разделов.
            elseif (($p = $this->products->get_product($product->url)) && $p->id!=$product->id) {
                $this->design->assign('message_error', 'url_exists');
                if (!empty($product->id)) {
                    $images = $this->products->get_images(array('product_id'=>$product->id));
                }
            } else {
                if (empty($product->id)) {
                    $product->id = $this->products->add_product($product);
                    $product = $this->products->get_product($product->id);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->products->update_product($product->id, $product);
                    $product = $this->products->get_product($product->id);
                    $this->design->assign('message_success', 'updated');
                }

                if ($product->id) {
                    // Категории товара
                    $query = $this->db->placehold('DELETE FROM __products_categories WHERE product_id=?', $product->id);
                    $this->db->query($query);
                    if (is_array($product_categories)) {
                        foreach ($product_categories as $i=>$category) {
                            $this->categories->add_product_category($product->id, $category->category_id, $i);
                        }
                    }
                    
                     // Документы
                    if ( !empty($_FILES['documents']['name'])) {

                        $countfiles = count($_FILES['documents']['name']);

                        for($i=0;$i<$countfiles;$i++){
                            $filename = time() . $_FILES['documents']['name'][$i];
                            move_uploaded_file($_FILES['documents']['tmp_name'][$i],$this->config->root_dir.$this->config->documents_dir.$filename);

                            $this->document->add(
                                $product->id,
                                $filename,
                                $_POST['document']['name'][$i]
                            );

                        }
                    }

                    if ( !empty($_POST['exist_document'])) {
                        foreach ( $_POST['exist_document'] as $id => $documentName ) {
                            $this->document->updateName($id, ['name' => $documentName]);
                        }
                    }

                    // Варианты
                    if (is_array($variants)) {
                        $variants_ids = array();
                        foreach ($variants as $index=>&$variant) {
                            if ($variant->stock == '∞' || $variant->stock == '') {
                                $variant->stock = null;
                            }

                            // Удалить файл
                            if (!empty($_POST['delete_attachment'][$index])) {
                                $this->variants->delete_attachment($variant->id);
                            }

                            // Загрузить файлы
                            if (!empty($_FILES['attachment']['tmp_name'][$index]) && !empty($_FILES['attachment']['name'][$index])) {
                                $attachment_tmp_name = $_FILES['attachment']['tmp_name'][$index];
                                $attachment_name = $_FILES['attachment']['name'][$index];
                                move_uploaded_file($attachment_tmp_name, $this->config->root_dir.'/'.$this->config->downloads_dir.$attachment_name);
                                $variant->attachment = $attachment_name;
                            }

                            if (!empty($variant->id)) {
                                $this->variants->update_variant($variant->id, $variant);
                            } else {
                                $variant->product_id = $product->id;
                                $variant->id = $this->variants->add_variant($variant);
                            }
                            $variant = $this->variants->get_variant($variant->id);
                            if (!empty($variant->id)) {
                                $variants_ids[] = $variant->id;
                            }
                        }


                        // Удалить непереданные варианты
                        $current_variants = $this->variants->get_variants(array('product_id'=>$product->id));
                        foreach ($current_variants as $current_variant) {
                            if (!in_array($current_variant->id, $variants_ids)) {
                                $this->variants->delete_variant($current_variant->id);
                            }
                        }

                        //if(!empty($))

                        // Отсортировать  варианты
                        asort($variants_ids);
                        $i = 0;
                        foreach ($variants_ids as $variant_id) {
                            $this->variants->update_variant($variants_ids[$i], array('position'=>$variant_id));
                            $i++;
                        }
                    }

                    // Удаление изображений
                    $images = (array)$this->request->post('images');
                    $current_images = $this->products->get_images(array('product_id'=>$product->id));
                    foreach ($current_images as $image) {
                        if (!in_array($image->id, $images)) {
                            $this->products->delete_image($image->id);
                        }
                    }

                    // Порядок изображений
                    if ($images = $this->request->post('images')) {
                        $i=0;
                        foreach ($images as $id) {
                            $this->products->update_image($id, array('position'=>$i));
                            $i++;
                        }
                    }
                    // Загрузка изображений
                    if ($images = $this->request->files('images')) {
                        for ($i=0; $i<count($images['name']); $i++) {
                            if ($image_name = $this->image->upload_image($images['tmp_name'][$i], $images['name'][$i])) {
                                $this->products->add_image($product->id, $image_name);
                            } else {
                                $this->design->assign('error', 'error uploading image');
                            }
                        }
                    }
                    // Загрузка изображений из интернета и drag-n-drop файлов
                    if ($images = $this->request->post('images_urls')) {
                        foreach ($images as $url) {
                            // Если не пустой адрес и файл не локальный
                            if (!empty($url) && $url != 'http://' && strstr($url, '/')!==false) {
                                $this->products->add_image($product->id, $url);
                            } elseif ($dropped_images = $this->request->files('dropped_images')) {
                                $key = array_search($url, $dropped_images['name']);
                                if ($key!==false && $image_name = $this->image->upload_image($dropped_images['tmp_name'][$key], $dropped_images['name'][$key])) {
                                    $this->products->add_image($product->id, $image_name);
                                }
                            }
                        }
                    }
                    $images = $this->products->get_images(array('product_id'=>$product->id));

                    // Характеристики товара

                    // Удалим все из товара
                    foreach ($this->features->get_product_options($product->id) as $po) {
                        $this->features->delete_option($product->id, $po->feature_id);
                    }

                    // Свойства текущей категории
                    $category_features = array();
                    foreach ($this->features->get_features(array('category_id'=>$product_categories[0]->category_id)) as $f) {
                        $category_features[] = $f->id;
                    }

                    if (is_array($options)) {
                        foreach ($options as $option) {
                            if (in_array($option->feature_id, $category_features)) {

                                if (is_array($option->values) && !empty($option->values[0])) {

                                    foreach ($option->values as $pos => $value) {

                                        $this->features->update_option($product->id, $option->feature_id, trim($value), $pos);
                                    }
                                }
                                // $this->features->update_option($product->id, $option->feature_id, $option->value);
                            } elseif(is_array($option->values) && !empty($option->values[0])) {
                                foreach ($option->values as $pos => $value) {
                                    $this->features->update_option($product->id, $option->feature_id, trim($value), $pos);
                                }
                                $this->features->add_feature_category($option->feature_id, reset($product_categories)->category_id);
                            }
                        }
                    }
                        // Свойства товара
                    $options = $this->features->get_product_options($product->id);


                    // Связанные товары
                    $query = $this->db->placehold('DELETE FROM __related_products WHERE product_id=?', $product->id);
                    $this->db->query($query);
                    if (is_array($related_products)) {
                        $pos = 0;
                        foreach ($related_products  as $i=>$related_product) {
                            $this->products->add_related_product($product->id, $related_product->related_id, $pos++);
                        }
                    }

                    // Связанные товары 2
                    $query = $this->db->placehold('DELETE FROM __related_products2 WHERE product_id=?', $product->id);
                    $this->db->query($query);
                    if (is_array($related_products2)) {
                        $pos = 0;
                        foreach ($related_products2  as $i=>$related_product) {
                            $this->products->add_related_product2($product->id, $related_product->related_id, $pos++);
                        }
                    }

                }
            }

            //header('Location: '.$this->request->url(array('message_success'=>'updated')));
        } else {
            $id = $this->request->get('id', 'integer');
            $product = $this->products->get_product(intval($id));

            if ($product) {

                // Категории товара
                $product_categories = $this->categories->get_product_categories($product->id);

                // Варианты товара
                $variants = $this->variants->get_variants(array('product_id'=>$product->id));

                // Изображения товара
                $images = $this->products->get_images(array('product_id'=>$product->id));

                // Свойства товара
                $options = $this->features->get_product_options($product->id);

                // Связанные товары
                $related_products = $this->products->get_related_products(array('product_id'=>$product->id));

                // Связанные товары
                $related_products2 = $this->products->get_related_products2(array('product_id'=>$product->id));

                  $documents = $this->document->get($product->id);
                
            } else {
                // Сразу активен
                $product = new \stdClass();
                $product->visible = 1;
            }
        }


        if (empty($variants)) {
            $variants = array(1);
        }

        if (empty($product_categories)) {
            if ($category_id = $this->request->get('category_id')) {
                $product_categories[0]->category_id = $category_id;
            } else {
                $product_categories = array(1);
            }
        }
        if (empty($product->brand_id) && $brand_id=$this->request->get('brand_id')) {
            $product->brand_id = $brand_id;
        }

        if (!empty($related_products)) {
            $r_products = array();
            foreach ($related_products as &$r_p) {
                $r_products[$r_p->related_id] = &$r_p;
            }
            $temp_products = $this->products->get_products(array('id'=>array_keys($r_products), 'limit' => count(array_keys($r_products))));
            foreach ($temp_products as $temp_product) {
                $r_products[$temp_product->id] = $temp_product;
            }

            $related_products_images = $this->products->get_images(array('product_id'=>array_keys($r_products)));
            foreach ($related_products_images as $image) {
                $r_products[$image->product_id]->images[] = $image;
            }
        }

        if (!empty($related_products2)) {
            $r_products2 = array();
            foreach ($related_products2 as &$r_p2) {
                $r_products2[$r_p2->related_id] = &$r_p2;
            }
            $temp_products2 = $this->products->get_products(array('id'=>array_keys($r_products2), 'limit' => count(array_keys($r_products2))));
            foreach ($temp_products2 as $temp_product) {
                $r_products2[$temp_product->id] = $temp_product;
            }

            $related_products_images2 = $this->products->get_images(array('product_id'=>array_keys($r_products2)));
            foreach ($related_products_images2 as $image) {
                $r_products2[$image->product_id]->images[] = $image;
            }
        }

        if (is_array($options)) {
            $temp_options = array();
            foreach ($options as $option) {
                if(empty($temp_options[$option->feature_id])) {
                    $temp_options[$option->feature_id] = new stdClass();
                    $temp_options[$option->feature_id]->values = array();
                }
                $temp_options[$option->feature_id]->values[] = $option->value;
            }
            $options = $temp_options;
        }

  if (!empty($product->id)) {
            if (empty($documents) ) {
                $documents = $this->document->get($product->id);
            }
            $this->design->assign('documents', $documents);
        }

        $this->design->assign('product', $product);

        $this->design->assign('product_categories', $product_categories);
        $this->design->assign('product_variants', $variants);
        $this->design->assign('product_images', $images);
        $this->design->assign('options', $options);
        $this->design->assign('related_products', $related_products);
        $this->design->assign('related_products2', $related_products2);


        // Все бренды
        $brands = $this->brands->get_brands();
        $this->design->assign('brands', $brands);

        // Все категории
        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);

        // Все свойства товара
        $category = reset($product_categories);
        if (!is_object($category)) {
            $category = reset($categories);
        }
        if (is_object($category)) {
   $features = $this->features->get_features(array(
                    'category_id' => isset($category->category_id) ? $category->category_id : $category->id,
                    'show_in_product' => 1
                )
            );            $this->design->assign('features', $features);
        }
        return $this->design->fetch('product.tpl');
    }
}
