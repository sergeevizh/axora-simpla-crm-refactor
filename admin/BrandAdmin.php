<?php

namespace Axora;

use App\Api\Axora;

class BrandAdmin extends Axora
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $brand = new \stdClass();
        if ($this->request->method('post')) {
            $brand->id = $this->request->post('id', 'integer');
            $brand->name = $this->request->post('name');
            $brand->description = $this->request->post('description');
            $brand->h1 = $this->request->post('h1');

            $brand->url = trim($this->request->post('url', 'string'));
            $brand->meta_title = $this->request->post('meta_title');
            $brand->meta_keywords = $this->request->post('meta_keywords');
            $brand->meta_description = $this->request->post('meta_description');

            // Не допустить одинаковые URL разделов.
            if (($c = $this->brands->get_brand($brand->url)) && $c->id!=$brand->id) {
                $this->design->assign('message_error', 'url_exists');
            } elseif (empty($brand->name)) {
                $this->design->assign('message_error', 'name_empty');
            } elseif (empty($brand->url)) {
                $this->design->assign('message_error', 'url_empty');
            } else {
                if (empty($brand->id)) {
                    $brand->id = $this->brands->add_brand($brand);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->brands->update_brand($brand->id, $brand);
                    $this->design->assign('message_success', 'updated');
                }
                // Удаление изображения
                if ($this->request->post('delete_image')) {
                    $this->brands->delete_image($brand->id);
                }
                // Загрузка изображения
                $image = $this->request->files('image');
                if (!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->brands->delete_image($brand->id);

                    $basename = basename($image['name']);
                    $base = $this->image->correct_filename(pathinfo($basename, PATHINFO_FILENAME));
                    $ext = pathinfo($basename, PATHINFO_EXTENSION);
                    $image_name = $base .'.'.$ext;

                    move_uploaded_file($image['tmp_name'], $this->config->root_dir.$this->config->brands_images_dir.$image_name);

                    $this->brands->update_brand($brand->id, array('image'=>$image_name));
                }
                $brand = $this->brands->get_brand($brand->id);
            }
        } else {
            $brand->id = $this->request->get('id', 'integer');
            $brand = $this->brands->get_brand($brand->id);
        }

        $this->design->assign('brand', $brand);

        return  $this->design->fetch('brand.tpl');
    }
}
