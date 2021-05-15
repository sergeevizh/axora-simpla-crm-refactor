<?php

namespace Axora;

use App\Api\Axora;
use stdClass;

class BannerAdmin extends Axora
{
	private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');
	public function fetch()
	{
		$banner = new stdClass;
		if($this->request->method('post'))
		{
			$banner->id = $this->request->post('id', 'integer');
			$banner->name = $this->request->post('name');
			$banner->sub_text = $this->request->post('sub_text');
			$banner->link = $this->request->post('link');
			$banner->visible = $this->request->post('visible', 'boolean');
			$banner->type = $this->request->post('type', 'integer');

			if(empty($banner->name))
			{
				$this->design->assign('message_error', 'name_empty');
			}
			else
			{

				if(empty($banner->id))
				{
					$banner->id = $this->banners->add($banner);
					$this->design->assign('message_success', 'added');
				}
				else
				{
					$this->banners->update($banner->id, $banner);
					$this->design->assign('message_success', 'updated');
				}
				// Удаление изображения
				if($this->request->post('delete_image'))
				{
					$this->banners->delete_image($banner->id);
				}
				// Загрузка изображения
				$image = $this->request->files('image');
				if(!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions))
				{
					$this->banners->delete_image($banner->id);
					move_uploaded_file($image['tmp_name'], $this->config->root_dir.$this->config->banners_dir.$image['name']);
					$this->banners->update($banner->id, array('image'=>$image['name']));
				}
				$banner = $this->banners->get($banner->id);
			}



		}
		else
		{
			$banner->id = $this->request->get('id', 'integer');
			$banner = $this->banners->get($banner->id);
		}

        $this->design->assign('types', $this->banners->types);
        $this->design->assign('typesNames', $this->banners->typesNames);

		$this->design->assign('banner', $banner);
		return $this->design->fetch('banner.tpl');
	}
}
